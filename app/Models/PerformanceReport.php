<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceReport extends Model
{
    protected $fillable = [
        'season_id',
        'player_id',
        'coach_id',
        'passing',
        'dribbling',
        'speed',
        'fitness',
        'discipline',
        'teamwork',
        'overall_score',
        'comments',
        'reported_at',
    ];

    protected function casts(): array
    {
        return [
            'passing' => 'integer',
            'dribbling' => 'integer',
            'speed' => 'integer',
            'fitness' => 'integer',
            'discipline' => 'integer',
            'teamwork' => 'integer',
            'overall_score' => 'decimal:2',
            'reported_at' => 'datetime',
        ];
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class);
    }
}
