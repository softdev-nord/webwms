<?php

declare(strict_types=1);

namespace WebWMS\Documentation\Presentation\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use WebWMS\Documentation\Application\SafeMarkdownRenderer;
use WebWMS\Documentation\Application\UserDocumentationService;

#[Route('/v3/help', name: 'v3_documentation_')]
class DocumentationController extends AbstractController
{
    public function __construct(private readonly string $projectDir)
    {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(Request $request, UserDocumentationService $documentation): Response
    {
        $query = trim((string) $request->query->get('q'));

        return $this->render('documentation/index.html.twig', [
            'page' => 'documentation.page.help_and_documentation',
            'groups' => $documentation->groupedDocuments($query),
            'query' => $query,
        ]);
    }

    #[Route('/api-reference', name: 'swagger', methods: ['GET'])]
    public function swagger(): Response
    {
        return $this->render('documentation/swagger.html.twig', [
            'page' => 'documentation.page.api_documentation',
            'openapiUrl' => $this->generateUrl('v3_documentation_openapi'),
        ]);
    }

    #[Route('/openapi.yaml', name: 'openapi', methods: ['GET'])]
    public function openApi(): Response
    {
        $content = file_get_contents($this->projectDir . '/docs/technical/openapi-v3.yaml');
        if (!is_string($content)) {
            throw $this->createNotFoundException('The OpenAPI contract is unavailable.');
        }

        return new Response($content, Response::HTTP_OK, ['Content-Type' => 'application/yaml']);
    }

    #[Route('/{slug}', name: 'show', requirements: ['slug' => '[a-z0-9][a-z0-9-]*'], methods: ['GET'])]
    public function show(string $slug, UserDocumentationService $documentation, SafeMarkdownRenderer $markdown): Response
    {
        $document = $documentation->document($slug);
        $rendered = $markdown->render($document['markdown'], $this->generateUrl('v3_documentation_index'));

        return $this->render('documentation/show.html.twig', [
            'page' => $document['title'],
            'document' => $document,
            'content' => $rendered['html'],
            'toc' => $rendered['toc'],
        ]);
    }
}
