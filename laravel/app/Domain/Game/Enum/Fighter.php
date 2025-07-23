<?php

namespace App\Domain\Game\Enum;

enum Fighter: string
{
    case ROCK = 'rock';
    case PAPER = 'paper';
    case SCISSORS = 'scissors';
    case LIZARD = 'lizard';
    case SPOCK = 'spock';

    public function getWinsAgainst(): array
    {
        return match ($this) {
            self::ROCK => [self::SCISSORS, self::LIZARD],
            self::PAPER => [self::ROCK, self::SPOCK],
            self::SCISSORS => [self::PAPER, self::LIZARD],
            self::LIZARD => [self::PAPER, self::SPOCK],
            self::SPOCK => [self::ROCK, self::SCISSORS],
        };
    }

    public function getAction(Fighter $against): string
    {
        return match (true) {
            $this === self::ROCK && $against === self::SCISSORS,
                $this === self::ROCK && $against === self::LIZARD => 'crushes',
            $this === self::PAPER && $against === self::ROCK => 'covers',
            $this === self::PAPER && $against === self::SPOCK => 'disproves',
            $this === self::SCISSORS && $against === self::PAPER => 'cuts',
            $this === self::SCISSORS && $against === self::LIZARD => 'decapitates',
            $this === self::LIZARD && $against === self::PAPER => 'eats',
            $this === self::LIZARD && $against === self::SPOCK => 'poisons',
            $this === self::SPOCK && $against === self::ROCK => 'vaporizes',
            $this === self::SPOCK && $against === self::SCISSORS => 'smashes',
            default => throw new \InvalidArgumentException('Invalid combination'),
        };
    }

    public function canWin(Fighter $against): bool
    {
        return in_array($against, $this->getWinsAgainst());
    }
}
