<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SiteTrafficEvent extends Model
{
    protected $fillable = [
        'path',
        'route_name',
        'url',
        'method',
        'referrer_host',
        'referrer_url',
        'user_agent',
        'ip_hash',
        'session_id',
        'viewed_at',
    ];

    protected function casts(): array
    {
        return [
            'viewed_at' => 'datetime',
        ];
    }

    public function scopeBetweenDates(Builder $query, CarbonInterface $start, CarbonInterface $end): Builder
    {
        return $query->whereBetween('viewed_at', [$start, $end]);
    }

    public function scopeForPublicRoute(Builder $query): Builder
    {
        return $query->whereNotNull('route_name')
            ->where('route_name', 'not like', 'admin.%')
            ->where('route_name', 'not like', 'dashboard.%');
    }
}
