<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\SessionTermSchoolScope;

// #[ScopedBy([SessionTermSchoolScope::class])]
class Fee extends BaseModel
{
    use HasFactory;

    // protected $appends = ['final_amount'];

    public function category()
    {
        return $this->belongsTo(FeeCategory::class, 'fee_category', 'id');
    }

    public function payments()
    {
        return $this->belongsToMany(Fee::class, 'fee_payments');
    }

    // A fee can belong to many students.
    public function students()
    {
        return $this->belongsToMany(User::class, 'discount_student_fee', 'fee_id', 'student_id')
                    ->using(DiscountStudentFee::class)
                    ->withPivot('discount_id')
                    ->withTimestamps();
    }

    /**
     * Calculates the total amount of the fee, given a discount percentage.
     */
    public function getTotal($percentageDiscount)
    {
        return $this->amount - ($this->amount * $percentageDiscount / 100);
    }

    // public function getFinalAmountAttribute()
    // {
    //     // Calculate the fee with the associated discount
    //     $discount = $this->discounts->where('end_date', '>=', now())->first();

    //     $discountedPercentage = $discount->percentage ?? 0;

    //     $discountedAmount = $this->amount - ($this->amount * $discountedPercentage / 100);

    //     return $discountedAmount;
    // }

    public static function getOverallAmountWithDiscounts($user)
    {
        // Get all fees for this user, including the discount associated with each fee
        $fees = $user->fees;
        // Apply discount to each fee
        $discountedAmounts = $fees->map(function ($fee) {
            $discount = $fee->discounts->where('end_date', '>=', now())->first();
            $discountedPercentage = $discount->percentage ?? 0;
            $discountedAmount = $fee->amount - ($fee->amount * $discountedPercentage / 100);
            return $discountedAmount;
        });

        return $discountedAmounts->sum();
    }

    public function reminders()
    {
        return $this->hasMany(PaymentReminder::class);
    }
}
