<?php

namespace App\Console\Commands;

use App\Events\StudentPromoted;
use App\Models\Classes;
use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PromoteStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:promote-students';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Promotes all students to the next level, if there is any.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Promote all students from their current class to the next class based on the order of the levels.
        // For example, if the current class is 1, promote them to class 2.
        // Store the number of students promoted and how long the query took to execute.
        // TODO: Ensure students are only promoted at the end of the academic year.

        // Get all the students
        $students = User::role(User::$STUDENT_ROLE)->get();

        $totalRowsUpdated = 0;

        // Loop through each student
        foreach ($students as $student) {
            // Get the current class of the student
            if (!$student->class) {
                // TODO: Create a task model to assign tasks to admins to fix administrative issues like this
                $this->info("Student {$student->firstname} {$student->lastname} has no class.");
                continue;
            }

            $currentClass = $student->class;
            $currentClassLevel = $currentClass->level;
            $currentClassLevelOrder = $currentClassLevel->order;

            $expectedNextClassLevel = $currentClassLevelOrder + 1;

            // Check if the next class level exists
            $nextClassLevel = $student->class->level->where('order', $expectedNextClassLevel)->first();

            if ($nextClassLevel) {
                // Get the next class
                $nextClass = Classes::where('level_id', $nextClassLevel->id)->first();

                // Update the student's class to the next class
                $student->class_id = $nextClass->id;
                $student->save();

                $this->info("Student {$student->firstname} {$student->lastname} promoted to class {$nextClass->name}.");

                // Notify the student of the promotion
                StudentPromoted::dispatch($student, $currentClass, $nextClass);

                // Store the promotion in the student's history
                $student->promotions()->create(['class_id' => $nextClass->id]);

                $totalRowsUpdated += 1;
            } else {
                $this->info("Student {$student->firstname} {$student->lastname} has reached the highest class level of {$currentClassLevel->name} .");
            }
        }

        $this->info('Promotion completed successfully.');
        $endTime = microtime(true);
        $executionTime = $endTime - LARAVEL_START;
        $this->info($totalRowsUpdated . ' students promoted in ' . round($executionTime, 2) . ' seconds.');
    }
}
