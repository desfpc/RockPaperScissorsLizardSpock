<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Game;
use Illuminate\Support\Facades\Cache;

class GameRedisRepository implements GameRepositoryInterface
{
    private const string CACHE_GAME_KEY = 'game:';
    private const string CACHE_NULL_PLAYER_ID = 'console';

    public function getActiveUserGame(?int $userId): Game
    {
        $game = null;
        $key = $this->getGameKey($userId);
        if (Cache::has($key)) {
            $game = new Game(Cache::get($key));
            if ($game->isFinished()) {
                $game = null;
            }
        }

        return $game ?? $this->create($userId);
    }

    public function create(?int $userId): Game
    {
        return new Game(['user_id' => $userId]);
    }

    public function save(Game $game): void
    {
        $key = $this->getGameKey($game->user_id);
        Cache::set($key, $game->toArray());
    }

    private function getGameKey(?int $userId): string
    {
        return self::CACHE_GAME_KEY . ($userId ?? self::CACHE_NULL_PLAYER_ID);
    }
}
