<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\FighterEnum;
use App\Http\Requests\GameChoiceRequest;
use App\Models\GameRound;
use App\Services\GameService;
use Exception;
use Illuminate\Console\Command;

class GameRpsls extends Command
{
    protected $signature = 'game:rpsls';
    protected $description = 'Play Rock Paper Scissors Lizard Spock 1st player against the computer';

    private array $fighterOptions;

    public function __construct(private readonly GameService $gameService)
    {
        parent::__construct();

        $this->fighterOptions = FighterEnum::cases();
        $this->gameService->startGame();
    }

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        $this->displayWelcomeMessage();
        $this->runGameLoop();
    }

    private function displayWelcomeMessage(): void
    {
        $this->info('Welcome to Rock Paper Scissors Lizard Spock game!');
    }

    /**
     * @throws Exception
     */
    private function runGameLoop(): void
    {
        while (true) {
            $this->displayMenu();
            $choice = $this->ask('Your choice');

            if (!$this->isValidChoice($choice)) {
                continue;
            }

            if ($choice === '0') {
                $this->endGame();
                break;
            }

            $this->playRound($this->fighterOptions[$choice - 1]);
        }
    }

    private function displayMenu(): void
    {
        $this->info("\nChoose your move:");

        $i = 0;
        foreach ($this->fighterOptions as $option) {
            ++$i;
            $this->info($i . '. ' . $option->getName());
        }

        $this->info('0. Quit');
    }

    private function isValidChoice(string $choice): bool
    {
        $request = new GameChoiceRequest();
        $request->choice = $choice;
        $request->maxChoice = count($this->fighterOptions);

        if (!$request->validate()) {
            $this->displayValidationErrors($request->errors());
            return false;
        }

        return true;
    }

    private function displayValidationErrors(array $errors): void
    {
        $this->error('Invalid choice:');
        foreach ($errors as $error) {
            $this->error($error);
        }
    }

    /**
     * @throws Exception
     */
    private function endGame(): void
    {
        $this->displayFinalStats();
        $this->info('Thanks for playing!');
        $this->gameService->endGame();
    }

    private function displayFinalStats(): void
    {
        $this->info("\nFinal Score:");
        $this->showGameScores();
    }

    /**
     * @throws Exception
     */
    private function playRound(FighterEnum $fighter): void
    {
        $this->gameService->playRound($fighter);
        $this->showRoundResults();
        $this->showGameScores();
    }

    private function showRoundResults(): void
    {
        $round = $this->gameService->getLastGameRound();
        $this->displayPlayerChoices($round);

        if ($round->fighter_player === $round->fighter_opponent) {
            $this->displayDrawResult();
        } else {
            $this->displayWinLoseResult($round);
        }
    }

    private function displayPlayerChoices(GameRound $round): void
    {
        $this->info('You chose: ' . $round->fighter_player->getName());
        $this->info('Computer chose: ' . $round->fighter_opponent->getName());
    }

    private function displayDrawResult(): void
    {
        $this->info('No Winner - Draw!');
    }

    private function displayWinLoseResult(GameRound $round): void
    {
        if ($round->is_win) {
            $message = 'You win!';
            $winner = $round->fighter_player;
            $loser = $round->fighter_opponent;
        } else {
            $message = 'You lose!';
            $winner = $round->fighter_opponent;
            $loser = $round->fighter_player;
        }

        $this->info('Result: ' . $message);
        $this->info(
            $winner->getName() . ' ' . $this->gameService->getAction($winner, $loser) . ' ' . $loser->getName()
        );
    }

    private function showGameScores(): void
    {
        $game = $this->gameService->getGame();
        $this->info(sprintf('Score: You %d - %d Computer', $game->wins, $game->loses));
        $this->info(sprintf('Draws: %d', $game->draws));
    }
}
