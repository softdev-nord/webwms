<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use InvalidArgumentException;

final readonly class QualityCheckAnswer
{
    public function __construct(
        private string $question,
        private bool $passed,
        private string $note
    ) {
        if (trim($question) === '' || mb_strlen($question) > 150 || mb_strlen($note) > 255) {
            throw new InvalidArgumentException('A quality check question or note has an invalid length.');
        }
    }

    public function question(): string
    {
        return trim($this->question);
    }

    public function passed(): bool
    {
        return $this->passed;
    }

    public function note(): string
    {
        return trim($this->note);
    }
}
