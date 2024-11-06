<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;
use Illuminate\Support\Facades\Log;

/**
 * This command can be triggered in two ways:
 * /quiz and by typing text containing "Quiz" in the chat
 */
class ExamCommand extends Command
{
    protected string $name = 'exam';
    protected string $description = 'Set the exam you want to take a quiz for. You can also provide the subject here. /exam WAEC Mathematics';
    protected string $pattern = '{exam: .*}';

    const EXAMS = [
        'waec' => 'WAEC',
        'neco' => 'NECO',
        'jamb' => 'JAMB',
        // 'post-utme' => 'Post-UTME',
        'bece' => 'BECE',
        'common-entrance' => 'Common Entrance',
        // 'nabteb' => 'NABTEB',
        // 'nysc' => 'NYSC',
        'toefl' => 'TOEFL',
        'ielts' => 'IELTS'
    ];

    public function handle()
    {
        # Get the username argument if the user provides,
        # (optional) fallback to username from Update object as the default.
        $exam = $this->argument('exam');

        $inline_keyboard = Keyboard::make()
                            ->setResizeKeyboard(true)
                            ->setOneTimeKeyboard(true)
                            ->inline();

        foreach(self::EXAMS as $key => $subject)
        {
            $row[] = Keyboard::inlineButton([
                'text' => $subject,
                'callback_data' => json_encode(['exam' => $key]),
            ]);

            if (count($row) === 2) {
                $inline_keyboard->row($row);
                $row = array();
            }
        }

        Log::debug("[/exam] exam selected: " . print_r($exam, true));

        if ($exam) {
            [$exam_name, $subject] = explode(' ', $exam, 2);

            $exam_name = strtolower($exam_name);
            $subject = isset($subject) ? null : strtolower($subject);

            if (array_key_exists($exam_name, self::EXAMS)) {
                $exam = $exam_name;
            } else {
                $this->replyWithMessage([
                    'text' => "Sorry we don't support " . $exam_name . " yet, but we've made a note of it. Please choose from the available subjects",
                    'reply_markup' => $inline_keyboard
                ]);
            }

            // TODO: Setting the level here doesn't work yet
            Log::debug("[/level] level selected: " . print_r($exam, true));
            $this->replyWithChatAction(['action' => Actions::TYPING]);
            $this->replyWithMessage([
                'text' => "Input exam and subject is " . $exam_name . " " . $subject,
                'reply_markup' => $inline_keyboard
            ]);
            $this->triggerCommand('examsubject');
        } else {
            $this->replyWithMessage([
                'text' => "Please choose your subject in this exam",
                'reply_markup' => $inline_keyboard
            ]);
        }
    }
}
