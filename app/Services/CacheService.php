<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

// CDN or in-memory caching service
class CacheService
{
    public function get($key)
    {
        return Cache::store('file')->get($key);
    }

    public function set($key, $value, $ttl = 3600)
    {
        Cache::store('file')->put($key, $value, $ttl);
    }

    public function delete($key)
    {
        Cache::store('file')->forget($key);
    }
}