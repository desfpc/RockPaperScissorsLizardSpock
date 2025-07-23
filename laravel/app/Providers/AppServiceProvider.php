<?php

namespace App\Providers;

use App\Repositories\GameRedisRepository;
use App\Repositories\GameRepositoryInterface;
use App\Repositories\GameRoundRedisRepository;
use App\Repositories\GameRoundRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(GameRepositoryInterface::class, GameRedisRepository::class);
        $this->app->bind(GameRoundRepositoryInterface::class, GameRoundRedisRepository::class);
    }
}
