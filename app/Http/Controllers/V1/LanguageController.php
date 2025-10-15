<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\LanguageRepositoryInterface;
use Illuminate\Http\JsonResponse;

/**
 * @group Languages
 * 
 * Language management endpoints for retrieving available languages.
 */
class LanguageController extends Controller
{
    private LanguageRepositoryInterface $languages;

    public function __construct(LanguageRepositoryInterface $languages)
    {
        $this->languages = $languages;
    }

    /**
     * List All Languages
     * 
     * Get all available languages without pagination.
     * 
     * @response 200 scenario="success" [
     *   {
     *     "id": 1,
     *     "code": "en",
     *     "name": "English",
     *     "created_at": "2025-01-15T10:30:00.000000Z",
     *     "updated_at": "2025-01-15T10:30:00.000000Z"
     *   },
     *   {
     *     "id": 2,
     *     "code": "fr",
     *     "name": "French",
     *     "created_at": "2025-01-15T10:30:00.000000Z",
     *     "updated_at": "2025-01-15T10:30:00.000000Z"
     *   },
     *   {
     *     "id": 3,
     *     "code": "es",
     *     "name": "Spanish",
     *     "created_at": "2025-01-15T10:30:00.000000Z",
     *     "updated_at": "2025-01-15T10:30:00.000000Z"
     *   }
     * ]
     */
    public function index(): JsonResponse
    {
        try {
            $languages = $this->languages->getAllLanguages();

            return response()->json([
                'status' => true,
                'message' => 'Languages fetched successfully',
                'data' => $languages
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching languages',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
