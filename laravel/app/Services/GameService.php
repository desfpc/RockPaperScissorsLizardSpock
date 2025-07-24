<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\FighterEnum;
use App\Enums\GameStatusEnum;
use App\Models\Game;
use App\Models\GameRound;
use App\Repositories\GameRepositoryInterface;
use App\Repositories\GameRoundRepositoryInterface;
use Exception;

class GameService
{
    private ?Game $game = null;
    private ?GameRound $lastGameRound = null;

    public function __construct(
        private readonly GameRepositoryInterface $gameRepository,
        private readonly GameRoundRepositoryInterface $gameRoundRepository,
        private readonly FighterRulesService $fighterRulesService
    ) {
    }

    public function startGame(): void
    {
        $this->game = $this->gameRepository->getActiveUserGame();
        $this->gameRepository->save($this->game);
    }

    /**
     * @throws Exception
     */
    public function playRound(FighterEnum $playerFighter): void
    {
        $this->validateGameExists();
        $this->processRound($playerFighter);
        $this->updateGameScore();
    }

    public function getLastGameRound(): ?GameRound
    {
        return $this->lastGameRound;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    /**
     * @throws Exception
     */
    public function endGame(): void
    {
        $this->validateGameExists();
        $this->game->status_id = GameStatusEnum::FINISHED;
        $this->gameRepository->save($this->game);
    }

    public function getAction(FighterEnum $player, FighterEnum $opponent): string
    {
        return $this->fighterRulesService->getAction($player, $opponent);
    }

    protected function getRandomOpponent(): FighterEnum
    {
        $cases = FighterEnum::cases();
        return $cases[array_rand($cases)];
    }

    /**
     * @throws Exception
     */
    private function validateGameExists(): void
    {
        if (!$this->game) {
            throw new Exception('Game not found');
        }
    }

    private function processRound(FighterEnum $playerFighter): void
    {
        $this->lastGameRound = $this->gameRoundRepository->getLastGameRound();
        $this->lastGameRound->counter++;
        $this->lastGameRound->fighter_player = $playerFighter;
        $this->lastGameRound->fighter_opponent = $this->getRandomOpponent();
        $this->lastGameRound->is_win = $this->fighterRulesService->canWin(
            $this->lastGameRound->fighter_player,
            $this->lastGameRound->fighter_opponent
        );
        $this->gameRoundRepository->save($this->lastGameRound);
    }

    private function updateGameScore(): void
    {
        if ($this->lastGameRound->fighter_player === $this->lastGameRound->fighter_opponent) {
            $this->game->draws++;
        } elseif ($this->lastGameRound->is_win) {
            $this->game->wins++;
        } else {
            $this->game->loses++;
        }

        $this->gameRepository->save($this->game);
    }
}
