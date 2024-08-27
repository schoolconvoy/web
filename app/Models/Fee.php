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

    protected $appends = ['final_amount'];

    public function classes()
    {
        return $this->belongsToMany(Classes::class, 'classes_fee');
    }

    public function category()
    {
        return $this->belongsTo(FeeCategory::class, 'fee_category', 'id');
    }

    public function payments()
    {
        return $this->belongsToMany(Fee::class, 'fee_payments');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'fee_student', 'fee_id', 'student_id')
                    ->withTimestamps();
    }

    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'discount_fee');
    }

    public function getFinalAmountAttribute()
    {
        // Calculate the fee with the associated discount
        $discount = $this->discounts->where('end_date', '>=', now())->first();

        $discountedPercentage = $discount->percentage ?? 0;

        $discountedAmount = $this->amount - ($this->amount * $discountedPercentage / 100);

        return $discountedAmount;
    }

    public function reminders()
    {
        return $this->hasMany(PaymentReminder::class);
    }
}
