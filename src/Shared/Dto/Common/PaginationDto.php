<?php

declare(strict_types=1);

namespace WebWMS\Shared\Dto\Common;

class PaginationDto
{
    public function __construct(
        private int $page = 1,
        private int $limit = 50,
    ) {
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->limit;
    }
}

