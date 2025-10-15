<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Contracts\TranslationRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Database\QueryException;

/**
 * @group Translations
 * 
 * Translation management endpoints for creating, updating, viewing, and searching translations.
 */
class TranslationController extends Controller
{
    private TranslationRepositoryInterface $translations;

    public function __construct(TranslationRepositoryInterface $translations)
    {
        $this->translations = $translations;
    }

    /**
     * List Translations
     * 
     * Get a paginated list of translations with optional filtering.
     * 
     * @queryParam locale string Filter by locale code. Example: en
     * @queryParam key string Filter by translation key (partial match). Example: welcome
     * @queryParam content string Filter by content (partial match). Example: hello
     * @queryParam tags array Filter by tag IDs. Example: [1,2,3]
     * @queryParam tag_names array Filter by tag names. Example: ["mobile","web"]
     * @queryParam per_page integer Number of items per page. Example: 50
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['locale', 'key', 'content', 'tags', 'tag_names']);
            $perPage = (int) $request->get('per_page', 50);

            $items = $this->translations->getTranslations($filters, $perPage);

            return response()->json([
                'status' => true,
                'message' => 'Translations fetched successfully',
                'data' => $items
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching translations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Create Translation
     * 
     * Create a new translation entry.
     * 
     * @bodyParam key string required Translation key. Example: en.welcome-message
     * @bodyParam content string required Translation content. Example: Welcome to our application
     * @bodyParam locale string required Locale code. Example: en
     * @bodyParam language_id integer Language ID (optional). Example: 1
     * @bodyParam tag string Tag name. Example: web
     * @bodyParam name string Translation name. Example: Welcome Message
     * @bodyParam tags array Array of tag IDs. Example: [1,2]
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'content' => 'required|string',
                'locale' => 'required|string|max:10',
                'language_id' => 'nullable|exists:languages,id',
                'tags' => 'array',
                'tags.*' => 'integer|exists:tags,id',
                'name' => 'required|string|max:255',
            ]);

            $translation = $this->translations->createTranslation($validated);

            return response()->json([
                'status' => true,
                'message' => 'Translation created successfully',
                'data' => $translation
            ], 201);

        } catch(\PDOException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error creating translation',
                'error' => 'Duplicate entry for key and locale, try changing the name'
            ], 500);

        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Error creating translation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display Translation
     * 
     * Display an existing translation entry.
     * 
     * @param string $id Translation ID
     * @response 200 scenario="success" {
     *   "message": "Translation fetched successfully"
     * }
     * @response 404 scenario="not found" {
     *   "message": "Translation not found"
     * }
     */
    public function show(string $id): JsonResponse
    {
        try {
            $translation = $this->translations->getTranslation((int) $id);

            if (!$translation) {
                return response()->json([
                    'status' => false,
                    'message' => 'Translation not found',
                    'error' => 'Not found'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Translation fetched successfully',
                'data' => $translation
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching translation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update Translation
     * 
     * Update an existing translation entry.
     * 
     * @bodyParam content string Translation content. Example: Welcome to our application
     * @bodyParam locale string Locale code. Example: en
     * @bodyParam language_id integer Language ID (optional). Example: 1
     * @bodyParam tags array Array of tag IDs. Example: [1,2]
     * @bodyParam name string Translation name. Example: Welcome Message
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'content' => 'sometimes|string',
                'locale' => 'sometimes|string|max:80',
                'language_id' => 'sometimes|exists:languages,id',
                'tags' => 'sometimes|array',
                'tags.*' => 'integer|exists:tags,id',
                'name' => 'sometimes|string|max:255',
            ]);

            $translation = $this->translations->updateTranslation((int) $id, $validated);

            if (!$translation) {
                return response()->json([
                    'status' => false,
                    'message' => 'Translation not found',
                    'error' => 'Not found'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Translation updated successfully',
                'data' => $translation
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error updating translation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove Translation
     * 
     * Remove an existing translation entry.
     * 
     * @param string $id Translation ID
     * @response 200 scenario="success" {
     *   "message": "Translation deleted successfully"
     * }
     * @response 404 scenario="not found" {
     *   "message": "Translation not found"
     * }
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $ok = $this->translations->destroyTranslation((int) $id);

            if (!$ok) {
                return response()->json([
                    'status' => false,
                    'message' => 'Translation not found',
                    'error' => 'Not found'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Translation deleted successfully',
                'data' => ['deleted' => true]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error deleting translation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search Translations
     * 
     * Search translations with various filters.
     * 
     * @queryParam locale string Filter by locale code. Example: en
     * @queryParam key string Filter by translation key (partial match). Example: welcome
     * @queryParam content string Filter by content (partial match). Example: hello
     * @queryParam tags array Filter by tag IDs. Example: [1,2,3]
     * @queryParam tag_names array Filter by tag names. Example: ["mobile","web"]
     * @queryParam per_page integer Number of items per page. Example: 50
     */
    public function searchTranslations(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['locale', 'key', 'content', 'tags', 'tag_names', 'page']);
            $perPage = (int) $request->get('per_page', 50);

            $items = $this->translations->getTranslations($filters, $perPage);

            return response()->json([
                'status' => true,
                'message' => 'Translations searched successfully',
                'data' => $items
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error searching translations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export Translations
     * 
     * Export translations for a specific locale as JSON for frontend applications.
     * 
     * @queryParam locale string required Locale code to export. Example: en
     * 
     * @response 200 scenario="success" {
     *   "en.welcome-message": "Welcome to our application",
     *   "en.user-profile": "User Profile",
     *   "en.auth-login": "Login"
     * }
     */
    public function export(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'locale' => 'required|string|max:80',
            ]);

            $data = $this->translations->exportByLocale($request->get('locale'));

            return response()->json([
                'status' => true,
                'message' => 'Translations exported successfully',
                'data' => $data
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error exporting translations',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
