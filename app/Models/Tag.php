<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function translations(): BelongsToMany
    {
        return $this->belongsToMany(Translation::class, 'tag_translation');
    }

    /**
     * Scope to filter by name (partial match)
     */
    public function scopeByName($query, ?string $name = null)
    {
        return $query->when($name, function ($q) use ($name) {
            return $q->where('name', 'like', '%' . $name . '%');
        });
    }
}
