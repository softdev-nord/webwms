<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Documentation\Application;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WebWMS\Documentation\Application\UserDocumentationService;

final class UserDocumentationServiceTest extends TestCase
{
    private UserDocumentationService $documentation;

    protected function setUp(): void
    {
        $this->documentation = new UserDocumentationService(dirname(__DIR__, 4));
    }

    public function testDiscoversAndGroupsRepositoryDocumentation(): void
    {
        $groups = $this->documentation->groupedDocuments();

        self::assertArrayHasKey('Grundlagen & Administration', $groups);
        self::assertArrayHasKey('Lager & Bestand', $groups);
        self::assertArrayHasKey('Wareneingang', $groups);
        self::assertArrayHasKey('Warenausgang', $groups);
        self::assertArrayHasKey('Integration & Technik', $groups);
    }

    public function testSearchUsesTitleAndMarkdownContent(): void
    {
        $groups = $this->documentation->groupedDocuments('Seriennummer');
        $documents = array_merge(...array_values($groups));

        self::assertContains('inventory-stock-dimensions', array_column($documents, 'slug'));
    }

    public function testRejectsPathTraversal(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $this->documentation->document('../technical/api-v3');
    }
}
