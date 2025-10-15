<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\TranslationRepositoryInterface;
use App\Repositories\TranslationRepository;
use App\Repositories\Contracts\AuthenticationRepositoryInterface;
use App\Repositories\AuthenticationRepository;
use App\Repositories\Contracts\LanguageRepositoryInterface;
use App\Repositories\LanguageRepository;
use App\Repositories\Contracts\TagRepositoryInterface;
use App\Repositories\TagRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TranslationRepositoryInterface::class, TranslationRepository::class);
        $this->app->bind(AuthenticationRepositoryInterface::class, AuthenticationRepository::class);
        $this->app->bind(LanguageRepositoryInterface::class, LanguageRepository::class);
        $this->app->bind(TagRepositoryInterface::class, TagRepository::class);
    }

    public function boot(): void
    {
    }
}
