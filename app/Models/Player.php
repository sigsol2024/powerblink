<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Player extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'registration_id',
        'user_id',
        'guardian_id',
        'program_id',
        'season_id',
        'player_code',
        'photo_media_id',
        'name',
        'date_of_birth',
        'nationality',
        'primary_position',
        'secondary_position',
        'years_experience',
        'technical_strengths',
        'allergies',
        'medical_history',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'years_experience' => 'integer',
        ];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function photo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'photo_media_id');
    }

    public function sessionAttendance(): HasMany
    {
        return $this->hasMany(SessionAttendance::class);
    }

    public function performanceReports(): HasMany
    {
        return $this->hasMany(PerformanceReport::class);
    }

    public function playerDocuments(): HasMany
    {
        return $this->hasMany(PlayerDocument::class);
    }

    public function academyPayments(): HasMany
    {
        return $this->hasMany(AcademyPayment::class);
    }

    public function tournamentSquads(): HasMany
    {
        return $this->hasMany(TournamentSquad::class);
    }

    public function registrationPayments(): HasMany
    {
        return $this->hasMany(RegistrationPayment::class);
    }

    public function installmentPlans(): HasMany
    {
        return $this->hasMany(InstallmentPlan::class);
    }
}
