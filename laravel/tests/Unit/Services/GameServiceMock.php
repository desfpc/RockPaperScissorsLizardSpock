<?php

namespace Tests\Unit\Services;

use App\Enums\FighterEnum;
use App\Services\GameService;

class GameServiceMock extends GameService
{
    private FighterEnum $opponentToReturn;

    public function setOpponentToReturn(FighterEnum $opponent): void
    {
        $this->opponentToReturn = $opponent;
    }

    protected function getRandomOpponent(): FighterEnum
    {
        return $this->opponentToReturn;
    }
}
