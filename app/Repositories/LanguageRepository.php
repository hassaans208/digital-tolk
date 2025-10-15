<?php

namespace App\Repositories;

use App\Repositories\Contracts\LanguageRepositoryInterface;
use App\Models\Language;
use Illuminate\Database\Eloquent\Collection;
use App\Services\CacheService;

class LanguageRepository extends Repository implements LanguageRepositoryInterface
{
    private $cacheKey = 'languages_all';

    public function getAllLanguages(): Collection
    {
        $cacheService = new CacheService();
        $cache = $cacheService->get($this->cacheKey);

        if ($cache) {
            return $cache;
        }

        $data = Language::all();

        $cacheService->set($this->cacheKey, $data);

        return $data;
    }
}
