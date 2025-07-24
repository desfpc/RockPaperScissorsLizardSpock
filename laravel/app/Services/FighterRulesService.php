<?php

declare(strict_types=1);

namespace App\Services;

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
