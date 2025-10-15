<?php

namespace App\Repositories;

use App\Repositories\Contracts\AuthenticationRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthenticationRepository extends Repository implements AuthenticationRepositoryInterface
{
    public function getToken(array $credentials = []): ?string
    {
        $email = $credentials['email'] ?? null;
        $password = $credentials['password'] ?? null;

        throw_if(!$email || !$password, new \Exception('Email and password are required'));

        $user = User::where('email', $email)->first();
      
        throw_if(!$user || !Hash::check($password, $user->password), new \Exception('Invalid credentials'));

        $token = $user->createToken('api')->plainTextToken;

        return $token;
    }
}