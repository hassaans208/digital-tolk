<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Language;
class SeedLanguages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed-languages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
 
    public function handle()
    {
        // Load CSV from storage/app
        $path = 'datasets/languages_dataset.csv';

        if (!Storage::exists($path)) {
            $this->error("CSV file not found at $path");
            return 1;
        }
        
        $csv = Storage::disk('local')->get($path);
        $lines = array_map('str_getcsv', explode("\n", trim($csv)));

        if (count($lines) < 2) {
            $this->error("CSV file does not contain enough data.");
            return 1;
        }

        $headers = array_map('trim', $lines[0]);

        $languageIndex = array_search('Language', $headers);
        $isoCodeIndex = array_search('ISO Code', $headers);

        if ($languageIndex === false || $isoCodeIndex === false) {
            $this->error("Required columns ('Language', 'ISO Code') not found.");
            return 1;
        }

        $languageStack = [];

        $data = collect($lines)
            ->skip(1)
            ->filter(fn($line) => isset($line[$languageIndex], $line[$isoCodeIndex]))
            ->map(function ($line) use ($languageIndex, $isoCodeIndex, $languageStack) {
                if (in_array($line[$languageIndex], $languageStack)) {
                    return null; // Skip duplicates
                }
                $languageStack[] = $line[$languageIndex];
                return [
                    'name' => $line[$languageIndex],
                    'code' => $line[$isoCodeIndex],
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            });

        $this->info("Extracted Language and ISO Code:");

        Language::upsert(
            $data->toArray(),
            ['code'], 
            ['name', 'updated_at']
        );

        $this->info("Language seeding completed.");

        return 0;
    }
}
