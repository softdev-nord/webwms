<?php

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Controller\Requirements as Requirements;
use WebWMS\Entity\Article as Articles;

class Article extends AbstractController
{
    /**
     * @Route("/article_ajax", name="article_ajax")
     */
    public function getAllArticles()
    {
        $articles = $this->getDoctrine()->getRepository(Articles::class)->getAllArticlesWithJoin();

        if (!$articles) {
            throw $this->createNotFoundException('Keine Artikel gefunden');
        }

        return $articles;
    }

    /**
     * @Route("/artikel", name="article")
     */
    public function index()
    {
        return $this->render('article/index.html.twig', [
            'appName' => Requirements::APP_NAME,
            'appVersion' => Requirements::APP_VERSION,
            'appVersionNumber' => Requirements::APP_VERSION_NUMBER,
            'page' => 'Artikelübersicht',
            'article' => $this->getAllArticles(),
        ]);
    }
}
