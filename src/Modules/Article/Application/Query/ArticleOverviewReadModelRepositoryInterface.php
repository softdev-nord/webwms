<?php

declare(strict_types=1);

namespace WebWMS\Modules\Article\Application\Query;

use WebWMS\Shared\Dto\Common\ListResultDto;
use WebWMS\Shared\Dto\Common\PaginationDto;

interface ArticleOverviewReadModelRepositoryInterface
{
    /**
     * Findet Artikel-Übersicht mit optimierter Query
     */
    public function findOverview(
        PaginationDto $pagination,
        ?string $searchTerm = null,
    ): ListResultDto;
}

