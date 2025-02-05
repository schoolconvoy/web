<?php

namespace App\Filament\Resources\DiscountResource\Pages;

use App\Filament\Resources\DiscountResource;
use App\Models\Discount;
use App\Models\DiscountStudentFee;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Models\Fee;

class CreateDiscount extends CreateRecord
{
    protected static string $resource = DiscountResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $data['created_by'] = auth()->user()->fullname;

        $fee_id = $data['fee_id'];
        $student_id = $data['student_id'];

        unset($data['fee_id'], $data['student_id']);

        $discount = static::getModel()::create($data);

        // Since any fee might be selected and it may not already have been attached to the student
        DiscountStudentFee::firstOrCreate(
            [
                'fee_id' => $fee_id,
                'student_id' => $student_id,
            ],
            [
                'discount_id' => $discount->id,
            ]
        );

        return $discount;
    }
}
