<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Models\Game;
use App\Models\GameRound;
use Illuminate\Console\Command;

interface GameRpslsPresenterInterface
{
    public function setCommand(Command $command): void;
    public function displayWelcomeMessage(): void;
    public function displayMenu(array $fighterOptions): void;
    public function askForChoice(): string;
    public function displayValidationErrors(array $errors): void;
    public function displayFinalStats(Game $game): void;
    public function displayThanksMessage(): void;
    public function showRoundResults(GameRound $round, string $action): void;
    public function showGameScores(Game $game): void;
    public function displayError(string $message): void;
}
