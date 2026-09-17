<?php

declare(strict_types=1);

namespace WebWMS\Modules\Article\Application\Query;

use WebWMS\Shared\Dto\Common\ListResultDto;
use WebWMS\Shared\Dto\Common\PaginationDto;

readonly class GetArticleOverviewQuery
{
    public function __construct(
        private int $page = 1,
        private int $limit = 50,
        private ?string $searchTerm = null,
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

    public function getSearchTerm(): ?string
    {
        return $this->searchTerm;
    }
}

