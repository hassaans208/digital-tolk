<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Translation;
use App\Models\Tag;
use App\Models\Language;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $count = 100000;
        $batch = 5000;

        $languages = Language::pluck('id', 'code');
        if ($languages->isEmpty()) {
            $this->command->warn('No languages found. Creating default EN language.');
            $en = Language::create(['code' => 'en', 'name' => 'English']);
            $languages = collect([$en->code => $en->id]);
        }

        // Ensure tags exist (only mobile, web, desktop)
        $tagNames = ['mobile', 'web', 'desktop'];
        foreach ($tagNames as $name) {
            Tag::firstOrCreate(['name' => $name]);
        }
        $tagIds = Tag::pluck('id')->all();

        $faker = \Faker\Factory::create();
        $inserted = 0;
        $counter = 0; // Add counter for uniqueness

        while ($inserted < $count) {
            $chunk = min($batch, $count - $inserted);
            $rows = [];
            $now = now();
            $langCodes = $languages->keys()->all();

            for ($i = 0; $i < $chunk; $i++) {
                $code = $langCodes[array_rand($langCodes)];
                $tag = $faker->randomElement($tagNames);
                $random = Str::lower(preg_replace('/[^a-z]/', '', Str::random(6)));
                $name = $faker->words(2, true) . ' ' . $random . ' ' . $counter;
                $key = $code . '.' . strtolower(str_replace(' ', '_', $name));
                
                $rows[] = [
                    'key' => $key,
                    'content' => $faker->sentence(),
                    'locale' => $code,
                    'language_id' => $languages[$code],
                    'tag' => $tag,
                    'name' => $name,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $counter++;
            }

            DB::table('translations')->insert($rows);

            // Attach tags randomly in batches for performance
            $newIds = Translation::orderByDesc('id')->limit($chunk)->pluck('id')->all();
            $pivotRows = [];
            foreach ($newIds as $tid) {
                $num = rand(1, 3);
                $picked = (array) array_rand(array_flip($tagIds), $num);
                foreach ($picked as $tagId) {
                    $pivotRows[] = [
                        'translation_id' => $tid,
                        'tag_id' => $tagId,
                    ];
                }
            }
            DB::table('tag_translation')->insertOrIgnore($pivotRows);

            $inserted += $chunk;
        }

        $this->command->info('Translation seeding done.');
    }
}
