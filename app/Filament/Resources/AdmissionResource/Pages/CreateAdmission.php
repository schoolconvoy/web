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

        
        $student = $this->createStudent($studentAndParent['student']);

        if ($studentAndParent['existing_parent']) {
            $this->updateParent($studentAndParent['parent'], $student->id);
            //handle existing parent
            //update parent_ward table
        } else {
            $this->createParent($studentAndParent['parent'], $student->id);
            //handle parent
        }


        return $student;
    }

    
}
