<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Throwable;

class GameException extends Exception
{
    public const string ERROR_PREFIX = 'GAME ERROR';
    public const string ERROR_VALIDATION_PREFIX = 'GAME VALIDATION ERROR';

    private string $prefix;

    public function __construct(
        string $message,
        string $prefix = self::ERROR_PREFIX,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        $this->prefix = $prefix;
        parent::__construct($message, $code, $previous);
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }
}
