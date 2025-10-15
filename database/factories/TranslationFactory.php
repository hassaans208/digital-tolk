<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Language;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Translation>
 */
class TranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $language = Language::inRandomOrder()->first();
        if (!$language) {
            $language = Language::factory()->create([
                'code' => 'en',
                'name' => 'English',
            ]);
        }

        // Generate Laravel-style key: locale.name
        $tags = ['mobile', 'web', 'desktop'];
        $tag = $this->faker->randomElement($tags);
        $name = $this->faker->words(2, true) . ' ' . Str::lower(Str::random(6));
        $key = $language->code . '.' . strtolower(str_replace(' ', '_', $name));

        return [
            'key' => $key,
            'content' => $this->faker->sentence(),
            'language_id' => $language->id,
            'locale' => $language->code,
            'tag' => $tag,
            'name' => $name,
        ];
    }
}
