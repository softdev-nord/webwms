<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Form\AddNewArticleType;
use WebWMS\Form\EditArticleType;
use WebWMS\Service\Article\ArticleService;
use WebWMS\Service\LoggingService;
use WebWMS\Service\Validation\ArticleValidationService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        Article
 */
class Article extends AbstractController
{
    public function __construct(
        private ArticleService $articleService,
        private Requirements $requirements,
        private ArticleValidationService $articleValidationService,
        private LoggingService $loggingService
    ) {
    }

    #[Route('/artikel', name: 'article')]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(AddNewArticleType::class);

        return $this->render(
            'article/index.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Artikelübersicht',
                'articleForm' => $form->createView(),
            ]
        );
    }

    #[Route('/artikel_anlegen', name: 'add_article')]
    public function addArticle(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $requestData = $request->request->all();

        if (!empty($requestData)) {
            $requestData = $requestData['add_new_article'];
        }

        $form = $this->createForm(AddNewArticleType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $requestData['message'] = 'Der Artikel wurde erfolgreich angelegt.';
            $logMessage = sprintf('Der Artikel mit der Artikel-Nr. %s wurde angelegt.', $requestData['articleNr']);
            $this->loggingService->write($request, $logMessage);
            $this->articleService->addArticle($request);

            return new JsonResponse($requestData);
        }

        return $this->render(
            'article/article_add.html.twig',
            [
                'lastId' => $this->articleService->getLastArticle(),
                'articleForm' => $form->createView(),
                'editArticle' => false,
            ]
        );
    }

    /**
     * @throws Exception
     */
    #[Route('artikel_bearbeiten/articleId/{articleId}', name: 'edit_article')]
    public function editArticle(Request $request, $articleId): RedirectResponse|JsonResponse|Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $requestData = $request->request->all();

        if (!empty($requestData)) {
            $requestData = $requestData['edit_article'];
        }

        $responseData = $this->articleValidationService->validateArticleData($requestData);
        $responseData['message'] = '';

        $article = $this->articleService->getArticleById((int) $articleId);
        $form = $this->createForm(EditArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($responseData['success']) {
                $responseData['message'] = 'Die Änderungen am Artikel wurden erfolgreich gespeichert.';
                $logMessage = sprintf('Der Artikel mit der Artikel-Nr. %s wurde geändert.', $requestData['articleNr']);
                $this->loggingService->write($request, $logMessage);
                $this->articleService->updateArticle($requestData);

                return new JsonResponse($responseData);
            }

            $responseData['message'] = 'Artikel konnte nicht gespeichert werden.';

            return new JsonResponse($responseData);
        }

        return $this->render(
            'article/article_edit.html.twig',
            [
                'articleForm' => $form->createView(),
                'articles' => $article,
                'editArticle' => true,
            ]
        );
    }

    /**
     * @throws Exception
     */
    #[Route('/article_ajax', name: 'article_ajax')]
    public function getAllArticles(): JsonResponse
    {
        return $this->articleService->getAllArticles();
    }
}
