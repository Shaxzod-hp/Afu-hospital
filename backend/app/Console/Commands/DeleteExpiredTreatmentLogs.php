<?php

namespace App\Console\Commands;

use App\Models\TreatmentLog;
use Illuminate\Console\Command;

class DeleteExpiredTreatmentLogs extends Command
{
    protected $signature = 'treatment-logs:cleanup';
    protected $description = 'Delete treatment log entries (and their photos) older than 24 hours';

    public function handle(): int
    {
        $count = 0;

        TreatmentLog::expired()->chunkById(100, function ($logs) use (&$count) {
            foreach ($logs as $log) {
                $log->deleteWithPhotos();
                $count++;
            }
        });

        $this->info("Deleted {$count} expired treatment log(s).");

        return Command::SUCCESS;
    }
}
