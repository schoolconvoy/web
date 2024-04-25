<?php

namespace App\Filament\Resources\StaffResource\Pages;

use App\Filament\Resources\StaffResource;
use App\Models\Classes;
use App\Models\Level;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Notifications\Auth\ResetPassword;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Filament\Notifications\Actions\Action;

class CreateStaff extends CreateRecord
{
    protected static string $resource = StaffResource::class;
    private $password;

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema([
                FileUpload::make('picture')
                    ->avatar()
                    ->image(),
                TextInput::make('firstname')
                    ->required(),
                TextInput::make('lastname')
                    ->required(),
                DatePicker::make('dob')
                    ->label('Date of birth'),
                TextInput::make('email')
                    ->unique()
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->required(),
                Select::make('gender')
                    ->options(['male' => 'Male', 'female' => 'Female'])
                    ->required(),
                Select::make('roles')
                    ->label('Assign role')
                    ->relationship('roles', 'name', function ($query) {
                        return $query->where('name', '!=', 'super-admin');
                    })
                ,
                Select::make('class_assigned')
                    ->label('Assign class')
                    ->options([
                        'Elementary school' => Level::where('order', '<=', 11)->pluck('name', 'id')->toArray(),
                        'High school' => Level::where('order', '>', 11)->pluck('name', 'id')->toArray(),
                    ])
                    ->nullable(),
                TextInput::make('address')
                    ->required()
            ]);
    }


    protected function handleRecordCreation(array $data): Model
    {
        $class_assigned = isset($data['class_assigned']) ? $data['class_assigned'] : null;

        unset($data['class_assigned']);

        // Relationship on select field handles this
        unset($data['roles']);

        $password = Str::random(8);
        $this->password = $password;

        $data['password'] = Hash::make($this->password);

        $user = static::getModel()::create($data);
        $user->password = $password;

        if ($class_assigned !== null) {
            $target_class = Classes::find($class_assigned);
            $target_class->teacher = $user->id;
            $target_class->save();
        }

        $user->save();

        return $user;
    }

    protected function getCreatedNotification(): ?Notification
    {
        // show a notification including the temporary password
        return Notification::make()
            ->title('Staff created successfully! Their temporary password is ' . $this->password)
            ->body('It is important that they change this password immediately to keep their account secure. Please inform them to check their email for further instructions.')
            ->persistent()
            ->success()
            ->send();
    }

    protected function onValidationError(ValidationException $exception): void
    {
        Notification::make()
            ->title($exception->getMessage())
            ->danger()
            ->send();
    }
}
