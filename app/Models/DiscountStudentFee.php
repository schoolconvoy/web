<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class DiscountStudentFee extends Pivot
{
    // Specify the pivot table name (if not the default snake_case of the model name)
    protected $table = 'discount_student_fee';

    // Allow mass assignment on these pivot fields
    protected $fillable = [
        'discount_id',
        'student_id',
        'fee_id',
    ];

    /**
     * Get the discount associated with this pivot record.
     */
    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    /**
     * Get the fee associated with this pivot record.
     */
    public function fee()
    {
        return $this->belongsTo(Fee::class);
    }

    /**
     * Get the student (user) associated with this pivot record.
     */
    public function student()
    {
        // Assuming that your student records are stored in the users table.
        return $this->belongsTo(User::class, 'student_id');
    }

    // In DiscountStudentFee.php (the pivot model)
    public function getFinalAmountAttribute()
    {
        // Assume fee is accessible via a relationship or attribute on the pivot
        $feeAmount = $this->fee->amount;
        $discountPercentage = $this->discount->percentage ?? 0;

        return $feeAmount * (1 - $discountPercentage / 100);
    }
}
