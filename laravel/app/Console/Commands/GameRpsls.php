<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\FighterEnum;
use App\Exceptions\GameException;
use App\Http\Requests\GameChoiceRequest;
use App\Presenters\GameRpslsPresenterInterface;
use App\Services\GameServiceInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Throwable;

class GameRpsls extends Command
{
    protected $signature = 'game:rpsls';
    protected $description = 'Play Rock Paper Scissors Lizard Spock 1st player against the computer';


    private array $fighterOptions;

    public function __construct(
        private readonly GameServiceInterface $gameService,
        private readonly GameRpslsPresenterInterface $presenter
    ) {
        parent::__construct();

        $this->fighterOptions = FighterEnum::cases();
        $this->gameService->startGame();
        $this->presenter->setCommand($this);
    }

    public function handle(): int
    {
        try {
            $this->presenter->displayWelcomeMessage();
            $this->runGameLoop();
            return CommandAlias::SUCCESS;
        } catch (Throwable $e) {
            $this->handleError($e);
            return CommandAlias::FAILURE;
        }
    }

    private function runGameLoop(): void
    {
        while (true) {
            try {
                $this->presenter->displayMenu($this->fighterOptions);
                $choice = $this->presenter->askForChoice();

                if (!$this->isValidChoice($choice)) {
                    continue;
                }

                if ($choice === '0') {
                    $this->endGame();
                    break;
                }

                $this->playRound($this->getPlayerFighter((int) $choice));
            } catch (Throwable $e) {
                $this->handleError($e);
                continue;
            }
        }
    }

    private function getPlayerFighter(int $choice): FighterEnum
    {
        return $this->fighterOptions[$choice - 1];
    }

    private function isValidChoice(?string $choice): bool
    {
        $request = new GameChoiceRequest();
        $request->choice = $choice;
        $request->maxChoice = count($this->fighterOptions);

        if (!$request->validate()) {
            $this->presenter->displayValidationErrors($request->errors());
            return false;
        }

        return true;
    }

    private function endGame(): void
    {
        try {
            $game = $this->gameService->getGame();
            $this->presenter->displayFinalStats($game);
            $this->presenter->displayThanksMessage();
            $this->gameService->endGame();
        } catch (Throwable $e) {
            $this->handleError($e);
        }
    }

    private function playRound(FighterEnum $fighter): void
    {
        try {
            $this->gameService->playRound($fighter);
            $this->showRoundResults();
            $this->showGameScores();
        } catch (Throwable $e) {
            $this->handleError($e);
        }
    }

    private function showRoundResults(): void
    {
        try {
            $round = $this->gameService->getLastGameRound();

            if ($round->fighter_player !== $round->fighter_opponent) {
                $winner = $round->is_win ? $round->fighter_player : $round->fighter_opponent;
                $loser = $round->is_win ? $round->fighter_opponent : $round->fighter_player;
                $action = $this->gameService->getAction($winner, $loser);
            } else {
                $action = '';
            }

            $this->presenter->showRoundResults($round, $action);
        } catch (Throwable $e) {
            $this->handleError($e);
        }
    }

    private function showGameScores(): void
    {
        try {
            $game = $this->gameService->getGame();
            $this->presenter->showGameScores($game);
        } catch (Throwable $e) {
            $this->handleError($e);
        }
    }

    private function handleError(Exception $e): void
    {
        if ($e instanceof GameException) {
            Log::error('[' . $e->getPrefix() . '] ' . $e->getMessage(), ['exception' => $e]);
        } else {
            Log::error('[' . GameException::ERROR_PREFIX . '] ' . $e->getMessage(), ['exception' => $e]);
        }
        $this->presenter->displayError($e->getMessage());
    }
}
