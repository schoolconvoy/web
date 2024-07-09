<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Events\CreatedUser;
use App\Filament\Resources\StudentResource;
use App\Models\User;
use App\Models\UserMeta;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\Wizard;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Set;
use Filament\Forms\Components\View;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Level;
use Filament\Forms\Components\Actions\Action;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    public static bool $hasInlineLabels = true;
    public array $review = [];
    public string $password = '';

    protected function handleRecordCreation(array $data): Model
    {
        try {
            $class_assigned = $data['class_assigned'] ?? null;
            $data['admission_no'] = $data['admission_no'] ?? User::generateAdmissionNo();

            // Set the password
            $password = Str::random(8);
            $this->password = $password;
            $data['password'] = Hash::make($this->password);

            unset($data['class_assigned']);

            $user = static::getModel()::create($data);

            // Automatically assign the student role
            $user->assignRole(User::$STUDENT_ROLE);

            Log::debug("Class assigned " . $class_assigned);

            // Set the class
            if (is_null($class_assigned) && auth()->user()->hasAnyRole([User::$TEACHER_ROLE]) && auth()->user()->teacher_class) {
                $user->class_id = auth()->user()->teacher_class->id;
            } else {
                $user->class_id = $class_assigned;
            }
            $user->save();
            CreatedUser::dispatch($user);
        } catch (\Throwable $th) {
            throw $th;
        }

        return $user;
    }

    protected function getCreatedNotification(): ?Notification
    {
        // show a notification including the temporary password
        return Notification::make()
            ->title('Student created successfully! Their temporary password is ' . $this->password)
            ->body('It is important that they change this password immediately to keep their account secure. Please inform them to check their email for further instructions.')
            ->persistent()
            ->success()
            ->send();
    }
}
