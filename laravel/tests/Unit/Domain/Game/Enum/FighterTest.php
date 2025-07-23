<?php

namespace Tests\Unit\Domain\Game\Enum;

use App\Domain\Game\Enum\Fighter;
use PHPUnit\Framework\TestCase;

class FighterTest extends TestCase
{
    public function testGetWinsAgainstReturnsCorrectArray(): void
    {
        $this->assertSame([Fighter::SCISSORS, Fighter::LIZARD], Fighter::ROCK->getWinsAgainst());
        $this->assertSame([Fighter::ROCK, Fighter::SPOCK], Fighter::PAPER->getWinsAgainst());
        $this->assertSame([Fighter::PAPER, Fighter::LIZARD], Fighter::SCISSORS->getWinsAgainst());
        $this->assertSame([Fighter::PAPER, Fighter::SPOCK], Fighter::LIZARD->getWinsAgainst());
        $this->assertSame([Fighter::ROCK, Fighter::SCISSORS], Fighter::SPOCK->getWinsAgainst());
    }

    public function testGetActionReturnsCorrectString(): void
    {
        // ROCK actions
        $this->assertSame('crushes', Fighter::ROCK->getAction(Fighter::SCISSORS));
        $this->assertSame('crushes', Fighter::ROCK->getAction(Fighter::LIZARD));

        // PAPER actions
        $this->assertSame('covers', Fighter::PAPER->getAction(Fighter::ROCK));
        $this->assertSame('disproves', Fighter::PAPER->getAction(Fighter::SPOCK));

        // SCISSORS actions
        $this->assertSame('cuts', Fighter::SCISSORS->getAction(Fighter::PAPER));
        $this->assertSame('decapitates', Fighter::SCISSORS->getAction(Fighter::LIZARD));

        // LIZARD actions
        $this->assertSame('eats', Fighter::LIZARD->getAction(Fighter::PAPER));
        $this->assertSame('poisons', Fighter::LIZARD->getAction(Fighter::SPOCK));

        // SPOCK actions
        $this->assertSame('vaporizes', Fighter::SPOCK->getAction(Fighter::ROCK));
        $this->assertSame('smashes', Fighter::SPOCK->getAction(Fighter::SCISSORS));
    }

    public function testGetActionThrowsExceptionForInvalidCombination(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid combination');

        // ROCK cannot win against PAPER, so this should throw an exception
        Fighter::ROCK->getAction(Fighter::PAPER);
    }

    public function testCanWinReturnsCorrectAnswer(): void
    {
        // ROCK
        $this->assertTrue(Fighter::ROCK->canWin(Fighter::SCISSORS));
        $this->assertTrue(Fighter::ROCK->canWin(Fighter::LIZARD));
        $this->assertFalse(Fighter::ROCK->canWin(Fighter::PAPER));
        $this->assertFalse(Fighter::ROCK->canWin(Fighter::SPOCK));
        $this->assertFalse(Fighter::ROCK->canWin(Fighter::ROCK));

        // PAPER
        $this->assertTrue(Fighter::PAPER->canWin(Fighter::ROCK));
        $this->assertTrue(Fighter::PAPER->canWin(Fighter::SPOCK));
        $this->assertFalse(Fighter::PAPER->canWin(Fighter::SCISSORS));
        $this->assertFalse(Fighter::PAPER->canWin(Fighter::LIZARD));
        $this->assertFalse(Fighter::PAPER->canWin(Fighter::PAPER));

        // SCISSORS
        $this->assertTrue(Fighter::SCISSORS->canWin(Fighter::PAPER));
        $this->assertTrue(Fighter::SCISSORS->canWin(Fighter::LIZARD));
        $this->assertFalse(Fighter::SCISSORS->canWin(Fighter::ROCK));
        $this->assertFalse(Fighter::SCISSORS->canWin(Fighter::SPOCK));
        $this->assertFalse(Fighter::SCISSORS->canWin(Fighter::SCISSORS));

        // LIZARD
        $this->assertTrue(Fighter::LIZARD->canWin(Fighter::PAPER));
        $this->assertTrue(Fighter::LIZARD->canWin(Fighter::SPOCK));
        $this->assertFalse(Fighter::LIZARD->canWin(Fighter::ROCK));
        $this->assertFalse(Fighter::LIZARD->canWin(Fighter::SCISSORS));
        $this->assertFalse(Fighter::LIZARD->canWin(Fighter::LIZARD));

        // SPOCK
        $this->assertTrue(Fighter::SPOCK->canWin(Fighter::ROCK));
        $this->assertTrue(Fighter::SPOCK->canWin(Fighter::SCISSORS));
        $this->assertFalse(Fighter::SPOCK->canWin(Fighter::PAPER));
        $this->assertFalse(Fighter::SPOCK->canWin(Fighter::LIZARD));
        $this->assertFalse(Fighter::SPOCK->canWin(Fighter::SPOCK));
    }
}
