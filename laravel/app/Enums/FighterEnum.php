<?php

declare(strict_types=1);

namespace App\Enums;

enum FighterEnum: string
{
    case ROCK = 'rock';
    case PAPER = 'paper';
    case SCISSORS = 'scissors';
    case LIZARD = 'lizard';
    case SPOCK = 'spock';

    public function getName(): string
    {
        return mb_convert_case($this->value, MB_CASE_TITLE, "UTF-8");
    }
}
