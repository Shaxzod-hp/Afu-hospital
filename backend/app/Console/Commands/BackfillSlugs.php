<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Models\StatsionarPackage;
use App\Models\Surgery;
use App\Models\Service;

class BackfillSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backfill-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill missing slugs for StatsionarPackage, Surgery, and Service';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting slug backfill...');

        $this->backfillModel(StatsionarPackage::class);
        $this->backfillModel(Surgery::class);
        $this->backfillModel(Service::class);

        $this->info('Backfill completed successfully.');
    }

    private function backfillModel($modelClass)
    {
        $records = $modelClass::whereNull('slug')->orWhere('slug', '')->get();
        $count = $records->count();

        if ($count === 0) {
            $this->info("No missing slugs for " . class_basename($modelClass));
            return;
        }

        $this->info("Backfilling {$count} records for " . class_basename($modelClass) . "...");

        foreach ($records as $record) {
            // StatsionarPackage doesn't have 'title', mostly 'name'.
            $baseName = $record->name ?? $record->title ?? 'item-' . $record->id;
            $slug = Str::slug($baseName);
            $originalSlug = $slug;
            $counter = 2;

            while ($modelClass::where('slug', $slug)->where('id', '!=', $record->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $record->slug = $slug;
            $record->save();
        }
    }
}
