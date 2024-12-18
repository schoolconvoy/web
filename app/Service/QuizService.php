<?php

namespace App\Service;

use App\Models\ConversationQuiz;
use App\Models\QuizScore;
use Harishdurga\LaravelQuiz\Models\{Question, QuestionOption, Quiz, QuizAuthor, QuizQuestion, QuizAttemptAnswer, QuizAttempt, Topic};
use Illuminate\Support\{Collection, Facades\Cache, Facades\DB, Facades\Log, Str};
use Telegram\Bot\Laravel\Facades\Telegram;

class QuizService
{
    private const LEVELS = [
        'grade-1' => 'Grade 1', 'grade-2' => 'Grade 2', 'grade-3' => 'Grade 3',
        'grade-4' => 'Grade 4', 'grade-5' => 'Grade 5', 'grade-6' => 'Grade 6',
        'jss-1' => 'JSS 1', 'jss-2' => 'JSS 2', 'jss-3' => 'JSS 3',
        'ss-1' => 'SS 1', 'ss-2' => 'SS 2', 'ss-3' => 'SS 3'
    ];

    private const EXAMS = [
        'waec' => 'WAEC', 'neco' => 'NECO', 'jamb' => 'JAMB',
        'post-utme' => 'Post-UTME', 'bece' => 'BECE',
        'common-entrance' => 'Common Entrance', 'nabteb' => 'NABTEB',
        'nysc' => 'NYSC', 'cambridge' => 'Cambridge', 'toefl' => 'TOEFL',
        'ielts' => 'IELTS', 'gre' => 'GRE', 'gmat' => 'GMAT',
        'sat' => 'SAT', 'act' => 'ACT', 'lsat' => 'LSAT'
    ];

    private static ?self $instance = null;

    public string $level = 'Grade 1';
    public string $exam = 'JAMB';
    public string $subject = 'Use of English';
    public int $id;
    public string $username = 'user';
    public int $chatId = 0;
    public int $correctOptionIndex = 0;
    public array $options = [];
    public int $pollId = 0;

    /**
     * Returns a singleton instance of the QuizService.
     */
    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    /**
     * Sets the chat ID for the current session.
     */
    public function setChatId(int $chatId): self
    {
        $this->chatId = $chatId;
        return $this;
    }

    /**
     * Sets the difficulty level for the quiz based on predefined levels.
     */
    public function setLevel(string $level): self
    {
        $this->level = self::LEVELS[$level] ?? $this->level;
        return $this;
    }

    /**
     * Sets the exam type for the quiz based on predefined exams.
     */
    public function setExam(string $exam): self
    {
        $this->exam = strtoupper(self::EXAMS[$exam] ?? $this->exam);
        return $this;
    }

    public function setPollID(int $pollId): self
    {
        $this->pollId = $pollId;
        return $this;
    }

    /**
     * Sets the subject for the quiz and formats it for display.
     */
    public function setSubject(string $subject): self
    {
        // convert 'use-of-english' to 'Use of English'
        $this->subject = Str::title(Str::replace('-', ' ', $subject));
        return $this;
    }

    /**
     * Sets the username for the current quiz session.
     */
    public function setUsername(string $username = 'anonymous'): self
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Creates a unique name for the quiz using the exam type and username.
     */
    public function createQuizName(): string
    {
        return "{$this->exam} Quiz for {$this->username} " . Str::random(5);
    }

    /**
     * Starts the quiz by creating and sending the first question as a poll.
     */
    public function start()
    {
        // This will give 15 questions in total
        $quiz_question = $this->createQuestion(5);
        $optionsData = $this->getOptions($quiz_question->question_id);

        $question = Question::find($quiz_question->question_id);

        $pollResponse = $this->sendPoll([
            'question' => $question->name,
            'correct_option' => $optionsData['correctOption'],
            'explanation' => $question->explanation,
            'options' => $optionsData['options'],
        ]);

        $this->setPollID($pollResponse->poll->id);

        // Start a new quiz attempt
        $quiz_attempt = QuizAttempt::create([
            'quiz_id' => $quiz_question->quiz_id,
            'participant_id' => $this->chatId,
            'participant_type' => get_class(Telegram::getMe()),
        ]);

        // One time, attach the user to the quiz
        $quiz_author = QuizAuthor::create([
            'quiz_id' => $quiz_question->quiz_id,
            'author_id' => $this->chatId,
            'author_type' => get_class(Telegram::getMe()),
            'author_role' => 'user',
        ]);

        // Record the quiz session, we need attempt_id to record the user's answer
        $this->createConversationalQuiz([
            'quiz_id' => $quiz_question->quiz_id,
            'attempt_id' => $quiz_attempt->id,
            'question_id' => $question->id
        ]);
    }

