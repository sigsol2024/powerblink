<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'status',
        'year',
        'make_listing_option_id',
        'model_listing_option_id',
        'condition_listing_option_id',
        'body_type_listing_option_id',
        'transmission_listing_option_id',
        'fuel_type_listing_option_id',
        'drive_listing_option_id',
        'country_listing_option_id',
        'type_listing_option_id',
        'price',
        'msrp',
        'finance_price',
        'finance_interest_rate',
        'finance_term_months',
        'finance_down_payment',
        'finance_min_down_payment',
        'finance_term_min_months',
        'finance_term_max_months',
        'show_financing_calculator',
        'mileage',
        'city_mpg',
        'hwy_mpg',
        'engine_size',
        'engine_layout',
        'top_track_speed',
        'zero_to_sixty',
        'number_of_gears',
        'street_address',
        'contact_phone',
        'contact_email',
        'map_location',
        'features',
        'exterior_color',
        'interior_color',
        'vin',
        'video_url',
        'description',
        'overview',
        'tech_specs',
        'is_special',
        'submitted_at',
        'approved_at',
        'approved_by',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'features' => 'array',
            'tech_specs' => 'array',
            'is_special' => 'boolean',
            'show_financing_calculator' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Same rule as dashboard “Staff listing” vs vendor: owner is console staff. */
    public function isStaffListing(): bool
    {
        return $this->user?->hasRole('admin') ?? false;
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function images(): HasMany
    {
        return $this->hasMany(VehicleImage::class)->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(VehicleVariant::class)->where('is_active', true)->orderBy('id');
    }

    public function allVariants(): HasMany
    {
        return $this->hasMany(VehicleVariant::class)->orderBy('id');
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(VehicleInquiry::class);
    }

    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'vehicle_favorites')->withTimestamps();
    }

    public function makeOption(): BelongsTo
    {
        return $this->belongsTo(ListingOption::class, 'make_listing_option_id');
    }

    public function modelOption(): BelongsTo
    {
        return $this->belongsTo(ListingOption::class, 'model_listing_option_id');
    }

    public function conditionOption(): BelongsTo
    {
        return $this->belongsTo(ListingOption::class, 'condition_listing_option_id');
    }

    public function bodyTypeOption(): BelongsTo
    {
        return $this->belongsTo(ListingOption::class, 'body_type_listing_option_id');
    }

    public function transmissionOption(): BelongsTo
    {
        return $this->belongsTo(ListingOption::class, 'transmission_listing_option_id');
    }

    public function fuelTypeOption(): BelongsTo
    {
        return $this->belongsTo(ListingOption::class, 'fuel_type_listing_option_id');
    }

    public function driveOption(): BelongsTo
    {
        return $this->belongsTo(ListingOption::class, 'drive_listing_option_id');
    }

    public function countryOption(): BelongsTo
    {
        return $this->belongsTo(ListingOption::class, 'country_listing_option_id');
    }

    public function typeOption(): BelongsTo
    {
        return $this->belongsTo(ListingOption::class, 'type_listing_option_id');
    }
}
