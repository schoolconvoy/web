<?php

namespace App\Livewire;

use App\Events\StudentPromoted;
use App\Models\Classes;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action as TableAction;
use App\Livewire\IRelationalEntityTable;
use App\Models\Subject;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class ClassGoogleClassroom extends IRelationalEntityTable
{
    public $classId;
    public $students = [];
    public $student = null;
    public $google_class_room_link = null;
    public $viewPath = 'livewire.class-google-classroom';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([
                    'sm' => 1,
                    'md' => 1,
                    'lg' => 1
                ])
                ->schema([
                    TextInput::make('google_class_room_link')
                        ->label('Add the Google Classroom Link here')
                        ->hintIcon('heroicon-o-information-circle')
                        ->hint('This is the link to the Google Classroom for this class')
                        ->required(),
                ])
            ]);
    }

    public function saveGoogleClassroom()
    {
        $class = Classes::find($this->classId);
        $class->google_class_room_link = $this->google_class_room_link;
        $class->save();

        return Notification::make()
                    ->success()
                    ->body('Google Classroom link saved successfully')
                    ->send();
    }

    public function mount($classId = null, $students = [])
    {
        $this->classId = $classId;
        $this->google_class_room_link = Classes::find($classId)->google_class_room_link;
    }
}
