<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GameRpsls extends Command
{
    protected $signature = 'game:rpsls';
    protected $description = 'Play Rock Paper Scissors Lizard Spock 1st player against the computer';

    public function handle(): void
    {
        $this->info('Welcome to Rock Paper Scissors Lizard Spock game!');
    }
}
