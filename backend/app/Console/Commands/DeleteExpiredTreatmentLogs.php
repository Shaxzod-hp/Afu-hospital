<?php

namespace App\Console\Commands;

use App\Models\TreatmentLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteExpiredTreatmentLogs extends Command
{
    protected $signature = 'treatment-logs:cleanup';
    protected $description = 'Delete treatment log entries (and their photos) older than 24 hours';

    public function handle(): int
    {
        $expired = TreatmentLog::where('created_at', '<', now()->subHours(24))->get();

        $count = 0;
        foreach ($expired as $log) {
            foreach (($log->photos ?? []) as $photo) {
                if (str_starts_with($photo, '/storage/')) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $photo));
                }
            }
            $log->delete();
            $count++;
        }

        $this->info("Deleted {$count} expired treatment log(s).");

        return Command::SUCCESS;
    }
}