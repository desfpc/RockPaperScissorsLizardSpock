<?php

namespace App\Repositories;

use App\Models\Game;

interface GameRepositoryInterface
{
    public function getActiveUserGame(?int $userId = null): Game;

    public function create(?int $userId): Game;

    public function save(Game $game): void;
}
