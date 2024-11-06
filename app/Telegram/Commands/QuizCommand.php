<?php

namespace App\Telegram\Commands;

use App\Service\QuizService;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * This command can be triggered in two ways:
 * /quiz and by typing text containing "quiz" in the chat
 */
class QuizCommand extends Command
{
    protected string $name = 'quiz';
    protected string $description = 'Start a new quiz for your exam';
    protected string $pattern = '{exam_subject* : The exam and subject for the quiz (e.g., "Grade 2")}';

    public function handle()
    {
        $exam = $this->argument('exam');
        $exam = is_array($exam) ? implode(' ', $exam) : $exam;
        Log::alert("exam provided: " . $exam);

        if ($exam) {
            $chatId = $this->update->getChat()->id;
            $quiz = new QuizService($exam, $chatId);
            $quiz->start();
        } else {
            $this->replyWithChatAction(['action' => Actions::TYPING]);
            // $this->triggerCommand('exam');
        }
    }
}
