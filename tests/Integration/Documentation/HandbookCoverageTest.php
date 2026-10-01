<?php

declare(strict_types=1);

namespace WebWMS\Tests\Integration\Documentation;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Environment;
use WebWMS\Documentation\Application\HandbookCoverageMap;
use WebWMS\Documentation\Application\UserDocumentationService;
use WebWMS\Documentation\Presentation\Web\DocumentationController;

final class HandbookCoverageTest extends KernelTestCase
{
    public function testEveryV3WebRouteIsAssignedToExactlyOneChapter(): void
    {
        self::bootKernel();
        $router = self::getContainer()->get(RouterInterface::class);
        self::assertInstanceOf(RouterInterface::class, $router);

        $coveredChapters = [];
        foreach ($router->getRouteCollection() as $name => $route) {
            if (!str_starts_with($route->getPath(), '/v3')) {
                continue;
            }
            $chapter = HandbookCoverageMap::chapterForRoute((string) $name);
            self::assertNotNull($chapter, sprintf('Route "%s" is missing from the handbook coverage map.', $name));
            $coveredChapters[$chapter] = true;
        }

        self::assertSame(
            array_column(UserDocumentationService::CHAPTERS, 'slug'),
            array_values(array_filter(array_column(UserDocumentationService::CHAPTERS, 'slug'), static fn (string $slug): bool => isset($coveredChapters[$slug]))),
            'Every handbook chapter must cover at least one productive V3 route.'
        );
    }

    public function testHandbookRoutesAndPermissionsAreConfigured(): void
    {
        self::bootKernel();
        $router = self::getContainer()->get(RouterInterface::class);
        self::assertInstanceOf(RouterInterface::class, $router);

        self::assertSame('/v3/help', $router->generate('v3_documentation_index'));
        self::assertSame('/v3/help/getting-started', $router->generate('v3_documentation_show', ['slug' => 'getting-started']));

        foreach (['index', 'show'] as $method) {
            $attributes = (new \ReflectionMethod(DocumentationController::class, $method))->getAttributes(IsGranted::class);
            self::assertCount(1, $attributes);
            self::assertSame('documentation.handbook.read', $attributes[0]->newInstance()->attribute);
        }
    }

    public function testStructuredChapterTemplateRendersBothLocalesAndEscapesHtml(): void
    {
        self::bootKernel();
        $documentation = self::getContainer()->get(UserDocumentationService::class);
        $twig = self::getContainer()->get(Environment::class);
        self::assertInstanceOf(UserDocumentationService::class, $documentation);
        self::assertInstanceOf(Environment::class, $twig);

        foreach (['de' => 'Einstieg und Arbeitsumgebung', 'en' => 'Getting started and workspace'] as $locale => $headline) {
            $document = $documentation->document('getting-started', $locale);
            $document['sections'][0]['paragraphs'][] = '<script>alert(1)</script>';
            $html = $twig->render('documentation/_chapter.html.twig', ['document' => $document]);

            self::assertStringContainsString($headline, $html);
            self::assertStringContainsString('/assets/images/handbook/placeholder.svg', $html);
            self::assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $html);
            self::assertStringNotContainsString('<script>', $html);
        }
    }

    public function testSidebarContainsPermissionAndChapterNavigation(): void
    {
        $sidebar = file_get_contents(dirname(__DIR__, 3) . '/templates/v3/subsections/sidebar.html.twig');
        self::assertIsString($sidebar);

        self::assertStringContainsString("is_granted('documentation.handbook.read')", $sidebar);
        self::assertStringContainsString('sidebarHandbook', $sidebar);
        foreach (UserDocumentationService::CHAPTERS as $chapter) {
            self::assertStringContainsString("'" . $chapter['slug'] . "'", $sidebar);
        }
    }
}
