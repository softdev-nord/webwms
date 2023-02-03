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
use WebWMS\Form\Article\AddArticleType;
use WebWMS\Form\Article\DeleteArticleType;
use WebWMS\Form\Article\EditArticleType;
use WebWMS\Service\Article\ArticleService;
use WebWMS\Service\LoggingService;
use WebWMS\Service\Validation\ArticleValidationService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        Article
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
    public function index(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(AddArticleType::class);

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
                'route' => $request->attributes->get('_route'),
            ]
        );
    }

    /**
     * @throws \Exception
     */
    #[Route('/artikel_anlegen', name: 'add_article')]
    public function addArticle(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $requestData = $request->request->all()['add_article'];

        $form = $this->createForm(AddArticleType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $requestData['message'] = 'Der Artikel wurde erfolgreich angelegt.';
            $logMessage = 'Der Artikel mit der Artikel-Nr. '.$requestData['articleNr'].' wurde angelegt.';
            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
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
     * @throws \Exception
     */
    #[Route('artikel_bearbeiten/articleId/{articleId}', name: 'edit_article')]
    public function editArticle(Request $request, int $articleId): RedirectResponse|JsonResponse|Response|null
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $requestData = $request->request->all()['edit_article'];

        $responseData = $this->articleValidationService->validateArticleData((array) $requestData);
        $responseData['message'] = '';

        $article = $this->articleService->getArticleById($articleId);

        if (!$article) {
            return null;
        }

        $form = $this->createForm(EditArticleType::class, $article);
        $form->handleRequest($request);
        $articleNr = $article->getArticleNr();

        if ($form->isSubmitted() && $form->isValid()) {
            if ($responseData['success']) {
                $responseData['message'] = 'Der Artikel mit der Artikel-Nr. '.$articleNr.' wurde erfolgreich geändert.';
                $logMessage = 'Der Artikel mit der Artikel-Nr. '.$articleNr.' wurde geändert.';
                $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
                $this->articleService->updateArticle($request);

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

    #[Route('/artikel_löschen/articleId/{articleId}', name: 'delete_article')]
    public function deleteArticle(Request $request, int $articleId): RedirectResponse|JsonResponse|Response|null
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $article = $this->articleService->getArticleById($articleId);

        if (!$article) {
            return null;
        }

        $form = $this->createForm(DeleteArticleType::class, $article);
        $form->handleRequest($request);
        $articleNr = $article->getArticleNr();

        if ($form->isSubmitted() && $form->isValid()) {
            $responseData['message'] = 'Der Artikel mit der Artikel-Nr. '.$articleNr.' wurde erfolgreich gelöscht.';
            $logMessage = 'Der Artikel mit der Artikel-Nr. '.$articleNr.' wurde gelöscht.';
            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->articleService->deleteArticle($articleNr);

            return new JsonResponse($responseData);
        }

        return $this->render(
            'article/article_delete_ask.html.twig',
            [
                'articleNr' => $articleNr,
                'articleForm' => $form->createView(),
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
