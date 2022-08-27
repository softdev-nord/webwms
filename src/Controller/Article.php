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
use WebWMS\Controller\Requirements as Requirements;
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

    /**
     * @Route("/artikel", name="article")
     */
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
                'editArticleForm' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/artikel_anlegen", name="add_article")
     */
    public function addArticle(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(AddNewArticleType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->articleService->addArticle($request);
            $this->addFlash('success', 'Der Artikel wurde erfolgreich angelegt.');

            return $this->redirectToRoute('add_article');
        }

        return $this->render(
            'article/add_new_article.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Artikel bearbeiten',
                'lastId' => $this->articleService->getLastArticle(),
                'addArticleForm' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("artikel_bearbeiten/articleNr/{article_nr}", name="edit_article", methods={"GET","POST"})
     * @throws Exception
     */
    public function editArticle(Request $request, $article_nr): RedirectResponse|JsonResponse|Response
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

        $article = $this->articleService->getArticleByNr((int) $article_nr);
        $form = $this->createForm(EditArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($responseData['success']) {
                $responseData['message'] = 'Die Änderungen am Artikel wurden erfolgreich gespeichert.';
                $logMessage = sprintf('Der Artikel mit der Artikel-Nr. %s wurde geändert.', $requestData['article_nr']);
                $this->articleService->updateArticle($requestData);
                $this->loggingService->write($request, $logMessage);

                return new JsonResponse($responseData);
            }

            $responseData['message'] = 'Artikel konnte nicht gespeichert werden.';

            return new JsonResponse($responseData);
        }

        return $this->render(
            'article/edit.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Artikel bearbeiten',
                'lastId' => $this->articleService->getLastArticle(),
                'editArticleForm' => $form->createView(),
                'articles' => json_decode($this->getAllArticles()->getContent()),
            ]
        );
    }

    /**
     * @Route("/article_ajax", name="article_ajax")
     *
     * @throws Exception
     */
    public function getAllArticles(): JsonResponse
    {
        return $this->articleService->getAllArticles();
    }
}
