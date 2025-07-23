<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\GameRepositoryInterface;

class GameService
{
    public function __construct(private GameRepositoryInterface $gameRepository)
    {
    }
}
