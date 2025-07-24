<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\FighterEnum;
use App\Models\Game;
use App\Models\GameRound;

interface GameServiceInterface
{
    public function startGame(): void;
    public function playRound(FighterEnum $playerFighter): void;
    public function getLastGameRound(): ?GameRound;
    public function getGame(): ?Game;
    public function endGame(): void;
    public function getAction(FighterEnum $player, FighterEnum $opponent): string;
}
