<?php

namespace Tests\Unit\Services;

use App\Enums\FighterActionEnum;
use App\Enums\FighterEnum;
use App\Services\FighterRulesService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class FighterRulesServiceTest extends TestCase
{
    private FighterRulesService $fighterRulesService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fighterRulesService = new FighterRulesService();
    }

    public function testGetWinsAgainst(): void
    {
        $this->assertEquals(
            [FighterEnum::SCISSORS, FighterEnum::LIZARD],
            $this->fighterRulesService->getWinsAgainst(FighterEnum::ROCK)
        );
        $this->assertEquals(
            [FighterEnum::ROCK, FighterEnum::SPOCK],
            $this->fighterRulesService->getWinsAgainst(FighterEnum::PAPER)
        );
        $this->assertEquals(
            [FighterEnum::PAPER, FighterEnum::LIZARD],
            $this->fighterRulesService->getWinsAgainst(FighterEnum::SCISSORS)
        );
        $this->assertEquals(
            [FighterEnum::PAPER, FighterEnum::SPOCK],
            $this->fighterRulesService->getWinsAgainst(FighterEnum::LIZARD)
        );
        $this->assertEquals(
            [FighterEnum::ROCK, FighterEnum::SCISSORS],
            $this->fighterRulesService->getWinsAgainst(FighterEnum::SPOCK)
        );
    }

    public function testCanWin(): void
    {
        $this->assertTrue($this->fighterRulesService->canWin(FighterEnum::ROCK, FighterEnum::SCISSORS));
        $this->assertTrue($this->fighterRulesService->canWin(FighterEnum::ROCK, FighterEnum::LIZARD));
        $this->assertFalse($this->fighterRulesService->canWin(FighterEnum::ROCK, FighterEnum::PAPER));
        $this->assertFalse($this->fighterRulesService->canWin(FighterEnum::ROCK, FighterEnum::SPOCK));
        $this->assertFalse($this->fighterRulesService->canWin(FighterEnum::ROCK, FighterEnum::ROCK));
    }

    public function testGetAction(): void
    {
        $this->assertEquals(
            FighterActionEnum::CRUSHES,
            $this->fighterRulesService->getAction(FighterEnum::ROCK, FighterEnum::SCISSORS)
        );
        $this->assertEquals(
            FighterActionEnum::CRUSHES,
            $this->fighterRulesService->getAction(FighterEnum::ROCK, FighterEnum::LIZARD)
        );
        $this->assertEquals(
            FighterActionEnum::COVERS,
            $this->fighterRulesService->getAction(FighterEnum::PAPER, FighterEnum::ROCK)
        );
        $this->assertEquals(
            FighterActionEnum::DISPROVES,
            $this->fighterRulesService->getAction(FighterEnum::PAPER, FighterEnum::SPOCK)
        );
        $this->assertEquals(
            FighterActionEnum::CUTS,
            $this->fighterRulesService->getAction(FighterEnum::SCISSORS, FighterEnum::PAPER)
        );
        $this->assertEquals(
            FighterActionEnum::DECAPITATES,
            $this->fighterRulesService->getAction(FighterEnum::SCISSORS, FighterEnum::LIZARD)
        );
        $this->assertEquals(
            FighterActionEnum::EATS,
            $this->fighterRulesService->getAction(FighterEnum::LIZARD, FighterEnum::PAPER)
        );
        $this->assertEquals(
            FighterActionEnum::POISONS,
            $this->fighterRulesService->getAction(FighterEnum::LIZARD, FighterEnum::SPOCK)
        );
        $this->assertEquals(
            FighterActionEnum::VAPORIZES,
            $this->fighterRulesService->getAction(FighterEnum::SPOCK, FighterEnum::ROCK)
        );
        $this->assertEquals(
            FighterActionEnum::SMASHES,
            $this->fighterRulesService->getAction(FighterEnum::SPOCK, FighterEnum::SCISSORS)
        );
    }

    public function testGetActionThrowsExceptionForInvalidCombination(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid combination');

        $this->fighterRulesService->getAction(FighterEnum::ROCK, FighterEnum::PAPER);
    }
}
