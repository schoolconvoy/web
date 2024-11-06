<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

/**
 * This command can be triggered in two ways:
 * /start and /join due to the alias.
 */
class StartCommand extends Command
{
    protected string $name = 'start';
    protected array $aliases = ['join'];
    protected string $description = 'Start Command to get you started';
    protected string $pattern = '{username}
    {age: \d+}';

    public function handle()
    {
        # username from Update object to be used as fallback.
        $fallbackUsername = $this->getUpdate()->getMessage()->from->username;

        # Get the username argument if the user provides,
        # (optional) fallback to username from Update object as the default.
        $username = $this->argument(
            'username',
            $fallbackUsername
        );

        // It's a design choice to use inline keyboards all through the bot.
        $inline_keyboard = Keyboard::make()
                            ->setResizeKeyboard(true)
                            ->setOneTimeKeyboard(true)
                            ->row([
                                Keyboard::button('Select your exam'),
                            ])
                            // ->row([
                            //     Keyboard::button('Study'),
                            // ])
                            // ->row([
                            //     Keyboard::button('Quiz (competition)'),
                            // ])
                            // ->row([
                            //     Keyboard::button('Login to keep your progress'),
                            // ])
                            ->row([
                                Keyboard::button('Learn with SchoolConvoy'),
                            ]);


        $this->replyWithMessage([
            'text' => "Hello {$username}!\n\nWelcome to SchoolConvoy, select your level to start a new quiz or explore learning resources.",
            'reply_markup' => $inline_keyboard
        ]);

        // # This will update the chat status to "typing..."
        // $this->replyWithChatAction(['action' => Actions::TYPING]);

        // # Get all the registered commands.
        // $commands = $this->getTelegram()->getCommands();

        // $response = '';
        // foreach ($commands as $name => $command) {
        //     $response .= sprintf('/%s - %s' . PHP_EOL, $name, $command->getDescription());
        // }

        // $this->replyWithMessage(['text' => $response]);

        // if($this->argument('age', 0) >= 18) {
        //     $this->replyWithMessage(['text' => 'Congrats, You are eligible to buy premimum access to our membership!']);
        // } else {
        //     $this->replyWithMessage(['text' => 'Sorry, you are not eligible to access premium membership yet!']);
        // }
    }
}
