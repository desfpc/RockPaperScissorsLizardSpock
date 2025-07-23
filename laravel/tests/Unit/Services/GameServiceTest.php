<?php

namespace Tests\Unit\Services;

use App\Enums\FighterEnum;
use App\Enums\GameStatusEnum;
use App\Models\Game;
use App\Models\GameRound;
use App\Repositories\GameRepositoryInterface;
use App\Repositories\GameRoundRepositoryInterface;
use App\Services\GameService;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;
use ReflectionMethod;
use Tests\TestCase;
use ReflectionClass;

class GameServiceTest extends TestCase
{
    private GameServiceMock $gameService;
    private MockObject $gameRepository;
    private MockObject $gameRoundRepository;
    private Game $game;
    private GameRound $gameRound;

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->gameRepository = $this->createMock(GameRepositoryInterface::class);
        $this->gameRoundRepository = $this->createMock(GameRoundRepositoryInterface::class);

        $this->game = new Game();
        $this->game->status_id = GameStatusEnum::PLAYING;
        $this->game->wins = 0;
        $this->game->loses = 0;
        $this->game->draws = 0;

        $this->gameRound = new GameRound();
        $this->gameRound->counter = 0;
        $this->gameRound->fighter_player = null;
        $this->gameRound->fighter_opponent = null;
        $this->gameRound->is_win = false;

        $this->gameService = new GameServiceMock($this->gameRepository, $this->gameRoundRepository);
    }

    public function testStartGame(): void
    {
        $this->gameRepository->expects($this->once())
            ->method('getActiveUserGame')
            ->willReturn($this->game);

        $this->gameRepository->expects($this->once())
            ->method('save')
            ->with($this->game);

        $this->gameService->startGame();
    }

    public function testPlayRoundThrowsExceptionWhenGameNotFound(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Game not found');

        $this->gameService->playRound(FighterEnum::ROCK);
    }

    /**
     * @throws Exception
     */
    public function testPlayRoundWithDraw(): void
    {
        $this->initializeTestGame();

        $this->gameService->setOpponentToReturn(FighterEnum::ROCK);

        $this->gameService->playRound(FighterEnum::ROCK);

        $this->assertEquals(1, $this->gameRound->counter);
        $this->assertEquals(FighterEnum::ROCK, $this->gameRound->fighter_player);
        $this->assertEquals(FighterEnum::ROCK, $this->gameRound->fighter_opponent);
        $this->assertFalse($this->gameRound->is_win);
        $this->assertEquals(1, $this->game->draws);
        $this->assertEquals(0, $this->game->wins);
        $this->assertEquals(0, $this->game->loses);
    }

    /**
     * @throws Exception
     */
    public function testPlayRoundWithWin(): void
    {
        $this->initializeTestGame();

        $this->gameService->setOpponentToReturn(FighterEnum::SCISSORS);

        $this->gameService->playRound(FighterEnum::ROCK);

        $this->assertEquals(1, $this->gameRound->counter);
        $this->assertEquals(FighterEnum::ROCK, $this->gameRound->fighter_player);
        $this->assertEquals(FighterEnum::SCISSORS, $this->gameRound->fighter_opponent);
        $this->assertTrue($this->gameRound->is_win);
        $this->assertEquals(0, $this->game->draws);
        $this->assertEquals(1, $this->game->wins);
        $this->assertEquals(0, $this->game->loses);
    }

    /**
     * @throws Exception
     */
    public function testPlayRoundWithLoss(): void
    {
        $this->initializeTestGame();

        $this->gameService->setOpponentToReturn(FighterEnum::PAPER);

        $this->gameService->playRound(FighterEnum::ROCK);

        $this->assertEquals(1, $this->gameRound->counter);
        $this->assertEquals(FighterEnum::ROCK, $this->gameRound->fighter_player);
        $this->assertEquals(FighterEnum::PAPER, $this->gameRound->fighter_opponent);
        $this->assertFalse($this->gameRound->is_win);
        $this->assertEquals(0, $this->game->draws);
        $this->assertEquals(0, $this->game->wins);
        $this->assertEquals(1, $this->game->loses);
    }

    public function testEndGameThrowsExceptionWhenGameNotFound(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Game not found');

        $this->gameService->endGame();
    }

    /**
     * @throws Exception
     */
    public function testEndGame(): void
    {
        $this->setGameProperty();

        $this->gameRepository->expects($this->once())
            ->method('save')
            ->with($this->equalTo($this->game));

        $this->gameService->endGame();

        $this->assertEquals(GameStatusEnum::FINISHED, $this->game->status_id);
    }

    /**
     * @throws ReflectionException
     */
    public function testGetRandomOpponent(): void
    {
        $method = $this->getPrivateMethod('getRandomOpponent');

        $result = $method->invoke($this->gameService);

        $this->assertInstanceOf(FighterEnum::class, $result);
        $this->assertContains($result, FighterEnum::cases());
    }

    /**
     * @throws ReflectionException
     */
    public function testGetWinsAgainst(): void
    {
        $method = $this->getPrivateMethod('getWinsAgainst');

        $this->assertEquals(
            [FighterEnum::SCISSORS, FighterEnum::LIZARD],
            $method->invoke($this->gameService, FighterEnum::ROCK)
        );
        $this->assertEquals(
            [FighterEnum::ROCK, FighterEnum::SPOCK],
            $method->invoke($this->gameService, FighterEnum::PAPER)
        );
        $this->assertEquals(
            [FighterEnum::PAPER, FighterEnum::LIZARD],
            $method->invoke($this->gameService, FighterEnum::SCISSORS)
        );
        $this->assertEquals(
            [FighterEnum::PAPER, FighterEnum::SPOCK],
            $method->invoke($this->gameService, FighterEnum::LIZARD)
        );
        $this->assertEquals(
            [FighterEnum::ROCK, FighterEnum::SCISSORS],
            $method->invoke($this->gameService, FighterEnum::SPOCK)
        );
    }

    /**
     * @throws ReflectionException
     */
    public function testGetActionThrowsExceptionForInvalidCombination(): void
    {
        $method = $this->getPrivateMethod('getAction');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid combination');

        $method->invoke($this->gameService, FighterEnum::ROCK, FighterEnum::PAPER);
    }

    private function initializeTestGame(): void
    {
        $this->setGameProperty();

        $this->gameRound = new GameRound();
        $this->gameRound->counter = 0;
        $this->gameRound->is_win = false;

        $this->gameRoundRepository->expects($this->once())
            ->method('getLastGameRound')
            ->willReturn($this->gameRound);

        $this->gameRepository->expects($this->once())
            ->method('save')
            ->with($this->equalTo($this->game));
    }

    private function setGameProperty(): void
    {
        $reflection = new ReflectionClass(GameService::class);
        $property = $reflection->getProperty('game');
        $property->setValue($this->gameService, $this->game);
    }

    /**
     * @throws ReflectionException
     */
    private function getPrivateMethod(string $methodName): ReflectionMethod
    {
        $reflection = new ReflectionClass(GameService::class);
        return $reflection->getMethod($methodName);
    }
}
