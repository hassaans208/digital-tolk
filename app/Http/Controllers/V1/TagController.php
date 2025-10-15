<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\TagRepositoryInterface;
use Illuminate\Http\JsonResponse;

/**
 * @group Tags
 * 
 * Tag management endpoints for retrieving available tags.
 */
class TagController extends Controller
{
    private TagRepositoryInterface $tags;

    public function __construct(TagRepositoryInterface $tags)
    {
        $this->tags = $tags;
    }

    /**
     * List All Tags
     * 
     * Get all available tags without pagination.
     * 
     * @response 200 scenario="success" [
     *   {
     *     "id": 1,
     *     "name": "mobile",
     *     "created_at": "2025-01-15T10:30:00.000000Z",
     *     "updated_at": "2025-01-15T10:30:00.000000Z"
     *   },
     *   {
     *     "id": 2,
     *     "name": "web",
     *     "created_at": "2025-01-15T10:30:00.000000Z",
     *     "updated_at": "2025-01-15T10:30:00.000000Z"
     *   },
     *   {
     *     "id": 3,
     *     "name": "desktop",
     *     "created_at": "2025-01-15T10:30:00.000000Z",
     *     "updated_at": "2025-01-15T10:30:00.000000Z"
     *   }
     * ]
     */
    public function index(): JsonResponse
    {
        try {
            $tags = $this->tags->getAllTags();

            return response()->json([
                'status' => true,
                'message' => 'Tags fetched successfully',
                'data' => $tags
            ]);
        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Error fetching tags',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
