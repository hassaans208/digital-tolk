<?php

namespace App\Repositories;

use App\Repositories\Contracts\TranslationRepositoryInterface;
use App\Models\Translation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Services\CacheService;

class TranslationRepository extends Repository implements TranslationRepositoryInterface
{

    private $searchCacheKeyPrefix = 'translations_search_';
    private $exportCacheKeyPrefix = 'translations_export_';

    public function createTranslation(array $data): Translation
    {
        $tags = $data['tags'] ?? [];
        unset($data['tags']);

        $data['key'] = '';

        $translation = Translation::create($data);

        if (! empty($tags)) {
            $translation->tags()->sync($tags);
        }

        return $translation->load('tags', 'language');
    }

    public function updateTranslation(int $id, array $data): ?Translation
    {
        $translation = Translation::find($id);

        if (! $translation) {
            return null;
        }

        $tags = $data['tags'] ?? null;
        unset($data['tags']);

        $data['key'] = '';

        $translation->update($data);

        if (! empty($tags)) {
            $translation->tags()->sync($tags);
        }
        return $translation->load('tags', 'language');
    }

    public function getTranslations(array $filters = [], int $perPage = 50): LengthAwarePaginator
    {
        $cacheKey = $this->searchCacheKey($filters, $perPage);

        $cacheService = new CacheService();
        $cache = $cacheService->get($cacheKey);

        if ($cache) {
            return $cache;
        }

        $data = Translation::query()
            ->with(['tags', 'language'])
            ->byLocale($filters['locale'] ?? null)
            ->byKey($filters['key'] ?? null)
            ->byContent($filters['content'] ?? null)
            ->byTagIds($filters['tags'] ?? null)
            ->byTagNames($filters['tag_names'] ?? null)
            ->paginate($perPage);

        $cacheService->set($cacheKey, $data);

        return $data;
    }

    public function getTranslation(int $id): ?Translation
    {
        return Translation::with(['tags', 'language'])->find($id);
    }

    public function destroyTranslation(int $id): bool
    {
        $translation = Translation::find($id);

        if (! $translation) {
            return false;
        }

        $locale = $translation->locale;
        $translation->delete();

        return true;
    }

    public function exportByLocale(string $locale): array
    {
        $cacheKey = $this->exportCacheKey($locale);

        $cacheService = new CacheService();
        $cache = $cacheService->get($cacheKey);

        if ($cache) {
            return $cache;
        }

        $data = Translation::query()
                ->select(['key', 'content'])
                ->where('locale', $locale)
                ->orderBy('key')
                ->get()
                ->mapWithKeys(fn($translation) => [$translation->key => $translation->content])
                ->toArray();

        $cacheService->set($cacheKey, $data);

        return $data;
    }

    private function exportCacheKey(string $locale): string
    {
        return $this->exportCacheKeyPrefix . $locale;
    }

    private function searchCacheKey(array $filters = [], int $perPage = 50): string
    {
        ksort($filters);

        $cacheKey = json_encode([$filters, $perPage, $filters['page'] ?? 1]);
        $cacheKey = md5($cacheKey);
        $cacheKey = $this->searchCacheKeyPrefix . $cacheKey;

        return $cacheKey;
    }
}