<?php

declare(strict_types=1);

namespace App\Enums;

enum GameStatusEnum: int
{
    case PLAYING = 1;
    case FINISHED = 2;
}
