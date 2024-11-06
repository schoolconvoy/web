<?php

namespace App\Http\Controllers;

use App\Service\QuizService;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatBotController extends Controller
{
    public function __construct()
    {
    }

    public function chatbot()
    {
        $response = Telegram::bot('mybot')->getMe();

        return $response;
    }

    public function webhook()
    {
        $update = Telegram::commandsHandler(true);

        // if ($update->isType('poll_answer')) {
        //     Log::debug("Poll answer: " . print_r($update->pollAnswer, true));
        //     return 'ok';
        // }

        if ($update->isType('message')) {
            // Text message
            $message = $update->getMessage();
            $text = $message->text;

            if (Str::contains($text, 'Select your exam')) {
                Telegram::triggerCommand('exam', $update);

                return 'ok';
            }
        } else if ($update->isType('callback_query')) {
            // Callback query from a button
            $callback = $update->getCallbackQuery();
            $data = json_decode($callback?->data);
            Log::debug("Callback data: " . print_r($data, true));

            $chatId = $callback->getMessage()->getChat()->getId();
            $username = $callback->getFrom()->getUsername();

            if ($data && isset($data->exam)) {
                $exam = $data->exam;

                // Trigger the next step
                Telegram::triggerCommand('examsubject', $update);

                // Start the quiz
                // $quiz = QuizService::getInstance();
                // $quiz->setChatId($chatId)
                //         ->setExam($exam)
                //         ->setUsername($username)
                //         ->createExam();
                Log::debug("Exam selected: " . print_r($exam, true));

                return 'ok';
            } else if ($data && $data->subject) {
                $subject = $data->subject;

                // Since we can't trigger a command with arguments, we will use the service directly here.
                // The user can still trigger the command directly with the level argument.
                $quiz = QuizService::getInstance();
                $quiz->setChatId($chatId)
                        ->setSubject($subject)
                        ->setUsername($username)
                        ->start();

                return 'ok';
            }

            return 'ok';
        } else if ($update->isType('poll_answer')) {
            // Poll answer (Quiz)
            $chatId = $update->getPollAnswer()->getUser()->getId();
            $username = $update->getPollAnswer()->getUser()->getUsername();

            $quiz = QuizService::getInstance();
            $pollId = $update->pollAnswer->getPollId();

            Log::debug("Poll ID: " . print_r($pollId, true));
            try {
                $quiz->setChatId($chatId)
                    ->setUsername($username)
                    ->setPollID($pollId)
                    ->checkAnswer($update->pollAnswer->getOptionIds());

                $hasNextQuestions = $quiz->setChatId($chatId)
                    ->setUsername($username)
                    ->setPollID($pollId)
                    ->hasNextQuestion();

                if (!$hasNextQuestions) {
                    $total = $quiz->setPollID($pollId)->totalScore();
                    Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => "You have completed the quiz. Your score is {$total}."
                    ]);
                    // TODO: Return a leader board, suggest a telegram channel, a button to start another quiz, etc.

                    Log::debug("Quiz completed");

                    return 'ok';
                }

                $quiz->setChatId($chatId)
                    ->setUsername($username)
                    ->setPollID($pollId)
                    ->nextQuestion();

                return 'ok';
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return 'ok';
            }
        }

        return 'ok';
    }

    public function telegramSetupWebhook()
    {
        $response = Telegram::setWebhook(['url' => 'https://iyiit75sm3.sharedwithexpose.com/sc-h-ool-con-vo-y/webhook']);

        // Clear webhook
        // $response = Telegram::removeWebhook();

        return $response;
    }
}
