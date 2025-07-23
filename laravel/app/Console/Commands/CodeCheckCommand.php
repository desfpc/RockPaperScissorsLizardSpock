<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class CodeCheckCommand extends Command
{
    protected $signature = 'code:check {path?}';
    protected $description = 'PHP Code Sniffer Check';

    public function handle(): ?int
    {
        $path = $this->argument('path') ?? './app';

        $process = new Process([
            './vendor/bin/phpcs',
            '--standard=PSR12',
            '--colors',
            '-p',
            $path
        ]);

        $process->setTty(true);
        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        return $process->getExitCode();
    }
}
