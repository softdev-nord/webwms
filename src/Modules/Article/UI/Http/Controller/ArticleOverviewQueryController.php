<?php

declare(strict_types=1);

namespace WebWMS\Modules\Article\UI\Http\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use WebWMS\Modules\Article\Application\Query\GetArticleOverviewQuery;
use WebWMS\Modules\Article\Application\Query\GetArticleOverviewQueryHandler;

#[Route('/query/articles')]
class ArticleOverviewQueryController extends AbstractController
{
    public function __construct(
        private readonly GetArticleOverviewQueryHandler $handler,
    ) {
    }

    #[Route('/overview', name: 'article_overview_query', methods: ['GET'])]
    public function overview(Request $request): JsonResponse
    {
        if (!$this->getUser() instanceof UserInterface) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $page = (int) $request->query->get('page', 1);
        $limit = (int) $request->query->get('limit', 50);
        $searchTerm = $request->query->get('search');

        $query = new GetArticleOverviewQuery(
            page: max(1, $page),
            limit: max(1, min($limit, 500)), // Max 500 per page
            searchTerm: $searchTerm,
        );

        $result = $this->handler->handle($query);

        return new JsonResponse([
            'items' => array_map(fn($item) => [
                'id' => $item->getArticleId(),
                'article_nr' => $item->getArticleNr(),
                'article_name' => $item->getArticleName(),
                'article_description' => $item->getArticleDescription(),
                'total_stock' => $item->getTotalStock(),
                'incoming_stock' => $item->getIncomingStock(),
                'location_count' => $item->getLocationCount(),
            ], $result->getItems()),
            'total' => $result->getTotal(),
            'page' => $result->getPage(),
            'limit' => $result->getLimit(),
        ]);
    }
}

