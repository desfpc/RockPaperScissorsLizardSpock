<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Models\Game;
use App\Models\GameRound;
use Illuminate\Console\Command;

class GameRpslsPresenter implements GameRpslsPresenterInterface
{
    private const string MESSAGE_WELCOME = 'Welcome to Rock Paper Scissors Lizard Spock game!';
    private const string MESSAGE_CHOICE = 'Your choice';
    private const string MESSAGE_COMPUTER_CHOICE = 'Computer chose';
    private const string MESSAGE_CHOICE_MOVE = 'Choose your move';
    private const string MESSAGE_QUIT = 'Quit';
    private const string MESSAGE_INVALID_CHOICE = 'Invalid choice';
    private const string MESSAGE_THANKS = 'Thanks for playing!';
    private const string MESSAGE_FINAL_SCORE = 'Final Score';
    private const string MESSAGE_NO_WINNER = 'No Winner - Draw!';
    private const string MESSAGE_WIN = 'You win!';
    private const string MESSAGE_LOSE = 'You lose!';
    private const string MESSAGE_RESULT = 'Result';
    private const string MESSAGE_GAME_SCORE = 'Score: You %d - %d Computer';
    private const string MESSAGE_GAME_DRAW = 'Draws: %d';

    private ?Command $command = null;

    public function setCommand(Command $command): void
    {
        $this->command = $command;
    }

    public function displayWelcomeMessage(): void
    {
        $this->command->info(self::MESSAGE_WELCOME);
    }

    public function displayMenu(array $fighterOptions): void
    {
        $this->command->info("\n" . self::MESSAGE_CHOICE_MOVE . ":");

        $i = 0;
        foreach ($fighterOptions as $option) {
            ++$i;
            $this->command->info($i . '. ' . $option->getName());
        }

        $this->command->info('0. ' . self::MESSAGE_QUIT);
    }

    public function askForChoice(): string
    {
        return $this->command->ask(self::MESSAGE_CHOICE);
    }

    public function displayValidationErrors(array $errors): void
    {
        $this->command->error(self::MESSAGE_INVALID_CHOICE . ':');
        foreach ($errors as $error) {
            $this->command->error($error);
        }
    }

    public function displayFinalStats(Game $game): void
    {
        $this->command->info("\n" . self::MESSAGE_FINAL_SCORE . ':');
        $this->showGameScores($game);
    }

    public function displayThanksMessage(): void
    {
        $this->command->info(self::MESSAGE_THANKS);
    }

    public function showRoundResults(GameRound $round, string $action): void
    {
        $this->displayPlayerChoices($round);

        if ($round->fighter_player === $round->fighter_opponent) {
            $this->displayDrawResult();
        } else {
            $this->displayWinLoseResult($round, $action);
        }
    }

    public function showGameScores(Game $game): void
    {
        $this->command->info(sprintf(self::MESSAGE_GAME_SCORE, $game->wins, $game->loses));
        $this->command->info(sprintf(self::MESSAGE_GAME_DRAW, $game->draws));
    }

    private function displayPlayerChoices(GameRound $round): void
    {
        $this->command->info(self::MESSAGE_CHOICE . ': ' . $round->fighter_player->getName());
        $this->command->info(self::MESSAGE_COMPUTER_CHOICE . ': ' . $round->fighter_opponent->getName());
    }

    private function displayDrawResult(): void
    {
        $this->command->info(self::MESSAGE_NO_WINNER);
    }

    private function displayWinLoseResult(GameRound $round, string $action): void
    {
        if ($round->is_win) {
            $message = self::MESSAGE_WIN;
            $winner = $round->fighter_player;
            $loser = $round->fighter_opponent;
        } else {
            $message = self::MESSAGE_LOSE;
            $winner = $round->fighter_opponent;
            $loser = $round->fighter_player;
        }

        $this->command->info(self::MESSAGE_RESULT . ': ' . $message);
        $this->command->info(
            $winner->getName() . ' ' . $action . ' ' . $loser->getName()
        );
    }

    public function displayError(string $message): void
    {
        $this->command->error($message);
    }
}
