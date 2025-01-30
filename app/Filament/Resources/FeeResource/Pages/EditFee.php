<?php

namespace App\Filament\Resources\FeeResource\Pages;

use App\Filament\Resources\FeeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use App\Models\Classes;
use Illuminate\Support\Facades\Log;

class EditFee extends EditRecord
{
    protected static string $resource = FeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function handleRecordUpdate(Model $record, array $data): Model
    {
        unset($data['classes']);

        $record->update($data);

        return $record;
    }
}
