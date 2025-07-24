<?php

namespace Tests\Unit\Services;

use App\Enums\FighterEnum;
use App\Repositories\GameRepositoryInterface;
use App\Repositories\GameRoundRepositoryInterface;
use App\Services\FighterRulesService;
use App\Services\GameService;

class GameServiceMock extends GameService
{
    private FighterEnum $opponentToReturn;

    public function __construct(
        GameRepositoryInterface $gameRepository,
        GameRoundRepositoryInterface $gameRoundRepository,
        ?FighterRulesService $fighterRulesService = null
    ) {
        parent::__construct(
            $gameRepository,
            $gameRoundRepository,
            $fighterRulesService ?? new FighterRulesService()
        );
    }

    public function setOpponentToReturn(FighterEnum $opponent): void
    {
        $this->opponentToReturn = $opponent;
    }

    protected function getRandomOpponent(): FighterEnum
    {
        return $this->opponentToReturn;
    }
}
