<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentReminder extends Model
{
    use HasFactory;

    public function fees()
    {
        return $this->hasMany(Fee::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function sentBy()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
