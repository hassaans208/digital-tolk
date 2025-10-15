<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create some languages
        \App\Models\Language::factory()->create([
            'code' => 'en',
            'name' => 'English',
        ]);
        
        \App\Models\Language::factory()->create([
            'code' => 'fr',
            'name' => 'French',
        ]);

        \App\Models\Language::factory()->create([
            'code' => 'es',
            'name' => 'Spanish',
        ]);

        // Create some tags (only mobile, web, desktop)
        \App\Models\Tag::factory()->create(['name' => 'mobile']);
        \App\Models\Tag::factory()->create(['name' => 'web']);
        \App\Models\Tag::factory()->create(['name' => 'desktop']);

        Artisan::call('app:seed-languages');
        
        // Seed ~100k translations using TranslationSeeder
        $this->call(TranslationSeeder::class);

    }
}
