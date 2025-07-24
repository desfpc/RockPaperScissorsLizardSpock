<?php

declare(strict_types=1);

namespace App\Enums;

enum FighterActionEnum: string
{
    public const string INVALID = 'Invalid combination';

    case CRUSHES = 'crushes';
    case COVERS = 'covers';
    case DISPROVES = 'disproves';
    case CUTS = 'cuts';
    case DECAPITATES = 'decapitates';
    case EATS = 'eats';
    case POISONS = 'poisons';
    case VAPORIZES = 'vaporizes';
    case SMASHES = 'smashes';
}
