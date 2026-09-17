<?php

declare(strict_types=1);

namespace WebWMS\Modules\Article\Application\Query;

use WebWMS\Shared\Dto\Common\ListResultDto;
use WebWMS\Shared\Dto\Common\PaginationDto;

readonly class GetArticleOverviewQueryHandler
{
    public function __construct(
        private ArticleOverviewReadModelRepositoryInterface $repository,
    ) {
    }

    public function handle(GetArticleOverviewQuery $query): ListResultDto
    {
        $pagination = new PaginationDto(
            page: $query->getPage(),
            limit: $query->getLimit(),
        );

        return $this->repository->findOverview(
            pagination: $pagination,
            searchTerm: $query->getSearchTerm(),
        );
    }
}

