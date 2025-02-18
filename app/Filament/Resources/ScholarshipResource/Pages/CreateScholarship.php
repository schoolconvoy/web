<?php

namespace App\Filament\Resources\ScholarshipResource\Pages;

use App\Filament\Resources\ScholarshipResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateScholarship extends CreateRecord
{
    protected static string $resource = ScholarshipResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $data['created_by'] = auth()->user()->fullname;
        return static::getModel()::create($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
