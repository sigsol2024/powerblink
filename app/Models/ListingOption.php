<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ListingOption extends Model
{
    public const EXTERNAL_SOURCE_VPIC = 'vpic';

    protected $fillable = [
        'category_id',
        'parent_id',
        'value',
        'logo_path',
        'flag_emoji',
        'sort_order',
        'is_active',
        'external_source',
        'external_id',
        'last_synced_at',
        'source_payload',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'source_payload' => 'array',
            'last_synced_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ListingOptionCategory::class, 'category_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ListingOption::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ListingOption::class, 'parent_id')->orderBy('sort_order')->orderBy('value');
    }

    public function activeChildren(): HasMany
    {
        return $this->children()->where('is_active', true);
    }

    public static function normalizeMake(string $value): string
    {
        return mb_strtoupper(trim($value));
    }
}
