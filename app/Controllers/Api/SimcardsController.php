<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\SimcardModel;
use App\Models\IpWhitelistModel;

class SimcardsController extends BaseController
{
    /**
     * POST /api/simcards/import
     *
     * Accepts JSON or form-encoded body:
     *   - simcards: array of { number, price }
     *   OR
     *   - number: string, price: int  (single record)
     *
     * Only IPs registered in api_ip_whitelist table are allowed.
     */
    public function import()
    {
        // ── IP Whitelist Check ─────────────────────────────────────────────
        $clientIp = $this->request->getIPAddress();
        $whitelistModel = new IpWhitelistModel();

        if (!$whitelistModel->isAllowed($clientIp)) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Access denied. Your IP is not whitelisted.',
                    'ip'      => $clientIp,
                ]);
        }

        // ── Parse Input ───────────────────────────────────────────────────
        // Support both JSON body and form-encoded POST
        $contentType = $this->request->getHeaderLine('Content-Type');
        if (str_contains($contentType, 'application/json')) {
            $body = $this->request->getJSON(true);
        } else {
            $body = $this->request->getPost();
        }

        // Normalize: support single record OR array under "simcards" key
        if (isset($body['simcards']) && is_array($body['simcards'])) {
            $records = $body['simcards'];
        } elseif (isset($body['number'])) {
            $records = [['number' => $body['number'], 'price' => $body['price'] ?? 0]];
        } else {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Invalid payload. Provide "number" and "price", or a "simcards" array.',
                ]);
        }

        // ── Process Records ───────────────────────────────────────────────
        $model        = new SimcardModel();
        $inserted     = 0;
        $skipped      = 0;
        $errors       = [];

        foreach ($records as $index => $row) {
            $number = trim((string)($row['number'] ?? ''));
            $price  = (int) str_replace(',', '', (string)($row['price'] ?? 0));

            if (empty($number)) {
                $errors[] = "Row " . ($index + 1) . ": empty number, skipped.";
                $skipped++;
                continue;
            }

            // Normalize: 10-digit number starting with 9 → prepend 0
            if (preg_match('/^9\d{9}$/', $number)) {
                $number = '0' . $number;
            }

            // Validate format: 11 digits starting with 09
            if (!preg_match('/^09\d{9}$/', $number)) {
                $errors[] = "Row " . ($index + 1) . ": invalid number format ($number), skipped.";
                $skipped++;
                continue;
            }

            // Skip duplicate
            if ($model->where('number', $number)->countAllResults() > 0) {
                $skipped++;
                continue;
            }

            // Insert
            $result = $model->insert([
                'number' => $number,
                'price'  => $price,
                'status' => 'free',
                'added_by' => 'api',
            ]);

            if ($result !== false) {
                $inserted++;
            } else {
                $errors[] = "Row " . ($index + 1) . ": validation failed for ($number): "
                    . implode(', ', $model->errors());
                $skipped++;
            }
        }

        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'status'   => 'ok',
                'inserted' => $inserted,
                'skipped'  => $skipped,
                'errors'   => $errors,
            ]);
    }
}
