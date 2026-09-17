<?php

declare(strict_types=1);

namespace WebWMS\Modules\Article\Infrastructure\Query;

use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityManagerInterface;
use WebWMS\Modules\Article\Application\Query\ArticleOverviewReadModelRepositoryInterface;
use WebWMS\Modules\Article\Application\Query\View\ArticleOverviewItemView;
use WebWMS\Shared\Dto\Common\ListResultDto;
use WebWMS\Shared\Dto\Common\PaginationDto;

readonly class DoctrineArticleOverviewReadModelRepository implements ArticleOverviewReadModelRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * Optimierte Query ohne N+1-Probleme durch native SQL mit Join
     */
    public function findOverview(
        PaginationDto $pagination,
        ?string $searchTerm = null,
    ): ListResultDto {
        $baseFrom = '
            FROM article a
            LEFT JOIN stock_occupancy so ON a.article_id = so.article_id
        ';

        $whereSql = '';
        $countParams = [];
        $countTypes = [];

        if ($searchTerm !== null && $searchTerm !== '') {
            $whereSql = ' WHERE (a.article_nr LIKE :search OR a.article_name LIKE :search)';
            $countParams['search'] = "%{$searchTerm}%";
            $countTypes['search'] = ParameterType::STRING;
        }

        $countSql = 'SELECT COUNT(DISTINCT a.article_id) as cnt ' . $baseFrom . $whereSql;

        $total = (int) ($this->entityManager->getConnection()
            ->executeQuery($countSql, $countParams, $countTypes)
            ->fetchOne() ?: 0);

        $dataSql = '
            SELECT
                a.article_id,
                a.article_nr,
                a.article_name,
                COALESCE(SUM(so.in_stock), 0) as total_stock,
                COALESCE(SUM(so.incoming_stock), 0) as incoming_stock,
                COUNT(DISTINCT so.stock_location_id) as location_count,
                a.created_at,
                a.updated_at
        ' . $baseFrom . $whereSql . '
            GROUP BY a.article_id, a.article_nr, a.article_name, a.created_at, a.updated_at
            ORDER BY a.article_nr ASC
            LIMIT :limit OFFSET :offset
        ';

        $dataParams = $countParams;
        $dataTypes = $countTypes;
        $dataParams['limit'] = $pagination->getLimit();
        $dataParams['offset'] = $pagination->getOffset();
        $dataTypes['limit'] = ParameterType::INTEGER;
        $dataTypes['offset'] = ParameterType::INTEGER;

        $result = $this->entityManager->getConnection()
            ->executeQuery($dataSql, $dataParams, $dataTypes)
            ->fetchAllAssociative();

        $items = array_map(
            fn(array $row) => (new ArticleOverviewItemView())
                ->setArticleId((int) $row['article_id'])
                ->setArticleNr((string) $row['article_nr'])
                ->setArticleName((string) $row['article_name'])
                ->setArticleDescription((string) ($row['article_description'] ?? ''))
                ->setTotalStock((float) ($row['total_stock'] ?? 0))
                ->setIncomingStock((float) ($row['incoming_stock'] ?? 0))
                ->setLocationCount((int) ($row['location_count'] ?? 0)),
            $result
        );

        return (new ListResultDto())
            ->setItems($items)
            ->setTotal($total)
            ->setPage($pagination->getPage())
            ->setLimit($pagination->getLimit());
    }
}
