<?php

namespace App\Models;

use App\Enums\GameStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int|null $id
 * @property int|null $user_id
 * @property GameStatusEnum $status_id
 * @property int $wins
 * @property int $loses
 * @property int $draws
 */
class Game extends Model
{
    protected $fillable = [
        'user_id',
        'status_id',
        'wins',
        'loses',
        'draws',
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'status_id' => GameStatusEnum::class,
        'wins' => 'integer',
        'loses' => 'integer',
        'draws' => 'integer',
    ];

    protected $attributes = [
        'id' => null,
        'user_id' => null,
        'status_id' => GameStatusEnum::PLAYING,
        'wins' => 0,
        'loses' => 0,
        'draws' => 0,
    ];

    public function isFinished(): bool
    {
        return $this->status_id === GameStatusEnum::FINISHED;
    }

    public function rounds(): HasMany
    {
        return $this->hasMany(GameRound::class);
    }
}
