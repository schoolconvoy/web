<?php

namespace App\Filament\Resources\AdmissionResource\Pages;

use App\Filament\Resources\AdmissionResource;
use App\Trait\UserTrait;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateAdmission extends CreateRecord
{
    use UserTrait;
    
    protected static string $resource = AdmissionResource::class;
    public static bool $hasInlineLabels = true;
    // public array $review = [];

    protected function handleRecordCreation(array $data): Model
    {
        $studentAndParent = $this->convertParentAndStudentToDualArray($data);

        $student_password = Str::random(8);
        $studentAndParent['student']['password'] = Hash::make($student_password);
        $student = $this->createStudent($studentAndParent['student']);
        Notification::make()
            ->title('Student created successfully! Their temporary password is ' . $student_password)
            ->body('It is important that they change this password immediately to keep their account secure. Please inform them to check their email for further instructions.')
            ->persistent()
            ->success()
            ->send();

        if ($studentAndParent['existing_parent']) {
            $this->updateParent($studentAndParent['parent'], $student->id);
            //handle existing parent
            //update parent_ward table

        } else {
            $parent_password = Str::random(8);
            $studentAndParent['parent']['password'] = Hash::make($parent_password);
            $this->createParent($studentAndParent['parent'], $student->id);
            //handle parent
            Notification::make()
                ->title('Parent created successfully! Their temporary password is ' . $parent_password)
                ->body('It is important that they change this password immediately to keep their account secure. Please inform them to check their email for further instructions.')
                ->persistent()
                ->success()
                ->send();
        }


        return $student;
    }
}
