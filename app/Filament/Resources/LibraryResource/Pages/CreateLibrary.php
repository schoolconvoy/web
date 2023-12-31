<?php

namespace App\Filament\Resources\LibraryResource\Pages;

use App\Filament\Resources\LibraryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class CreateLibrary extends CreateRecord
{
    protected static string $resource = LibraryResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $data['added_by'] = auth()->user()->id;

        Log::debug('Data is ' . print_r($data, true));

        return static::getModel()::create($data);
    }
}
