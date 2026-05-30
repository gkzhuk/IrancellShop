<?php

namespace App\Services;

use App\Models\ExcelImportJobModel;
use App\Models\InventorySettingModel;
use CodeIgniter\Database\BaseConnection;
use Config\Database;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class InventoryImportService
{
    private BaseConnection $db;
    private ExcelImportJobModel $jobModel;
    private InventorySettingModel $settingModel;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? Database::connect();
        $this->jobModel = new ExcelImportJobModel();
        $this->settingModel = new InventorySettingModel();
    }

    public function processNextPending(int $limit = 1, int $maxSeconds = 25): array
    {
        $jobs = $this->jobModel
            ->whereIn('status', ['pending', 'processing'])
            ->orderBy('id', 'ASC')
            ->findAll($limit);

        $results = [];
        $deadline = time() + $maxSeconds;

        foreach ($jobs as $job) {
            if (time() >= $deadline) {
                break;
            }

            $results[] = $this->processChunk((int) $job['id']);
        }

        return $results;
    }

    public function processChunk(int $jobId): array
    {
        $job = $this->jobModel->find($jobId);

        if (!$job) {
            return ['status' => 'missing', 'message' => 'Import job not found.'];
        }

        if ($job['status'] === 'completed') {
            return ['status' => 'completed', 'message' => 'Import job already completed.'];
        }

        $filePath = (string) $job['file_path'];
        if (!is_file($filePath)) {
            $this->appendError($job, 'Import file not found: ' . $filePath, true);
            return ['status' => 'failed', 'message' => 'Import file not found.'];
        }

        $startedAt = $job['started_at'] ?: date('Y-m-d H:i:s');
        $this->jobModel->update($jobId, [
            'status'     => 'processing',
            'started_at' => $startedAt,
        ]);

        $chunkSize = max(50, min(1000, (int) ($job['chunk_size'] ?? 300)));
        $startRow = max(2, (int) ($job['current_row'] ?? 2));
        $endRow = $startRow + $chunkSize - 1;

        try {
            $reader = IOFactory::createReaderForFile($filePath);
            $reader->setReadDataOnly(true);
            $reader->setReadFilter(new ChunkReadFilter($startRow, $endRow));
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = (int) $sheet->getHighestDataRow();
            $highestColumn = Coordinate::columnIndexFromString($sheet->getHighestDataColumn());
            $highestColumn = min($highestColumn, 4);

            if ((int) $job['total_rows'] === 0) {
                $this->jobModel->update($jobId, [
                    'total_rows' => max(0, $this->detectTotalRows($filePath) - 1),
                ]);
            }

            $inserted = 0;
            $updated = 0;
            $failed = 0;
            $errors = [];
            $lastTouchedRow = $startRow - 1;

            for ($rowIndex = $startRow; $rowIndex <= $endRow; $rowIndex++) {
                $row = [];
                for ($column = 1; $column <= $highestColumn; $column++) {
                    $row[] = $sheet->getCell(Coordinate::stringFromColumnIndex($column) . $rowIndex)->getValue();
                }

                if ($this->isEmptyRow($row)) {
                    if ($rowIndex > $highestRow) {
                        break;
                    }
                    $lastTouchedRow = $rowIndex;
                    continue;
                }

                $lastTouchedRow = $rowIndex;
                $result = $this->upsertRow($row, $rowIndex);

                if ($result['status'] === 'inserted') {
                    $inserted++;
                } elseif ($result['status'] === 'updated') {
                    $updated++;
                } else {
                    $failed++;
                    $errors[] = $result['message'];
                }
            }

            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet, $sheet);

            $nextRow = $lastTouchedRow + 1;
            $totalRows = (int) ($this->jobModel->find($jobId)['total_rows'] ?? 0);
            $completed = $lastTouchedRow < $startRow || ($totalRows > 0 && ($nextRow - 2) >= $totalRows);

            $freshJob = $this->jobModel->find($jobId);
            $errorLog = $this->mergeErrors((string) ($freshJob['error_log'] ?? ''), $errors);

            $update = [
                'processed_rows'    => (int) $freshJob['processed_rows'] + max(0, $lastTouchedRow - $startRow + 1),
                'inserted_rows'     => (int) $freshJob['inserted_rows'] + $inserted,
                'updated_rows'      => (int) $freshJob['updated_rows'] + $updated,
                'failed_rows'       => (int) $freshJob['failed_rows'] + $failed,
                'current_row'       => $nextRow,
                'error_log'         => $errorLog,
                'last_processed_at' => date('Y-m-d H:i:s'),
            ];

            if ($completed) {
                $update['status'] = 'completed';
                $update['completed_at'] = date('Y-m-d H:i:s');
            } else {
                $update['status'] = 'processing';
            }

            $this->jobModel->update($jobId, $update);

            log_message('info', sprintf(
                'Inventory import job %d chunk processed: inserted=%d updated=%d failed=%d next_row=%d status=%s',
                $jobId,
                $inserted,
                $updated,
                $failed,
                $nextRow,
                $update['status']
            ));

            return [
                'status'   => $update['status'],
                'inserted' => $inserted,
                'updated'  => $updated,
                'failed'   => $failed,
                'next_row' => $nextRow,
            ];
        } catch (\Throwable $e) {
            $this->appendError($job, 'Fatal chunk error: ' . $e->getMessage(), true);
            log_message('error', 'Inventory import job ' . $jobId . ' failed: ' . $e->getMessage());

            return ['status' => 'failed', 'message' => $e->getMessage()];
        }
    }

    private function upsertRow(array $row, int $rowIndex): array
    {
        $number = $this->normalizeNumber((string) ($row[0] ?? ''));
        $price = $this->parseInteger($row[1] ?? 0);
        $discountPercent = $this->parseDiscountPercent($row[2] ?? null);
        $rowDiscountEnd = $this->parseDateTime($row[3] ?? null);

        if ($number === null) {
            return ['status' => 'failed', 'message' => 'Row ' . $rowIndex . ': invalid SIM number.'];
        }

        if ($price <= 0) {
            return ['status' => 'failed', 'message' => 'Row ' . $rowIndex . ': invalid price for ' . $number . '.'];
        }

        if ($discountPercent < 0 || $discountPercent > 100) {
            return ['status' => 'failed', 'message' => 'Row ' . $rowIndex . ': invalid discount percent for ' . $number . '.'];
        }

        $discountSettings = $this->settingModel->getDiscountSettings();
        $discountStart = null;
        $discountEnd = null;

        if ($discountPercent > 0) {
            if ($rowDiscountEnd !== null) {
                $discountEnd = $rowDiscountEnd;
            } else {
                $discountStart = $discountSettings['global_discount_start'];
                $discountEnd = $discountSettings['global_discount_end'];
            }
        }

        $discountEnabled = $discountSettings['enable_discounts'] && $discountPercent > 0;
        $finalPrice = $discountEnabled ? (int) round($price * (100 - $discountPercent) / 100) : $price;

        $data = [
            'number'              => $number,
            'price'               => $finalPrice,
            'original_price'      => $price,
            'discount_percent'    => $discountPercent,
            'final_price'         => $finalPrice,
            'discount_starts_at'  => $discountStart,
            'discount_ends_at'    => $discountEnd,
            'discount_enabled'    => $discountEnabled ? 1 : 0,
            'added_by'            => 'excel_import',
            'updated_at'          => date('Y-m-d H:i:s'),
        ];

        try {
            $existing = $this->db->table('simcards')->select('id')->where('number', $number)->get()->getRowArray();

            if ($existing) {
                unset($data['number']);
                $this->db->table('simcards')->where('id', $existing['id'])->update($data);

                return ['status' => 'updated'];
            }

            $data['status'] = 'free';
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->table('simcards')->insert($data);

            return ['status' => 'inserted'];
        } catch (\Throwable $e) {
            return ['status' => 'failed', 'message' => 'Row ' . $rowIndex . ': DB error for ' . $number . ' - ' . $e->getMessage()];
        }
    }

    private function detectTotalRows(string $filePath): int
    {
        $reader = IOFactory::createReaderForFile($filePath);
        $worksheetInfo = $reader->listWorksheetInfo($filePath);

        return (int) ($worksheetInfo[0]['totalRows'] ?? 0);
    }

    private function normalizeNumber(string $number): ?string
    {
        $number = preg_replace('/\D+/', '', trim($number));

        if (preg_match('/^9\d{9}$/', $number)) {
            $number = '0' . $number;
        }

        return preg_match('/^09\d{9}$/', $number) ? $number : null;
    }

    private function parseInteger($value): int
    {
        return (int) str_replace([',', '٬', ' '], '', (string) $value);
    }

    private function parseDiscountPercent($value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        return (float) str_replace(['%', '٫', ','], ['', '.', '.'], (string) $value);
    }

    private function parseDateTime($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d H:i:s');
            } catch (\Throwable $e) {
                return null;
            }
        }

        $timestamp = strtotime((string) $value);
        if ($timestamp === false) {
            return null;
        }

        return date('Y-m-d H:i:s', $timestamp);
    }

    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $cell) {
            if (trim((string) $cell) !== '') {
                return false;
            }
        }

        return true;
    }

    private function appendError(array $job, string $message, bool $markFailed = false): void
    {
        $errorLog = $this->mergeErrors((string) ($job['error_log'] ?? ''), [$message]);
        $update = ['error_log' => $errorLog];

        if ($markFailed) {
            $update['status'] = 'failed';
            $update['completed_at'] = date('Y-m-d H:i:s');
        }

        $this->jobModel->update((int) $job['id'], $update);
    }

    private function mergeErrors(string $currentLog, array $newErrors): string
    {
        if (empty($newErrors)) {
            return $currentLog;
        }

        $lines = $currentLog === '' ? [] : explode("\n", $currentLog);
        foreach ($newErrors as $error) {
            $lines[] = '[' . date('Y-m-d H:i:s') . '] ' . $error;
        }

        return implode("\n", array_slice($lines, -200));
    }
}

class ChunkReadFilter implements IReadFilter
{
    private int $startRow;
    private int $endRow;

    public function __construct(int $startRow, int $endRow)
    {
        $this->startRow = $startRow;
        $this->endRow = $endRow;
    }

    public function readCell($columnAddress, $row, $worksheetName = ''): bool
    {
        return $row === 1 || ($row >= $this->startRow && $row <= $this->endRow);
    }
}
