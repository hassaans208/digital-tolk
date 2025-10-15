<?php

namespace App\Repositories;

use App\Repositories\Contracts\TagRepositoryInterface;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use App\Services\CacheService;

class TagRepository extends Repository implements TagRepositoryInterface
{
    private $cacheKey = 'tags_all';

    public function getAllTags(): Collection
    {
        $cacheService = new CacheService();
        $cache = $cacheService->get($this->cacheKey);

        if ($cache) {
            return $cache;
        }

        $data = Tag::all();

        $cacheService->set($this->cacheKey, $data);

        return $data;
    }
}
