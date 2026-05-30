<?php

namespace App\Commands;

use App\Services\InventoryImportService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ProcessImports extends BaseCommand
{
    protected $group       = 'Imports';
    protected $name        = 'imports:process';
    protected $description = 'Processes pending inventory Excel import jobs in memory-safe chunks.';

    public function run(array $params)
    {
        $limit = isset($params[0]) ? max(1, (int) $params[0]) : 1;
        $maxSeconds = isset($params[1]) ? max(5, (int) $params[1]) : 25;

        $service = new InventoryImportService();
        $results = $service->processNextPending($limit, $maxSeconds);

        if (empty($results)) {
            CLI::write('No pending inventory import jobs found.', 'yellow');
            return;
        }

        foreach ($results as $result) {
            CLI::write(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'green');
        }
    }
}
