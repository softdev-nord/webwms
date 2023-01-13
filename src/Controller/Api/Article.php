<?php

declare(strict_types=1);

namespace WebWMS\Controller\Api;

use Doctrine\ORM\EntityNotFoundException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Service\Article\ArticleService;

/**
 * @package:    WebWMS\Controller\Api
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 *
 * Class        Article
 */
#[Route('/api', name: 'api_')]
class Article extends AbstractFOSRestController
{
    public function __construct(
        private ArticleService $articleService
    ) {
    }

    /**
     * @Rest\Get("/articles/{articleId}")
     * @OA\Response(
     *     response=200,
     *     description="Retrieves an Article by id",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Article::class, groups={"full"})),
     *        @OA\Examples(
     *              example="result",
     *              value={
     *                  "article_id": 1,
     *                  "article_nr": "60004",
     *                  "article_name": "Telefon MBO Alpha 1600 CT",
     *                  "article_category": "DECT-Telefone",
     *                  "article_weight": 1.5,
     *                  "article_ean": "4260151600048",
     *                  "article_unit": "Stk",
     *                  "article_depth": 250,
     *                  "article_width": 110,
     *                  "article_height": 150
     *              },summary="An result object."
     *        ),
     *     ),
     * )
     * @OA\Tag(name="Article")
     *
     * @throws EntityNotFoundException
     */
    public function getArticle(int $articleId): View
    {
        $article = $this->articleService->getArticleApi($articleId);
        if (!$article) {
            throw new EntityNotFoundException('Article with id '.$articleId.' does not exist!');
        }

        return $this->view($article, Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/articles")
     * @OA\Response(
     *     response=200,
     *     description="Retrieves a collection of Articles",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Article::class, groups={"full"}))
     *     ),
     * )
     * @OA\Tag(name="Article")
     */
    public function getArticles(): View
    {
        $articles = $this->articleService->getAllArticlesApi();

        return $this->view($articles, Response::HTTP_OK);
    }

    /**
     * @Rest\Post("/articles")
     * @OA\Response(
     *     response=200,
     *     description="Creates an Article",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Article::class, groups={"full"}))
     *     ),
     * )
     * @OA\Tag(name="Article")
     */
    public function postArticle(Request $request): View
    {
        $article = $this->articleService->addArticleApi(
            $request->get('article_nr'),
            $request->get('article_name'),
            $request->get('article_category'),
            $request->get('article_weight'),
            $request->get('article_ean'),
            $request->get('article_unit'),
            $request->get('article_depth'),
            $request->get('article_width'),
            $request->get('article_height'),
            $request->get('stock_out_strategy'),
            $request->get('le_quantity'),
            $request->get('standard_loading_equipment')
        );

        return $this->view($article, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Put("/articles/{articleId}")
     * @OA\Response(
     *     response=200,
     *     description="Replace a Article",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Article::class, groups={"full"}))
     *     ),
     * )
     * @OA\Tag(name="Article")
     */
    public function putArticle(int $articleId, Request $request): View
    {
        $article = $this->articleService->updateArticleApi(
            $articleId,
            $request->get('article_nr'),
            $request->get('article_name'),
            $request->get('article_category'),
            $request->get('article_weight'),
            $request->get('article_ean'),
            $request->get('article_unit'),
            $request->get('article_depth'),
            $request->get('article_width'),
            $request->get('article_height'),
            $request->get('stock_out_strategy'),
            $request->get('le_quantity'),
            $request->get('standard_loading_equipment')
        );

        return $this->view($article, Response::HTTP_OK);
    }

    /**
     * @Rest\Delete("/articles/{articleId}")
     * @OA\Response(
     *     response=200,
     *     description="Removes the Article by Id",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Article::class, groups={"full"}))
     *     ),
     * )
     * @OA\Tag(name="Article")
     */
    public function deleteArticle(int $articleId): View
    {
        $this->articleService->deleteArticleApi($articleId);

        return $this->view([], Response::HTTP_NO_CONTENT);
    }
}
