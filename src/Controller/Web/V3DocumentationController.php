<?php

declare(strict_types=1);

namespace WebWMS\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use WebWMS\Documentation\Application\SafeMarkdownRenderer;
use WebWMS\Documentation\Application\UserDocumentationService;

#[Route('/v3/help', name: 'v3_documentation_')]
final class V3DocumentationController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(Request $request, UserDocumentationService $documentation): Response
    {
        $query = trim((string) $request->query->get('q'));

        return $this->render('v3/documentation/index.html.twig', [
            'page' => 'Hilfe & Dokumentation',
            'groups' => $documentation->groupedDocuments($query),
            'query' => $query,
        ]);
    }

    #[Route('/{slug}', name: 'show', requirements: ['slug' => '[a-z0-9][a-z0-9-]*'], methods: ['GET'])]
    public function show(string $slug, UserDocumentationService $documentation, SafeMarkdownRenderer $markdown): Response
    {
        $document = $documentation->document($slug);
        $rendered = $markdown->render($document['markdown'], $this->generateUrl('v3_documentation_index'));

        return $this->render('v3/documentation/show.html.twig', [
            'page' => $document['title'],
            'document' => $document,
            'content' => $rendered['html'],
            'toc' => $rendered['toc'],
        ]);
    }
}
