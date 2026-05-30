<?php

namespace App\Models;

use CodeIgniter\Model;

class ExcelImportJobModel extends Model
{
    protected $table            = 'excel_import_jobs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'file_path',
        'original_filename',
        'status',
        'total_rows',
        'processed_rows',
        'inserted_rows',
        'updated_rows',
        'failed_rows',
        'current_row',
        'chunk_size',
        'error_log',
        'started_at',
        'completed_at',
        'last_processed_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'file_path' => 'required',
        'status'    => 'required|in_list[pending,processing,completed,failed]',
    ];
}
