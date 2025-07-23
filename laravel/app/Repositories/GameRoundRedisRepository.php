<?php

namespace App\Repositories;

use App\Models\GameRound;
use Illuminate\Support\Facades\Cache;

class GameRoundRedisRepository implements GameRoundRepositoryInterface
{
    private const string CACHE_GAME_ROUND_KEY = 'game_round:';
    private const string CACHE_NULL_GAME_ID = 'console';

    public function getLastGameRound(?int $gameId): GameRound
    {
        $key = $this->getGameRoundKey($gameId);
        if (Cache::has($key)) {
            return new GameRound(Cache::get($key));
        }
        return $this->create($gameId);
    }

    public function save(GameRound $gameRound): void
    {
        $key = $this->getGameRoundKey($gameRound->game_id);
        Cache::set($key, $gameRound->toArray());
    }

    public function create(?int $gameId): GameRound
    {
        return new GameRound(['game_id' => $gameId]);
    }

    private function getGameRoundKey(?int $gameId): string
    {
        return self::CACHE_GAME_ROUND_KEY . ($gameId ?? self::CACHE_NULL_GAME_ID);
    }
}
