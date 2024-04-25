<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Harishdurga\LaravelQuiz\Models\Quiz;

class QuizClasses extends Model
{
    use HasFactory;

    protected $table = 'quiz_classes';

    /**
     * @return \Harishdurga\LaravelQuiz\Models\Quiz
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function quiz_class()
    {
        return $this->belongsTo(Classes::class);
    }
}
