<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Harishdurga\LaravelQuiz\Models\Quiz;

class QuizClasses extends Model
{
    use HasFactory;

    /**
     * @return \Harishdurga\LaravelQuiz\Models\Quiz
     */
    public function quiz()
    {
        return $this->belongsToMany(Quiz::class);
    }
}
