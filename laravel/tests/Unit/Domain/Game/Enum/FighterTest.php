<?php

namespace Tests\Unit\Domain\Game\Enum;

use app\Enums\FighterEnum;
use PHPUnit\Framework\TestCase;

class FighterTest extends TestCase
{
    public function testGetWinsAgainstReturnsCorrectArray(): void
    {
        $this->assertSame([FighterEnum::SCISSORS, FighterEnum::LIZARD], FighterEnum::ROCK->getWinsAgainst());
        $this->assertSame([FighterEnum::ROCK, FighterEnum::SPOCK], FighterEnum::PAPER->getWinsAgainst());
        $this->assertSame([FighterEnum::PAPER, FighterEnum::LIZARD], FighterEnum::SCISSORS->getWinsAgainst());
        $this->assertSame([FighterEnum::PAPER, FighterEnum::SPOCK], FighterEnum::LIZARD->getWinsAgainst());
        $this->assertSame([FighterEnum::ROCK, FighterEnum::SCISSORS], FighterEnum::SPOCK->getWinsAgainst());
    }

    public function testGetActionReturnsCorrectString(): void
    {
        // ROCK actions
        $this->assertSame('crushes', FighterEnum::ROCK->getAction(FighterEnum::SCISSORS));
        $this->assertSame('crushes', FighterEnum::ROCK->getAction(FighterEnum::LIZARD));

        // PAPER actions
        $this->assertSame('covers', FighterEnum::PAPER->getAction(FighterEnum::ROCK));
        $this->assertSame('disproves', FighterEnum::PAPER->getAction(FighterEnum::SPOCK));

        // SCISSORS actions
        $this->assertSame('cuts', FighterEnum::SCISSORS->getAction(FighterEnum::PAPER));
        $this->assertSame('decapitates', FighterEnum::SCISSORS->getAction(FighterEnum::LIZARD));

        // LIZARD actions
        $this->assertSame('eats', FighterEnum::LIZARD->getAction(FighterEnum::PAPER));
        $this->assertSame('poisons', FighterEnum::LIZARD->getAction(FighterEnum::SPOCK));

        // SPOCK actions
        $this->assertSame('vaporizes', FighterEnum::SPOCK->getAction(FighterEnum::ROCK));
        $this->assertSame('smashes', FighterEnum::SPOCK->getAction(FighterEnum::SCISSORS));
    }

    public function testGetActionThrowsExceptionForInvalidCombination(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid combination');

        // ROCK cannot win against PAPER, so this should throw an exception
        FighterEnum::ROCK->getAction(FighterEnum::PAPER);
    }

    public function testCanWinReturnsCorrectAnswer(): void
    {
        // ROCK
        $this->assertTrue(FighterEnum::ROCK->canWin(FighterEnum::SCISSORS));
        $this->assertTrue(FighterEnum::ROCK->canWin(FighterEnum::LIZARD));
        $this->assertFalse(FighterEnum::ROCK->canWin(FighterEnum::PAPER));
        $this->assertFalse(FighterEnum::ROCK->canWin(FighterEnum::SPOCK));
        $this->assertFalse(FighterEnum::ROCK->canWin(FighterEnum::ROCK));

        // PAPER
        $this->assertTrue(FighterEnum::PAPER->canWin(FighterEnum::ROCK));
        $this->assertTrue(FighterEnum::PAPER->canWin(FighterEnum::SPOCK));
        $this->assertFalse(FighterEnum::PAPER->canWin(FighterEnum::SCISSORS));
        $this->assertFalse(FighterEnum::PAPER->canWin(FighterEnum::LIZARD));
        $this->assertFalse(FighterEnum::PAPER->canWin(FighterEnum::PAPER));

        // SCISSORS
        $this->assertTrue(FighterEnum::SCISSORS->canWin(FighterEnum::PAPER));
        $this->assertTrue(FighterEnum::SCISSORS->canWin(FighterEnum::LIZARD));
        $this->assertFalse(FighterEnum::SCISSORS->canWin(FighterEnum::ROCK));
        $this->assertFalse(FighterEnum::SCISSORS->canWin(FighterEnum::SPOCK));
        $this->assertFalse(FighterEnum::SCISSORS->canWin(FighterEnum::SCISSORS));

        // LIZARD
        $this->assertTrue(FighterEnum::LIZARD->canWin(FighterEnum::PAPER));
        $this->assertTrue(FighterEnum::LIZARD->canWin(FighterEnum::SPOCK));
        $this->assertFalse(FighterEnum::LIZARD->canWin(FighterEnum::ROCK));
        $this->assertFalse(FighterEnum::LIZARD->canWin(FighterEnum::SCISSORS));
        $this->assertFalse(FighterEnum::LIZARD->canWin(FighterEnum::LIZARD));

        // SPOCK
        $this->assertTrue(FighterEnum::SPOCK->canWin(FighterEnum::ROCK));
        $this->assertTrue(FighterEnum::SPOCK->canWin(FighterEnum::SCISSORS));
        $this->assertFalse(FighterEnum::SPOCK->canWin(FighterEnum::PAPER));
        $this->assertFalse(FighterEnum::SPOCK->canWin(FighterEnum::LIZARD));
        $this->assertFalse(FighterEnum::SPOCK->canWin(FighterEnum::SPOCK));
    }
}
