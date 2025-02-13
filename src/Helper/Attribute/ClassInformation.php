<?php

declare(strict_types=1);

namespace WebWMS\Helper\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class ClassInformation
{
    public function __construct(
        private ?string $package = null,
        private ?string $author = null,
        private ?string $copyright = null,
        private ?string $class = null,
        private ?string $interface = null,
        private ?string $trait = null,
        private ?string $covers = null
    ) {
    }

    public function getPackage(): ?string
    {
        return $this->package;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function getCopyright(): ?string
    {
        return $this->copyright;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function getInterface(): ?string
    {
        return $this->interface;
    }

    public function getTrait(): ?string
    {
        return $this->trait;
    }

    public function getCovers(): ?string
    {
        return $this->covers;
    }
}
