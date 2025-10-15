<?php

namespace App\Repositories\Contracts;

use App\Models\Translation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TranslationRepositoryInterface {
    public function createTranslation(array $data): Translation;

    public function updateTranslation(int $id, array $data): ?Translation;

    public function getTranslations(array $filters = [], int $perPage = 50): LengthAwarePaginator;

    public function getTranslation(int $id): ?Translation;

    public function destroyTranslation(int $id): bool;

    public function exportByLocale(string $locale): array;
}