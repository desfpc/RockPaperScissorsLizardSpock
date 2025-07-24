<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\FighterActionEnum;
use App\Enums\FighterEnum;
use InvalidArgumentException;

class FighterRulesService
{
    public function getWinsAgainst(FighterEnum $fighter): array
    {
        return match ($fighter) {
            FighterEnum::ROCK => [FighterEnum::SCISSORS, FighterEnum::LIZARD],
            FighterEnum::PAPER => [FighterEnum::ROCK, FighterEnum::SPOCK],
            FighterEnum::SCISSORS => [FighterEnum::PAPER, FighterEnum::LIZARD],
            FighterEnum::LIZARD => [FighterEnum::PAPER, FighterEnum::SPOCK],
            FighterEnum::SPOCK => [FighterEnum::ROCK, FighterEnum::SCISSORS],
        };
    }

    public function canWin(FighterEnum $player, FighterEnum $against): bool
    {
        return in_array($against, $this->getWinsAgainst($player));
    }

    public function getAction(FighterEnum $player, FighterEnum $opponent): FighterActionEnum
    {
        return match (true) {
            $player === FighterEnum::ROCK && $opponent === FighterEnum::SCISSORS,
                $player === FighterEnum::ROCK && $opponent === FighterEnum::LIZARD => FighterActionEnum::CRUSHES,
            $player === FighterEnum::PAPER && $opponent === FighterEnum::ROCK => FighterActionEnum::COVERS,
            $player === FighterEnum::PAPER && $opponent === FighterEnum::SPOCK => FighterActionEnum::DISPROVES,
            $player === FighterEnum::SCISSORS && $opponent === FighterEnum::PAPER => FighterActionEnum::CUTS,
            $player === FighterEnum::SCISSORS && $opponent === FighterEnum::LIZARD => FighterActionEnum::DECAPITATES,
            $player === FighterEnum::LIZARD && $opponent === FighterEnum::PAPER => FighterActionEnum::EATS,
            $player === FighterEnum::LIZARD && $opponent === FighterEnum::SPOCK => FighterActionEnum::POISONS,
            $player === FighterEnum::SPOCK && $opponent === FighterEnum::ROCK => FighterActionEnum::VAPORIZES,
            $player === FighterEnum::SPOCK && $opponent === FighterEnum::SCISSORS => FighterActionEnum::SMASHES,
            default => throw new InvalidArgumentException(FighterActionEnum::INVALID),
        };
    }
}
