<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Language;
use App\Models\Translation;
use App\Models\Tag;
use Laravel\Sanctum\Sanctum;

class TranslationExportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the export endpoint returns translations quickly for a large dataset.
     */
    public function test_export_endpoint_returns_fast_response_for_large_dataset(): void
    {
        // Arrange: Create test data
        $englishLanguage = $this->createEnglishLanguage();
        $this->createLargeTranslationDataset($englishLanguage, 2000);

        $authenticatedUser = $this->createAuthenticatedUser();

        // Act: Make the export request and measure time
        $startTime = microtime(true);
        $response = $this->getJson('/api/v1/translations/export?locale=en');
        $elapsedTime = (microtime(true) - $startTime) * 1000;

        // Assert: Response is fast and valid
        $response->assertOk();
        $this->assertLessThan(500, $elapsedTime, "Export took too long: {$elapsedTime}ms");
        $response->assertJsonStructure();
    }

    /**
     * Test that export returns correct JSON structure for frontend consumption.
     */
    public function test_export_returns_correct_json_structure(): void
    {
        // Arrange: Create test translations
        $englishLanguage = $this->createEnglishLanguage();
        $this->createTestTranslations($englishLanguage);

        $authenticatedUser = $this->createAuthenticatedUser();

        // Debug: Check what translations exist
        $translations = Translation::where('locale', 'en')->get();
        $this->assertGreaterThan(0, $translations->count(), 'No translations found for locale en');

        // Act: Export translations
        $response = $this->getJson('/api/v1/translations/export?locale=en');

        // Assert: Correct structure and content
        $response->assertOk();
        $response->assertJsonStructure([
            'status',
            'message', 
            'data'
        ]);
        
        $responseData = $response->json('data');
        
        // Debug: Check what we got
        $this->assertIsArray($responseData, 'Response data should be an array');
        $this->assertNotEmpty($responseData, 'Response data should not be empty');
        
        // Check that data contains key-value pairs (not nested structure)
        $this->assertIsString(array_keys($responseData)[0], 'Keys should be strings');
        $this->assertIsString(array_values($responseData)[0], 'Values should be strings');
        
    }

    /**
     * Test that export only returns translations for the specified locale.
     */
    public function test_export_filters_by_locale_correctly(): void
    {
        // Arrange: Create translations in multiple locales
        $englishLanguage = $this->createEnglishLanguage();
        $frenchLanguage = $this->createFrenchLanguage();
        
        $this->createEnglishTranslations($englishLanguage);
        $this->createFrenchTranslations($frenchLanguage);

        $authenticatedUser = $this->createAuthenticatedUser();

        // Act: Export only English translations
        $response = $this->getJson('/api/v1/translations/export?locale=en');

        // Assert: Only English translations are returned
        $response->assertOk();
        $responseData = $response->json('data');
        
        // All keys should start with 'en.'
        foreach (array_keys($responseData) as $key) {
            $this->assertStringStartsWith('en.', $key, "Key '{$key}' should start with 'en.'");
        }
    }

    /**
     * Test that export requires authentication.
     */
    public function test_export_requires_authentication(): void
    {
        // Arrange: Create test data without authentication
        $englishLanguage = $this->createEnglishLanguage();
        $this->createTestTranslations($englishLanguage);

        // Act: Try to export without authentication
        $response = $this->getJson('/api/v1/translations/export?locale=en');

        // Assert: Unauthorized response
        $response->assertUnauthorized();
    }

    /**
     * Test that export validates required locale parameter.
     */
    public function test_export_validates_required_locale_parameter(): void
    {
        // Arrange: Authenticated user
        $authenticatedUser = $this->createAuthenticatedUser();

        // Act: Export without locale parameter
        $response = $this->getJson('/api/v1/translations/export');

        // Assert: Validation error
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'locale'
            ]
        ]);
    }

    /**
     * Test that export handles empty locale gracefully.
     */
    public function test_export_handles_empty_locale_gracefully(): void
    {
        // Arrange: Authenticated user
        $authenticatedUser = $this->createAuthenticatedUser();

        // Act: Export with empty locale
        $response = $this->getJson('/api/v1/translations/export?locale=');

        // Assert: Validation error
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'locale'
            ]
        ]);
    }

    /**
     * Test that export returns empty array when no translations exist for locale.
     */
    public function test_export_returns_empty_array_for_non_existent_locale(): void
    {
        // Arrange: Authenticated user, no translations
        $authenticatedUser = $this->createAuthenticatedUser();

        // Act: Export for non-existent locale
        $response = $this->getJson('/api/v1/translations/export?locale=de');

        // Assert: Empty array returned
        $response->assertOk();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $responseData = $response->json('data');
        $this->assertEmpty($responseData);
    }

    /**
     * Test that export is cached for performance.
     */
    public function test_export_is_cached_for_performance(): void
    {
        // Arrange: Create test data
        $englishLanguage = $this->createEnglishLanguage();
        $this->createTestTranslations($englishLanguage);
        $authenticatedUser = $this->createAuthenticatedUser();

        // Act: Make two identical requests
        $firstResponse = $this->getJson('/api/v1/translations/export?locale=en');
        $secondResponse = $this->getJson('/api/v1/translations/export?locale=en');

        // Assert: Both responses are identical (cached)
        $firstResponse->assertOk();
        $secondResponse->assertOk();
        
        $firstData = $firstResponse->json('data');
        $secondData = $secondResponse->json('data');
        
        $this->assertEquals($firstData, $secondData);
    }

    // Helper methods for creating test data

    private function createEnglishLanguage(): Language
    {
        return Language::create([
            'code' => 'en',
            'name' => 'English',
        ]);
    }

    private function createFrenchLanguage(): Language
    {
        return Language::create([
            'code' => 'fr',
            'name' => 'French',
        ]);
    }

    private function createAuthenticatedUser(): User
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        return $user;
    }

    private function createLargeTranslationDataset(Language $language, int $count): void
    {
        Translation::factory()->count($count)->create([
            'locale' => $language->code,
            'language_id' => $language->id,
        ]);
    }

    private function createTestTranslations(Language $language): void
    {
        Translation::create([
            'content' => 'Welcome to our application',
            'locale' => $language->code,
            'language_id' => $language->id,
            'name' => 'welcome_message',
        ]);

        Translation::create([
            'content' => 'User Profile',
            'locale' => $language->code,
            'language_id' => $language->id,
            'name' => 'user_profile',
        ]);

        Translation::create([
            'content' => 'Login to your account',
            'locale' => $language->code,
            'language_id' => $language->id,
            'name' => 'auth_login',
        ]);
    }

    private function createEnglishTranslations(Language $language): void
    {
        Translation::create([
            'content' => 'Welcome to our application',
            'locale' => $language->code,
            'language_id' => $language->id,
            'name' => 'welcome_message',
        ]);

        Translation::create([
            'content' => 'User Profile',
            'locale' => $language->code,
            'language_id' => $language->id,
            'name' => 'user_profile',
        ]);
    }

    private function createFrenchTranslations(Language $language): void
    {
        Translation::create([
            'content' => 'Bienvenue dans notre application',
            'locale' => $language->code,
            'language_id' => $language->id,
            'name' => 'bienvenue_message',
        ]);
    }
}