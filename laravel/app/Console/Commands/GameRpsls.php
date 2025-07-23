<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\FighterEnum;
use App\Http\Requests\GameChoiceRequest;
use App\Services\GameService;
use Exception;
use Illuminate\Console\Command;

class GameRpsls extends Command
{
    protected $signature = 'game:rpsls';
    protected $description = 'Play Rock Paper Scissors Lizard Spock 1st player against the computer';

    private array $cases;

    public function __construct(private readonly GameService $gameService)
    {
        parent::__construct();

        $this->cases = FighterEnum::cases();
        $this->gameService->startGame();
    }

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        $this->info('Welcome to Rock Paper Scissors Lizard Spock game!');

        while (true) {
            $this->displayMenu();
            $choice = $this->ask('Your choice');

            $request = new GameChoiceRequest();
            $request->choice = $choice;
            $request->maxChoice = count($this->cases);

            if (!$request->validate()) {
                $this->error('Invalid choice:');
                foreach ($request->errors() as $error) {
                    $this->error($error);
                }
                continue;
            }

            if ($choice === '0') {
                $this->displayFinalStats();
                $this->info('Thanks for playing!');
                $this->gameService->endGame();
                break;
            }

            $this->processGame($this->cases[$choice - 1]);
        }
    }

    private function displayMenu(): void
    {
        $this->info("\nChoose your move:");

        $i = 0;
        foreach ($this->cases as $case) {
            ++$i;

            $this->info($i . '. ' . $case->getName());
        }

        $this->info('0. Quit');
    }

    private function displayFinalStats(): void
    {
        $this->info("\nFinal Score:");
        $this->showGameScores();
    }

    /**
     * @throws Exception
     */
    private function processGame(FighterEnum $fighter): void
    {
        $this->gameService->playRound($fighter);
        $this->showRoundResults();
        $this->showGameScores();
    }

    private function showRoundResults(): void
    {
        $round = $this->gameService->getLastGameRound();
        $this->info('You chose: ' . $round->fighter_player->getName());
        $this->info('Computer chose: ' . $round->fighter_opponent->getName());

        if ($round->fighter_player === $round->fighter_opponent) {
            $this->info('No Winner - Draw!');
        } else {
            if ($round->is_win) {
                $message = 'You win!';
                $first = $round->fighter_player;
                $second = $round->fighter_opponent;
            } else {
                $message = 'You lose!';
                $first = $round->fighter_opponent;
                $second = $round->fighter_player;
            }
            $this->info('Result: ' . $message);
            $this->info(
                $first->getName() . ' ' . $this->gameService->getAction($first, $second) . ' ' . $second->getName()
            );
        }
    }

    private function showGameScores(): void
    {
        $game = $this->gameService->getGame();
        $this->info(sprintf('Score: You %d - %d Computer', $game->wins, $game->loses));
        $this->info(sprintf('Draws: %d', $game->draws));
    }
}
