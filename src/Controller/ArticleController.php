<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use WebWMS\Entity\Article as ArticleEntity;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Helper\FormHelper\ArticleFormHelper;
use WebWMS\Service\Article\ArticleService;
use WebWMS\Service\LoggingService;
use WebWMS\Service\RequirementsService;
use WebWMS\Service\Validation\ArticleValidationService;

#[ClassInformation(
    package: 'WebWMS\Controller',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'ArticleController'
)]
class ArticleController extends AbstractController
{
    public function __construct(
        private readonly ArticleService $articleService,
        private readonly RequirementsService $requirementsService,
        private readonly ArticleValidationService $articleValidationService,
        private readonly LoggingService $loggingService,
        private readonly ArticleFormHelper $articleFormHelper,
    ) {
    }

    #[Route('/artikel', name: 'article')]
    public function index(): Response
    {
        if (!$this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'article/index.html.twig',
            [
                'appName' => $this->requirementsService->getAppName(),
                'appVersion' => $this->requirementsService->getAppVersion(),
                'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
                'appCopyright' => $this->requirementsService->getAppCopyright(),
                'appLizenz' => $this->requirementsService->getAppLizenz(),
                'page' => 'Artikelübersicht',
            ]
        );
    }

    #[Route('/artikel_anlegen', name: 'add_article')]
    public function addArticle(Request $request): Response
    {
        if (!$this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->articleFormHelper->addArticleForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ArticleEntity $requestData */
            $requestData = $form->getData();
            $articleNr = $requestData->getArticleNr();
            $responseData = $this->articleValidationService
                ->validateArticleData(
                    (array) $request->request->all()['add_article']
                );

            if (isset($responseData['success'])) {
                $responseData['message'] = 'Der Artikel mit der Artikel-Nr. ' . $articleNr . ' wurde erfolgreich angelegt.';
                $logMessage = 'Der Artikel mit der Artikel-Nr. ' . $articleNr . ' wurde angelegt.';

                $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
                $this->articleService->addArticle($requestData);

                return new JsonResponse($responseData);
            }

            return new JsonResponse($responseData);
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

    #[Route('artikel_bearbeiten/articleId/{articleId}', name: 'edit_article')]
    public function editArticle(Request $request, int $articleId): RedirectResponse|JsonResponse|Response|null
    {
        if (!$this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }

        $article = $this->articleService->getArticleById($articleId);

        if (!$article instanceof ArticleEntity) {
            return null;
        }

        $form = $this->articleFormHelper->editArticleForm($article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ArticleEntity $requestData */
            $requestData = $form->getData();
            $articleNr = $article->getArticleNr();
            $responseData = $this->articleValidationService
                ->validateArticleData(
                    (array) $request->request->all()['edit_article']
                );

            if (isset($responseData['success'])) {
                $responseData['message'] = 'Der Artikel mit der Artikel-Nr. ' . $articleNr . ' wurde erfolgreich geändert.';
                $logMessage = 'Der Artikel mit der Artikel-Nr. ' . $articleNr . ' wurde geändert.';

                $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
                $this->articleService->updateArticle($requestData);

                return new JsonResponse($responseData);
            }

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
        if (!$this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }

        $article = $this->articleService->getArticleById($articleId);

        if (!$article instanceof ArticleEntity) {
            return null;
        }

        $form = $this->articleFormHelper->deleteArticleForm($article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ArticleEntity $requestData */
            $requestData = $form->getData();
            $articleNr = $article->getArticleNr();
            $responseData = [];

            $responseData['message'] = 'Der Artikel mit der Artikel-Nr. ' . $articleNr . ' wurde erfolgreich gelöscht.';
            $logMessage = 'Der Artikel mit der Artikel-Nr. ' . $articleNr . ' wurde gelöscht.';

            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->articleService->deleteArticle($requestData);

            return new JsonResponse($responseData);
        }

        return $this->render(
            'article/article_delete_ask.html.twig',
            [
                'articleForm' => $form->createView(),
                'articleNr' => $article->getArticleNr(),
            ]
        );
    }

    #[Route('/article_ajax', name: 'article_ajax')]
    public function getAllArticles(): JsonResponse
    {
        return $this->articleService->getAllArticles();
    }
}