    /**
     * Creates a new conversational quiz entry in the database.
     */
    public function createConversationalQuiz(array $payload): ConversationQuiz
    {
        return ConversationQuiz::create([
            'quiz_id' => $payload['quiz_id'],
            'question_id' => $payload['question_id'],
            'attempt_id' => $payload['attempt_id'],
            'chat_id' => $this->chatId,
            'username' => $this->username,
            'poll_id' => $this->pollId
        ]);
    }

    /**
     * Retrieves the options for a given question and identifies the correct one.
     */
    public function getOptions(int $questionId): array
    {
        $options = QuestionOption::where('question_id', $questionId)->get();
        $correctOptionName = $options->firstWhere('is_correct', true)?->name;
        $correctOptionIndex = array_search($correctOptionName, $options->pluck('name')->toArray());

        Log::debug("Options are " . print_r(['options' => $options->pluck('name')->toArray(), 'correctOption' => $correctOptionIndex], true));

        return ['options' => $options->pluck('name')->toArray(), 'correctOption' => $correctOptionIndex];
    }

    /**
     * Creates a specified number of quiz questions and associates them with the quiz.
     */
    public function createQuestion(int $count = 1): QuizQuestion
    {
        $exam = Topic::firstWhere('name', $this->exam) ?? throw new \Exception("Exam not found {$this->exam}");
        $subject = $exam->children()->firstWhere('name', $this->subject) ?? throw new \Exception("Subject not found {$this->subject}");

        $topics = $subject->children()->inRandomOrder()->limit(3)->get();
        $questions = $topics->flatMap(fn($topic) => $topic->questions()->inRandomOrder()->limit($count)->get());

        $quiz = Quiz::create([
            'name' => $this->createQuizName(),
            'description' => 'Test your knowledge of computer science',
            'slug' => Str::slug($this->createQuizName()),
            'time_between_attempts' => 0, //Time in seconds between each attempt
            'total_marks' => count($questions),
            'pass_marks' => count($questions) / 2,
            'max_attempts' => 1,
            'is_published' => 1,
            'valid_from' => now(),
            'valid_upto' => now()->addMinutes(30),
            'media_url'=>'',
            'media_type'=>'',
            'negative_marking_settings'=>[
                'enable_negative_marks' => true,
                'negative_marking_type' => 'fixed',
                'negative_mark_value' => 0,
            ]
        ]);

        foreach ($questions as $index => $question) {
            $quiz_question = QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question_id' => $question->id,
                'marks' => 1,
                'order' => $index + 1, // order will be used to send the next question
                'is_optional' => true
            ]);
        }

        // Send the first question, according to the order
        return QuizQuestion::whereQuizId($quiz->id)
                            ->whereOrder(1)
                            ->firstOrFail();
    }

    /**
     * Fetches and sends the next question in the quiz.
     */
    public function nextQuestion()
    {
        $quiz_session = ConversationQuiz::wherePollId($this->pollId)
                                        ->firstOrFail();

        Log::debug("Quiz session is " . print_r($quiz_session, true));

        $quiz_id = $quiz_session->quiz_id;

        // Get all questions that have been sent in this quiz session
        // Poll ID of the previous question will be used to retrieve the quiz ID
        Log::debug("Poll ID is {$this->pollId} and Quiz ID is {$quiz_id}");

        $totalQuestions = ConversationQuiz::whereQuizId($quiz_id)->count();

        Log::debug("Total questions answered: $totalQuestions");

        // Get the next question in the quiz
        $quiz_question = QuizQuestion::whereQuizId($quiz_id)
                            ->where('order', '>', $totalQuestions)
                            ->firstOrFail();

        Log::debug("Next question is " . print_r($quiz_question, true));

        $question = Question::find($quiz_question->question_id);

        Log::debug("Underlying Question is " . print_r($question, true));

        $optionsData = $this->getOptions($question->id);

        $pollResponse = $this->sendPoll([
            'question' => $question->name,
            'correct_option' => $optionsData['correctOption'],
            'explanation' => $question->explanation,
            'options' => $optionsData['options'],
        ]);

        $this->setPollID($pollResponse->poll->id);

        return $this->createConversationalQuiz([
            'quiz_id' => $quiz_id,
            'question_id' => $question->id,
            'attempt_id' => $quiz_session->attempt_id
        ]);
    }

    /**
     * Sends a poll to the user through Telegram, formatted as a quiz.
     */
    public function sendPoll(array $selectedQuestion): \Telegram\Bot\Objects\Message
    {
        return Telegram::sendPoll([
            'chat_id' => $this->chatId,
            'question' => strip_tags($selectedQuestion['question']),
            'options' => $selectedQuestion['options'],
            'correct_option_id' => $selectedQuestion['correct_option'],
            'is_anonymous' => false,
            'open_period' => 60,
            'protect_content' => true,
            'type' => 'quiz',
            'explanation' => $selectedQuestion['explanation']
        ]);
    }

    /**
     * Checks if the user's answer is correct and records the score.
     */
    public function checkAnswer(\Telegram\Bot\Objects\TelegramObject $optionIds)
    {
        Log::debug("Checking answer for " . print_r($optionIds, true));

        // Poll ID is unique per question
        $currentQuestion = ConversationQuiz::wherePollId($this->pollId)
                                            ->firstOrFail();
        Log::debug("Current question is " . print_r($currentQuestion, true));

        $quiz_id = $currentQuestion->quiz_id;
        $quiz_attempt_id = $currentQuestion->attempt_id;

        $quiz_question = QuizQuestion::whereQuizId($quiz_id)
                                        ->where('question_id', $currentQuestion->question_id)
                                        ->firstOrFail();

        $total_questions_done = ConversationQuiz::whereQuizId($quiz_id)->count();

        Log::debug("Quiz question is " . print_r($quiz_question, true));

        $options = QuestionOption::where('question_id', $quiz_question->question_id)
                                    ->get();

        Log::debug("Options are " . print_r($options, true));

        $options = $options->pluck('name')->toArray();

        Log::debug("Options name are " . print_r($options, true));

        $selectedOptionIndex = $optionIds[0];
        $selectedOptionId = QuestionOption::where('question_id', $quiz_question->question_id)
                          ->where('name', $options[$selectedOptionIndex])
                          ->firstOrFail()
                          ->id;

        Log::debug("Selected option ID is $selectedOptionId");

        // Record the user's answer
        $quiz_attempt = QuizAttemptAnswer::create(
            [
                'quiz_attempt_id' => $quiz_attempt_id,
                'quiz_question_id' => $quiz_question->id,
                'question_option_id' => $selectedOptionId,
            ]
        );

        Log::debug("Quiz attempt is " . print_r($quiz_attempt, true));
    }

    public function totalScore(): string
    {
        $quiz_session = ConversationQuiz::wherePollId($this->pollId)
                                        ->firstOrFail();

        $quiz_attempt_id = $quiz_session->attempt_id;
        $quiz_attempt = QuizAttempt::find($quiz_attempt_id);
        $total = $quiz_attempt->calculate_score();

        return $total . '/' . $quiz_attempt->quiz->total_marks;
    }

    /**
     * Checks if there are more questions remaining in the quiz.
     */
    public function hasNextQuestion(): bool
    {
        $quiz_session = ConversationQuiz::wherePollId($this->pollId)
                                        ->firstOrFail();

        Log::debug("[hasNextQuestion] PollID and Quiz Session are: {$this->pollId} and {$quiz_session}");

        $totalQuestions = ConversationQuiz::whereQuizId($quiz_session->quiz_id)
                                            ->count();

        Log::debug("[hasNextQuestion] Total questions answered: $totalQuestions");

        $next_question_exists = QuizQuestion::whereQuizId($quiz_session->quiz_id)
                                        ->where('order', '>', $totalQuestions)
                                        ->exists();

        Log::debug("[hasNextQuestion] Next question exist?: $next_question_exists");

        return $next_question_exists;
    }

    /**
     * Ends the quiz, calculates the total score, and records it.
     */
    public function completeQuiz(): void
    {
        // QuizScore::create([
        //     'username' => $this->username,
        //     'score' => $this->totalScore()
        // ]);
        ConversationQuiz::whereChatId($this->chatId)->delete();
    }
}
