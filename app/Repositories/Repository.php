<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class Repository
{
    protected $model;

    public function  __construct(Model | null $model = null)
    {
        $this->model = $model;
    }
}