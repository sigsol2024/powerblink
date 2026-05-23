<?php

namespace App\Models;

use App\Support\ListingOptionNormalizer;
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

    /**
     * Canonical value as stored or compared for a category (make = uppercase, others = normalized + synonyms).
     */
    public static function normalizedValue(string $categorySlug, string $raw): string
    {
        if ($categorySlug === 'make') {
            return self::normalizeMake($raw);
        }

        return ListingOptionNormalizer::canonical($categorySlug, $raw);
    }

    /**
     * Case-insensitive comparison key for duplicate detection within a category + parent group.
     */
    public static function valueComparisonKey(string $categorySlug, string $raw): string
    {
        $normalized = self::normalizedValue($categorySlug, $raw);
        if ($normalized === '') {
            return '';
        }

        return $categorySlug === 'make'
            ? $normalized
            : mb_strtolower($normalized);
    }

    /**
     * Whether another option in the same category/parent group already uses this value (case-insensitive).
     */
    public static function duplicateExists(
        int $categoryId,
        string $categorySlug,
        string $raw,
        ?int $parentId = null,
        ?int $ignoreOptionId = null,
    ): bool {
        $key = self::valueComparisonKey($categorySlug, $raw);
        if ($key === '') {
            return false;
        }

        $query = static::query()->where('category_id', $categoryId);
        if ($parentId === null) {
            $query->whereNull('parent_id');
        } else {
            $query->where('parent_id', $parentId);
        }
        if ($ignoreOptionId !== null) {
            $query->where('id', '!=', $ignoreOptionId);
        }

        if ($categorySlug === 'make') {
            return $query->where(function ($q) use ($key) {
                $q->where('value', $key)
                    ->orWhereRaw('UPPER(TRIM(value)) = ?', [$key]);
            })->exists();
        }

        return $query->whereRaw('LOWER(TRIM(value)) = ?', [$key])->exists();
    }
}
