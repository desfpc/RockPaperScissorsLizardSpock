<?php

namespace App\Providers;

use App\Repositories\GameRedisRepository;
use App\Repositories\GameRepositoryInterface;
use App\Repositories\GameRoundRedisRepository;
use App\Repositories\GameRoundRepositoryInterface;
use App\Presenters\GameRpslsPresenter;
use App\Presenters\GameRpslsPresenterInterface;
use App\Services\FighterRulesService;
use App\Services\GameService;
use App\Services\GameServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(GameRepositoryInterface::class, GameRedisRepository::class);
        $this->app->bind(GameRoundRepositoryInterface::class, GameRoundRedisRepository::class);
        $this->app->bind(GameRpslsPresenterInterface::class, GameRpslsPresenter::class);
        $this->app->bind(GameServiceInterface::class, GameService::class);

        $this->app->singleton(FighterRulesService::class);
    }
}
