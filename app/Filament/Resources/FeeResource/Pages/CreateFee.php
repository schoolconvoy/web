<?php

namespace App\Filament\Resources\FeeResource\Pages;

use App\Filament\Resources\FeeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Models\Classes;

class CreateFee extends CreateRecord
{
    protected static string $resource = FeeResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // We don't necessarily want to store the classes in the fee table
        unset($data['classes']);

        return static::getModel()::create($data);
    }
}
