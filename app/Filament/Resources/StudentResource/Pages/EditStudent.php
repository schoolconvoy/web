<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Actions\Action;
use App\Models\Level;
use Filament\Forms\Set;
use STS\FilamentImpersonate\Pages\Actions\Impersonate;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;
    public static bool $hasInlineLabels = true;
    public array $review = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try {

            $class_assigned = $data['class_assigned'] ?? null;
            $data['admission_no'] = $data['admission_no'] ?? User::generateAdmissionNo();

            // Set the class
            if (!$class_assigned && auth()->user()->hasAnyRole([User::$TEACHER_ROLE]) && auth()->user()->teacher_class) {
                $this->record->class_id = auth()->user()->teacher_class->id;
            } else {
                $this->record->class_id = $class_assigned;
            }

            unset($data['class_assigned']);

            $record->update($data);

        } catch (\Throwable $th) {
            Log::debug('An error has occurred when saving user ' . print_r($th, true));
            throw $th;
        }

        return $record;
    }

    protected function getActions(): array
    {
        return [
            Impersonate::make()->record($this->getRecord())
        ];
    }
}
