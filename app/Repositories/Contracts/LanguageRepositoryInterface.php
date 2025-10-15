<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface LanguageRepositoryInterface
{
    public function getAllLanguages(): Collection;
}
