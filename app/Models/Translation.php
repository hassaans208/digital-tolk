<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'content',
        'locale',
        'language_id',
        'tag',
        'name',
    ];

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'tag_translation');
    }

    /**
     * Scope to filter by locale
     */
    public function scopeByLocale($query, ?string $locale = null)
    {
        return $query->when($locale, function ($q) use ($locale) {
            return $q->where('locale', $locale);
        });
    }

    /**
     * Scope to filter by key (partial match)
     */
    public function scopeByKey($query, ?string $key = null)
    {
        return $query->when($key, function ($q) use ($key) {
            return $q->where('key', 'like', '%' . $key . '%');
        });
    }

    /**
     * Scope to filter by content (partial match)
     */
    public function scopeByContent($query, ?string $content = null)
    {
        return $query->when($content, function ($q) use ($content) {
            return $q->where('content', 'like', '%' . $content . '%');
        });
    }

    /**
     * Scope to filter by tag IDs
     */
    public function scopeByTagIds($query, ?array $tagIds = null)
    {
        return $query->when($tagIds && is_array($tagIds), function ($q) use ($tagIds) {
            return $q->whereHas('tags', function ($subQuery) use ($tagIds) {
                return $subQuery->whereIn('tags.id', $tagIds);
            });
        });
    }

    /**
     * Scope to filter by tag names
     */
    public function scopeByTagNames($query, ?array $tagNames = null)
    {
        return $query->when($tagNames && is_array($tagNames), function ($q) use ($tagNames) {
            return $q->whereHas('tags', function ($subQuery) use ($tagNames) {
                return $subQuery->whereIn('tags.name', $tagNames);
            });
        });
    }

    /**
     * Interact with the translation's key.
     */
    protected function key(): Attribute
    {
        return Attribute::make(
            get: function (string $value) {
                // If key is already set, return it
                if (! empty($value)) {
                    return $value;
                }

                // Generate key from locale and name
                $locale = $this->attributes['locale'] ?? 'en';
                $name = $this->attributes['name'] ?? 'translation_' . uniqid();
                $name = strtolower(str_replace(' ', '_', $name));
                $name = preg_replace('/[^a-zA-Z0-9_]/', '', $name);

                return $locale . '.' . $name;
            },
            set: function (string $value) {
                // If key is provided manually, use it
                if (! empty($value)) {
                    return $value;
                }

                // Auto-generate key from locale.name format
                $locale = $this->attributes['locale'] ?? 'en';
                $name = $this->attributes['name'] ?? 'translation_' . uniqid();
                $name = strtolower(str_replace(' ', '_', $name));
                $name = preg_replace('/[^a-zA-Z0-9_]/', '', $name);

                return $locale . '.' . $name;
            }
        );
    }
}
