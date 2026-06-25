<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Registration extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference_code',
        'season_id',
        'program_id',
        'guardian_id',
        'status',
        'payment_plan',
        'payment_token',
        'payment_token_expires_at',
        'payment_token_used_at',
        'player_name',
        'date_of_birth',
        'nationality',
        'primary_position',
        'secondary_position',
        'years_experience',
        'technical_strengths',
        'allergies',
        'medical_history',
        'fitness_certified',
        'profile_photo_media_id',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'approved_by',
        'approved_at',
        'rejected_reason',
        'rejected_at',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'years_experience' => 'integer',
            'fitness_certified' => 'boolean',
            'payment_token_expires_at' => 'datetime',
            'payment_token_used_at' => 'datetime',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'submitted_at' => 'datetime',
        ];
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class);
    }

    public function profilePhoto(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'profile_photo_media_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function player(): HasOne
    {
        return $this->hasOne(Player::class);
    }

    public function registrationPayments(): HasMany
    {
        return $this->hasMany(RegistrationPayment::class);
    }

    public function installmentPlans(): HasMany
    {
        return $this->hasMany(InstallmentPlan::class);
    }

    public function playerDocuments(): HasMany
    {
        return $this->hasMany(PlayerDocument::class);
    }
}
