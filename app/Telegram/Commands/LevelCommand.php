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
class LevelCommand extends Command
{
    protected string $name = 'level';
    protected string $description = 'Set your level for this bot';
    protected string $pattern = '{level*}';

    const LEVELS = [
        'grade-1' => 'Grade 1',
        'grade-2' => 'Grade 2',
        'grade-3' => 'Grade 3',
        'grade-4' => 'Grade 4',
        'grade-5' => 'Grade 5',
        'grade-6' => 'Grade 6',
        'jss-1' => 'JSS 1',
        'jss-2' => 'JSS 2',
        'jss-3' => 'JSS 3',
        'ss-1' => 'SS 1',
        'ss-2' => 'SS 2',
        'ss-3' => 'SS 3'
    ];

    public function handle()
    {
        # Get the username argument if the user provides,
        # (optional) fallback to username from Update object as the default.
        $level = $this->argument('level');

        $inline_keyboard = Keyboard::make()
                            ->setResizeKeyboard(true)
                            ->setOneTimeKeyboard(true)
                            ->inline()
                            ->row([
                                Keyboard::inlineButton([
                                    'text' => 'Grade 1',
                                    'callback_data' => json_encode(['level' => 'grade-1']),
                                ]),
                                Keyboard::inlineButton([
                                    'text' => 'Grade 2',
                                    'callback_data' => json_encode(['level' => 'grade-2']),
                                ]),
                                Keyboard::inlineButton([
                                    'text' => 'Grade 3',
                                    'callback_data' => json_encode(['level' => 'grade-3']),
                                ]),
                            ])
                            ->row([
                                Keyboard::inlineButton([
                                    'text' => 'Grade 4',
                                    'callback_data' => json_encode(['level' => 'grade-4']),
                                ]),
                                Keyboard::inlineButton([
                                    'text' => 'Grade 5',
                                    'callback_data' => json_encode(['level' => 'grade-5']),
                                ]),
                                Keyboard::inlineButton([
                                    'text' => 'Grade 6',
                                    'callback_data' => json_encode(['level' => 'grade-6']),
                                ]),
                            ])
                            ->row([
                                Keyboard::inlineButton([
                                    'text' => 'JSS 1',
                                    'callback_data' => json_encode(['level' => 'jss-1']),
                                ]),
                                Keyboard::inlineButton([
                                    'text' => 'JSS 2',
                                    'callback_data' => json_encode(['level' => 'jss-2']),
                                ]),
                                Keyboard::inlineButton([
                                    'text' => 'JSS 3',
                                    'callback_data' => json_encode(['level' => 'jss-3']),
                                ]),
                            ])
                            ->row([
                                Keyboard::inlineButton([
                                    'text' => 'SS 1',
                                    'callback_data' => json_encode(['level' => 'ss-1']),
                                ]),
                                Keyboard::inlineButton([
                                    'text' => 'SS 2',
                                    'callback_data' => json_encode(['level' => 'ss-2']),
                                ]),
                                Keyboard::inlineButton([
                                    'text' => 'SS 3',
                                    'callback_data' => json_encode(['level' => 'ss-3']),
                                ])
                            ]);

        if ($level) {
            // TODO: Setting the level here doesn't work yet
            Log::debug("[/level] level selected: " . print_r($level, true));
            $this->replyWithChatAction(['action' => Actions::TYPING]);
            $this->triggerCommand('quiz');
        } else {
            $this->replyWithMessage([
                'text' => "Please choose your educational level",
                'reply_markup' => $inline_keyboard
            ]);
        }
    }
}
