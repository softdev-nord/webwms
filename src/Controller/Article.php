<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Controller\Requirements as Requirements;
use WebWMS\Services\ArticleService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        Article
 */
class Article extends AbstractController
{
    /** @var ArticleService */
    private $articleService;

    /** @var Requirements */
    private $requirements;

    public function __construct(
        ArticleService $articleService,
        Requirements $requirements
    ) {
        $this->articleService = $articleService;
        $this->requirements = $requirements;
    }

    /**
     * @Route("/article_ajax", name="article_ajax")
     * @throws Exception
     */
    public function getAllArticles(): JsonResponse
    {
        return $this->articleService->getAllArticles();
    }

    /**
     * @Route("/artikel", name="article")
     */
    public function index(): Response
    {
        return $this->render('article/index.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Artikelübersicht',
                'article' => $this->getAllArticles(),
            ]
        );
    }
}
