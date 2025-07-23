<?php

namespace App\Repositories;

use App\Models\GameRound;

interface GameRoundRepositoryInterface
{
    public function getLastGameRound(?int $gameId = null): GameRound;

    public function save(GameRound $gameRound): void;

    public function create(?int $gameId): GameRound;
}
