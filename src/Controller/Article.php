<?php

namespace WebWMS\Controller;

use WebWMS\Controller\Requirements as Requirements;
use WebWMS\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL;

class Article extends AbstractController
{
    /**
     * @var DBAL\Connection
     */
    private $db;

    public function __construct(DBAL\Connection $db)
    {
        $this->db = $db;
    }

    public function getAllArticle()
    {
        $articles = $this->getDoctrine()->getRepository(\WebWMS\Entity\Article::class)->findAll();

        if (!$articles) {
            throw $this->createNotFoundException(
                'Keine Artikel gefunden'
            );
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
            'article' => $this->getAllArticle(),
        ]);
    }
}
