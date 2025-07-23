<?php

namespace App\Models;

use app\Enums\FighterEnum;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int|null $id
 * @property int|null $game_id
 * @property FighterEnum|null $fighter_player
 * @property FighterEnum|null $fighter_opponent
 * @property int $counter
 * @property bool is_win
 */
class GameRound extends Model
{
    protected $fillable = [
        'game_id',
        'fighter_player',
        'fighter_opponent',
        'counter',
        'is_win'
    ];

    protected $attributes = [
        'id' => null,
        'game_id' => null,
        'fighter_player' => null,
        'fighter_opponent' => null,
        'counter' => 0,
        'is_win' => false,
    ];

    protected $casts = [
        'fighter_player' => FighterEnum::class,
        'fighter_opponent' => FighterEnum::class,
        'counter' => 'integer',
        'is_win' => 'boolean',
    ];
}
