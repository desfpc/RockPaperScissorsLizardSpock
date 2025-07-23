<?php

namespace Tests\Feature\Commands;

use App\Enums\FighterEnum;
use App\Models\Game;
use App\Models\GameRound;
use App\Services\GameService;
use Mockery;
use ReflectionException;
use Tests\TestCase;

class GameRpslsTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->bind(GameService::class, function () {
            $mock = Mockery::mock(GameService::class);
            $mock->shouldReceive('startGame')->once();
            $mock->shouldReceive('getGame')->andReturn(new Game());
            return $mock;
        });
    }

    public function testInputStringValidation(): void
    {
        $this->artisan('game:rpsls')
            ->expectsQuestion('Your choice', 'invalid')
            ->expectsOutputToContain('Invalid choice:')
            ->expectsQuestion('Your choice', '0')
            ->assertExitCode(0);
    }

    public function testInputOutOfRangeValidation(): void
    {
        $this->artisan('game:rpsls')
            ->expectsQuestion('Your choice', '10')
            ->expectsOutputToContain('Invalid choice:')
            ->expectsQuestion('Your choice', '0')
            ->assertExitCode(0);
    }

    /**
     * @throws ReflectionException
     */
    public function testPlayRound(): void
    {
        $this->app->bind(GameService::class, function () {
            $mock = Mockery::mock(GameService::class);
            $mock->shouldReceive('startGame')->once();

            $game = new Game();
            $mock->shouldReceive('getGame')->andReturn($game);

            $round = new GameRound();
            $round->fighter_player = FighterEnum::ROCK;
            $round->fighter_opponent = FighterEnum::SCISSORS;
            $round->is_win = true;

            $mock->shouldReceive('playRound')
                ->once()
                ->with(FighterEnum::ROCK);

            $mock->shouldReceive('getLastGameRound')->andReturn($round);
            $mock->shouldReceive('getAction')->andReturn('crushes');

            return $mock;
        });

        $this->artisan('game:rpsls')
            ->expectsQuestion('Your choice', '1') // Choose Rock
            ->expectsQuestion('Your choice', '0')
            ->assertExitCode(0);
    }

    /**
     * @throws ReflectionException
     */
    public function testGameEnd(): void
    {
        $this->app->bind(GameService::class, function () {
            $mock = Mockery::mock(GameService::class);
            $mock->shouldReceive('startGame')->once();

            $game = new Game();
            $mock->shouldReceive('getGame')->andReturn($game);

            $mock->shouldReceive('endGame')->once();

            return $mock;
        });

        $this->artisan('game:rpsls')
            ->expectsQuestion('Your choice', '0')
            ->expectsOutputToContain('Thanks for playing!')
            ->assertExitCode(0);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
