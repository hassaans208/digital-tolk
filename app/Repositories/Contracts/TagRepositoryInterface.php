<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface TagRepositoryInterface
{
    public function getAllTags(): Collection;
}
