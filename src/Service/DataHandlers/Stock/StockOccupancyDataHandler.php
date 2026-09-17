<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\Stock;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\Article;
use WebWMS\Entity\StockOccupancy;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\DataHandlers\Article\ArticleDataHandler;

#[ClassInformation(
    package: 'WebWMS\Service\DataHandlers\Stock',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockOccupancyDataHandler'
)]
readonly class StockOccupancyDataHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function save(StockOccupancy $stockOccupancy): void
    {
        $this->entityManager->persist($stockOccupancy);
        $this->entityManager->flush();
    }

    public function update(StockOccupancy $stockOccupancy): void
    {
        $this->entityManager->persist($stockOccupancy);
        $this->entityManager->flush();
    }

    public function delete(StockOccupancy $stockOccupancy): void
    {
        $this->entityManager->remove($stockOccupancy);
        $this->entityManager->flush();
    }

    /**
     * @throws Exception
     * @return array<int, array<string, mixed>>
     *
     * @SuppressWarnings(ElseExpression)
     */
    public function getStockOccupancy(int $stockLocationLn): array
    {
        $allResults = [];
        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();

        $queryBuilder
            ->select('
            sl.stock_location_coordinate AS koordinate,
            sl.stock_location_ln AS ln,
            sl.stock_location_fb AS fb,
            sl.stock_location_sp AS sp,
            sl.stock_location_tf AS tf,
            sl.stock_location_desc,
            (SELECT SUM((SELECT IF(tr_type = 1, tr_quantity, 0.000))) FROM transport_history WHERE stock_coordinate = tph.stock_coordinate GROUP BY stock_coordinate LIMIT 1) - (SELECT SUM((SELECT IF(tr_type = 2, tr_quantity, 0.000))) FROM transport_history WHERE stock_coordinate = tph.stock_coordinate GROUP BY stock_coordinate LIMIT 1) AS lp_bestand')
            ->from('transport_history', 'tph')
            ->rightJoin('tph', 'stock_location', 'sl', 'tph.stock_coordinate = sl.stock_location_coordinate')
            ->andWhere('sl.stock_location_ln = :stock_location_ln')
            ->setParameter('stock_location_ln', $stockLocationLn)
            ->groupBy('sl.stock_location_coordinate');

        $stmt = $queryBuilder->executeQuery();

        $results = $stmt->fetchAllAssociative();

        foreach ($results as $result) {
            if ($result['lp_bestand'] > 0) {
                $allResults[] = [
                    'ln' => $result['ln'],
                    'fb' => $result['fb'],
                    'sp' => $result['sp'],
                    'tf' => $result['tf'],
                    'lnKomplett' => $result['ln'] . '-' . $result['fb'] . '-' . $result['sp'] . '-' . $result['tf'],
                    'koordinate' => $result['koordinate'],
                    'system' => $result['stock_location_desc'],
                    'belegt' => true,
                ];
            } else {
                $allResults[] = [
                    'ln' => $result['ln'],
                    'fb' => $result['fb'],
                    'sp' => $result['sp'],
                    'tf' => $result['tf'],
                    'lnKomplett' => $result['ln'] . '-' . $result['fb'] . '-' . $result['sp'] . '-' . $result['tf'],
                    'koordinate' => $result['koordinate'],
                    'system' => $result['stock_location_desc'],
                    'belegt' => false,
                ];
            }
        }

        return $allResults;
    }

    /**
     * @throws Exception
     * @return array<string|int|mixed>
     *
     * @SuppressWarnings(ElseExpression)
     */
    public function getStockOccupancyByArticleId(int $articleId): array
    {
        /** @var Article|null $article */
        $article = $this->entityManager
            ->getRepository(Article::class)
            ->findOneBy(['articleId' => $articleId]);

        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();

        $queryBuilder
            ->select('tph.id AS id,
            tph.stock_coordinate AS koordinate,
            tph.stock_nr AS ln,
            tph.stock_level1 AS fb,
            tph.stock_level2 AS sp,
            tph.stock_level3 AS tf,
            tph.su_id AS lagereinheit,
            tph.article_nr AS articleNr,
            art.article_name AS bezeichnung,
            (SELECT SUM((SELECT IF(stock_coordinate = ta.stock_coordinate AND tr_type = 1, tr_quantity, 0.000))) FROM transport_request GROUP BY stock_coordinate LIMIT 1) AS transEin,
            (SELECT SUM((SELECT IF(stock_coordinate = ta.stock_coordinate AND tr_type = 2, tr_quantity, 0.000))) FROM transport_request GROUP BY stock_coordinate LIMIT 1) AS transAus,
            (SELECT SUM((SELECT IF(tr_type = 1, tr_quantity, 0.000))) FROM transport_history WHERE stock_coordinate = tph.stock_coordinate AND su_id = tph.su_id GROUP BY stock_coordinate LIMIT 1) - (SELECT SUM((SELECT IF(tr_type = 2, tr_quantity, 0.000))) FROM transport_history WHERE stock_coordinate = tph.stock_coordinate AND su_id = tph.su_id GROUP BY stock_coordinate LIMIT 1) AS lpBestand,
            (SELECT IF(tr_type = 1, DATE_FORMAT(tr_access,"%d.%m.%Y %T"), "") FROM transport_history WHERE stock_coordinate = tph.stock_coordinate ORDER BY tr_access DESC LIMIT 1) AS letzterZugang,
            (SELECT IF(tr_type = 2, DATE_FORMAT(tr_dispatch,"%d.%m.%Y %T"), "") FROM transport_history WHERE stock_coordinate = tph.stock_coordinate ORDER BY tr_dispatch DESC LIMIT 1) AS letzterAbgang')
            ->from('transport_history', 'tph')
            ->leftJoin('tph', 'transport_request', 'ta', 'tph.stock_coordinate = ta.stock_coordinate')
            ->innerJoin('tph', 'article', 'art', 'tph.article_nr = art.article_nr')
            ->andWhere('tph.article_nr = :article_nr')
            ->setParameter('article_nr', $article?->getArticleNr())
            ->groupBy('tph.su_id');

        $result = $queryBuilder->executeQuery();

        return $result->fetchAllAssociative();
    }

    /**
     * @throws Exception
     */
    public function getAllStockOccupancy(): JsonResponse
    {
        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();

        $queryBuilder
            ->select('tph.id AS id,
            tph.stock_coordinate AS stock_location_coordinate,
            tph.stock_nr AS stock_location_ln,
            tph.stock_level1 AS stock_location_fb,
            tph.stock_level2 AS stock_location_sp,
            tph.stock_level3 AS stock_location_tf,
            tph.su_id AS lagereinheit,
            tph.article_nr AS article_nr,
            art.article_name AS article_name,
            (SELECT SUM((SELECT IF(stock_coordinate = ta.stock_coordinate AND tr_type = 1, tr_quantity, 0.000))) FROM transport_request GROUP BY stock_coordinate LIMIT 1) AS incoming_stock,
            (SELECT SUM((SELECT IF(stock_coordinate = ta.stock_coordinate AND tr_type = 2, tr_quantity, 0.000))) FROM transport_request GROUP BY stock_coordinate LIMIT 1) AS reserved_stock,
            (SELECT SUM((SELECT IF(tr_type = 1, tr_quantity, 0.000))) FROM transport_history WHERE stock_coordinate = tph.stock_coordinate AND su_id = tph.su_id GROUP BY stock_coordinate LIMIT 1) - (SELECT SUM((SELECT IF(tr_type = 2, tr_quantity, 0.000))) FROM transport_history WHERE stock_coordinate = tph.stock_coordinate AND su_id = tph.su_id GROUP BY stock_coordinate LIMIT 1) AS in_stock,
            (SELECT IF(tr_type = 1, DATE_FORMAT(tr_access,"%d.%m.%Y %T"), "") FROM transport_history WHERE stock_coordinate = tph.stock_coordinate ORDER BY tr_access DESC LIMIT 1) AS last_incoming,
            (SELECT IF(tr_type = 2, DATE_FORMAT(tr_dispatch,"%d.%m.%Y %T"), "") FROM transport_history WHERE stock_coordinate = tph.stock_coordinate ORDER BY tr_dispatch DESC LIMIT 1) AS last_outgoing')
            ->from('transport_history', 'tph')
            ->leftJoin('tph', 'transport_request', 'ta', 'tph.stock_coordinate = ta.stock_coordinate')
            ->innerJoin('tph', 'article', 'art', 'tph.article_nr = art.article_nr')
            ->groupBy('tph.su_id')
            ->orderBy('tph.stock_coordinate', 'ASC');

        $result = $queryBuilder->executeQuery();

        return new JsonResponse($result->fetchAllAssociative());
    }

    public function getStockOccupancyById(int $stockOccupancyId): ?StockOccupancy
    {
        return $this->entityManager
            ->getRepository(StockOccupancy::class)
            ->findOneBy(['stockOccupancyId' => $stockOccupancyId]);
    }
}
