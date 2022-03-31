<?php

declare(strict_types=1);

namespace WebWMS\Services;

use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WebWMS\Entity\Article as Articles;

/**
 * @package:    WebWMS\Services
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        ArticleService
 */
class ArticleService
{
    /** @var ManagerRegistry */
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @throws Exception
     */
    public function getAllArticles(): JsonResponse
    {
        $articles = $this->getAllArticlesWithJoin();

        if (!$articles) {
            throw $this->createNotFoundException('Keine Artikel gefunden');
        }

        return $articles;
    }

    /**
     * Get article for
     * @return JsonResponse
     */
    public function getArticle(): JsonResponse
    {
        $connection = $this->doctrine->getConnection();

        $numOfBoxArt = !empty($_GET['numOfBoxArt']) ? $_GET['numOfBoxArt'] : '';
        $name = !empty($_GET['article_nr']) ? strtolower(trim($_GET['article_nr'])) : '';

        $boxName = 'article_nr';

        switch ($numOfBoxArt) {
            case 1:
                $boxName = 'article_name';
                break;
            case 2:
                $boxName = 'id';
                break;
            case 3:
                $boxName = 'art_ean';
                break;
            case 4:
                $boxName = 'art_kat';
                break;
        }

        $data = [];
        if (!empty($_GET['name_art'])) {
            $name = strtolower(trim($_GET['name_art']));

            $sqlArt = "SELECT article_nr, article_name, id, article_ean, article_category FROM article where LOWER($boxName) LIKE '".$name."%'";
            $stmt = $connection->executeQuery($sqlArt);

            while ($row = $stmt->fetchAssociative()) {
                $name = $row['article_nr'].'|'.$row['article_name'].'|'.$row['id'].'|'.$row['article_ean'].'|'.$row['article_category'];
                array_push($data, $name);
            }
        }

        return new JsonResponse($data);
    }

    /**
     * @throws Exception
     */
    public function getAllArticlesWithJoin(): JsonResponse
    {
        $conn = $this->doctrine->getConnection();

        $sql = "SELECT art.article_nr, art.article_name, art.article_category, art.article_weight, art.article_ean, art.article_unit, art.article_depth, art.article_width, art.article_height,
                (SELECT (SUM(IF(transport_history.tr_typ = '1', transport_history.tr_quantity, 0.000))) - (SUM(IF(transport_history.tr_typ = '2', transport_history.tr_quantity, 0.000))) 
                    FROM transport_history WHERE transport_history.article_nr = art.article_nr GROUP BY transport_history.article_nr LIMIT 1) AS lbw_menge
                FROM transport_history AS tph
                RIGHT OUTER JOIN article AS art
                    ON tph.article_nr = art.article_nr
                GROUP BY art.article_nr";

        $data = $conn->fetchAllAssociative($sql);

        return new JsonResponse($data);
    }

    protected function createNotFoundException(string $message = 'Not Found', \Throwable $previous = null): NotFoundHttpException
    {
        return new NotFoundHttpException($message, $previous);
    }
}