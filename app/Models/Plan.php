<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'price_monthly',
        'price_yearly',
        'stripe_monthly_plan_id',
        'stripe_yearly_plan_id',
        'paystack_monthly_plan_id',
        'paystack_yearly_plan_id',
        'trial_days',
        'max_schools',
        'max_students',
        'max_teachers',
        'max_parents',
        'features',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'features' => 'array',
    ];

    /**
     * Get the subscriptions for the plan.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the monthly price formatted.
     */
    public function getMonthlyPriceFormattedAttribute(): string
    {
        return '$' . number_format($this->price_monthly, 2);
    }

    /**
     * Get the yearly price formatted.
     */
    public function getYearlyPriceFormattedAttribute(): string
    {
        return '$' . number_format($this->price_yearly, 2);
    }

    /**
     * Get active plans.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
