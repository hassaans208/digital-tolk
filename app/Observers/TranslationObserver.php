<?php

namespace App\Observers;

use App\Models\Translation;
use App\Services\CacheService;

class TranslationObserver
{
    private $searchCacheKeyPrefix = 'translations_search_';
    private $exportCacheKeyPrefix = 'translations_export_';

    public function created(Translation $translation): void
    {
        $this->forgetLocaleExport();
    }

    public function updated(Translation $translation): void
    {
        $this->forgetLocaleExport();
    }

    public function deleted(Translation $translation): void
    {
        $this->forgetLocaleExport();
    }

    private function forgetLocaleExport(): void
    {
        $searchCacheKeyPrefix = $this->searchCacheKeyPrefix;
        $exportCacheKeyPrefix = $this->exportCacheKeyPrefix;

        $cacheService = new CacheService();
        $cacheService->delete("$searchCacheKeyPrefix*");
        $cacheService->delete("$exportCacheKeyPrefix*");
    }
}
