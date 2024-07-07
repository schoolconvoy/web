<?php

namespace App\Trait;

use App\Events\ParentCreated;
use App\Models\Level;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait UserTrait
{

    public function updateParent(array $parentData, string $student_id)
    {
        $relationship = $parentData['relationship'];
        unset($parentData['relationship']);

        $email = $parentData['email'];
        $parent = User::where('email', $email)->first();

        $parent->wards()->attach($student_id, [
            'relationship' => $relationship
        ]);
        return $parent;
    }
    public function createParent(array $parentData, string $student_id)
    {
        $relationship = $parentData['relationship'];
        unset($parentData['relationship']);

        $parent_password = Str::random(8);
        $parentData['password'] = Hash::make($parent_password);

        $parent = User::create($parentData);

        $parent->assignRole(User::$PARENT_ROLE);
        $parent->save();
        //create parent ward relationship
        $parent->wards()->attach($student_id, [
            'relationship' => $relationship
        ]);
        Notification::make()
            ->title('Parent created successfully! Their temporary password is ' . $parent_password)
            ->body('It is important that they change this password immediately to keep their account secure. Please inform them to check their email for further instructions.')
            ->persistent()
            ->success()
            ->send();

        // Dispatch event
        ParentCreated::dispatch($parent);
        return $parent;
    }
    public function createStudent($studentData)
    {
        $student_password = Str::random(8);
        $studentData['password'] = Hash::make($student_password);
        $student = User::create($studentData);

        $student->assignRole(User::$STUDENT_ROLE);
        $student->save();
        Notification::make()
            ->title('Student created successfully! Their temporary password is ' . $student_password)
            ->body('It is important that they change this password immediately to keep their account secure. Please inform them to check their email for further instructions.')
            ->persistent()
            ->success()
            ->send();
        return $student;
    }
    public function convertParentAndStudentToDualArray(array $data)
    {
        $existing_parent = false;
        if ($data['existing_parent']) {
            $existing_parent = true;
        }
        unset($data['existing_parent']);
        // Filter out elements that start with "parent_"
        $parentData = array_filter($data, function ($key) {
            return strpos($key, 'parent_') === 0;
        }, ARRAY_FILTER_USE_KEY);

        // Remove parent elements from the original array
        $remainingData = array_diff_key($data, $parentData);

        // Remove the "parent_" prefix from the keys in the parentData array
        $parentData = array_combine(
            array_map(function ($key) {
                return substr($key, 7); // Remove the first 7 characters ("parent_")
            }, array_keys($parentData)),
            $parentData
        );
        $parentData['school_id'] = 1; //this is a default available school
        $remainingData['school_id'] = 1;
        return [
            'parent' => $parentData,
            'student' => $remainingData,
            'existing_parent' => $existing_parent
        ];
    }
    public static function getUserLevel()
    {
        return Level::where('order', '<', 12)->pluck('name', 'id')->toArray();
    }
}
