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
use InvalidArgumentException;

class GameService
{
    private ?Game $game = null;
    private ?GameRound $lastGameRound = null;

    public function __construct(
        private readonly GameRepositoryInterface $gameRepository,
        private readonly GameRoundRepositoryInterface $gameRoundRepository
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
        if (!$this->game) {
            throw new Exception('Game not found');
        }

        $this->lastGameRound = $this->gameRoundRepository->getLastGameRound();
        $this->lastGameRound->counter++;
        $this->lastGameRound->fighter_player = $playerFighter;
        $this->lastGameRound->fighter_opponent = $this->getRandomOpponent();
        $this->lastGameRound->is_win = $this->canWin(
            $this->lastGameRound->fighter_player,
            $this->lastGameRound->fighter_opponent
        );
        $this->gameRoundRepository->save($this->lastGameRound);

        if ($this->lastGameRound->fighter_player === $this->lastGameRound->fighter_opponent) {
            $this->game->draws++;
        } elseif ($this->lastGameRound->is_win) {
            $this->game->wins++;
        } else {
            $this->game->loses++;
        }

        $this->gameRepository->save($this->game);
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
        if (!$this->game) {
            throw new Exception('Game not found');
        }
        $this->game->status_id = GameStatusEnum::FINISHED;
        $this->gameRepository->save($this->game);
    }

    protected function getRandomOpponent(): FighterEnum
    {
        $cases = FighterEnum::cases();
        return $cases[array_rand($cases)];
    }

    private function getWinsAgainst(FighterEnum $fighter): array
    {
        return match ($fighter) {
            FighterEnum::ROCK => [FighterEnum::SCISSORS, FighterEnum::LIZARD],
            FighterEnum::PAPER => [FighterEnum::ROCK, FighterEnum::SPOCK],
            FighterEnum::SCISSORS => [FighterEnum::PAPER, FighterEnum::LIZARD],
            FighterEnum::LIZARD => [FighterEnum::PAPER, FighterEnum::SPOCK],
            FighterEnum::SPOCK => [FighterEnum::ROCK, FighterEnum::SCISSORS],
        };
    }

    private function canWin(FighterEnum $player, FighterEnum $against): bool
    {
        return in_array($against, $this->getWinsAgainst($player));
    }

    public function getAction(FighterEnum $player, FighterEnum $opponent): string
    {
        return match (true) {
            $player === FighterEnum::ROCK && $opponent === FighterEnum::SCISSORS,
                $player === FighterEnum::ROCK && $opponent === FighterEnum::LIZARD => 'crushes',
            $player === FighterEnum::PAPER && $opponent === FighterEnum::ROCK => 'covers',
            $player === FighterEnum::PAPER && $opponent === FighterEnum::SPOCK => 'disproves',
            $player === FighterEnum::SCISSORS && $opponent === FighterEnum::PAPER => 'cuts',
            $player === FighterEnum::SCISSORS && $opponent === FighterEnum::LIZARD => 'decapitates',
            $player === FighterEnum::LIZARD && $opponent === FighterEnum::PAPER => 'eats',
            $player === FighterEnum::LIZARD && $opponent === FighterEnum::SPOCK => 'poisons',
            $player === FighterEnum::SPOCK && $opponent === FighterEnum::ROCK => 'vaporizes',
            $player === FighterEnum::SPOCK && $opponent === FighterEnum::SCISSORS => 'smashes',
            default => throw new InvalidArgumentException('Invalid combination'),
        };
    }
}
