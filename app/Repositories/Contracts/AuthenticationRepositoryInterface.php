<?php

namespace App\Repositories\Contracts;

interface AuthenticationRepositoryInterface {
    public function getToken(array $credentials = []): ?string;
}