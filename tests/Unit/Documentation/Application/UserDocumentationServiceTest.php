<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Documentation\Application;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Yaml\Yaml;
use WebWMS\Documentation\Application\UserDocumentationService;

class UserDocumentationServiceTest extends TestCase
{
    private UserDocumentationService $documentation;

    protected function setUp(): void
    {
        $this->documentation = new UserDocumentationService(dirname(__DIR__, 4));
    }

    public function testDiscoversAndGroupsTranslatedHandbook(): void
    {
        $groups = $this->documentation->groupedDocuments(locale: 'de');

        self::assertArrayHasKey('Grundlagen & Bedienkonzept', $groups);
        self::assertArrayHasKey('Administration & Konfiguration', $groups);
        self::assertArrayHasKey('Lager & Bestand', $groups);
        self::assertArrayHasKey('Wareneingang, Fulfillment & Versand', $groups);
        self::assertCount(count(UserDocumentationService::CHAPTERS), array_merge(...array_values($groups)));
    }

    public function testSearchReturnsChapterAnchorAndExcerpt(): void
    {
        $groups = $this->documentation->groupedDocuments('Seriennummern', 'de');
        $documents = array_merge(...array_values($groups));
        $stock = array_values(array_filter($documents, static fn (array $document): bool => $document['slug'] === 'warehouse-and-stock'))[0];

        self::assertSame('section_3', $stock['match']['anchor']);
        self::assertStringContainsString('Seriennummern', $stock['match']['excerpt']);
    }

    #[DataProvider('localeProvider')]
    public function testEveryChapterIsTranslated(string $locale): void
    {
        foreach (UserDocumentationService::CHAPTERS as $chapter) {
            $document = $this->documentation->document($chapter['slug'], $locale);
            self::assertNotSame('', $document['title']);
            self::assertNotSame([], $document['sections']);
            self::assertNotSame([], array_filter($document['sections'], static fn (array $section): bool => $section['image'] !== null));
        }
    }

    public function testRuntimeUsesStructuredSectionsInsteadOfMarkdown(): void
    {
        $document = $this->documentation->document('getting-started', 'de');
        $section = $document['sections'][0];

        self::assertArrayNotHasKey('markdown', $document);
        self::assertSame('section_1', $section['id']);
        self::assertIsArray($section['paragraphs']);
        self::assertIsArray($section['steps']);
        self::assertSame('/assets/images/handbook/placeholder.svg', $section['image']['src']);
    }

    public function testGermanAndEnglishTranslationKeysAreIdentical(): void
    {
        $root = dirname(__DIR__, 4) . '/translations/handbook.';
        $german = Yaml::parseFile($root . 'de.yaml');
        $english = Yaml::parseFile($root . 'en.yaml');

        self::assertSame($this->keys($german), $this->keys($english));
    }

    public function testRejectsPathTraversal(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->documentation->document('../technical/api-v3', 'de');
    }

    /** @return iterable<string, array{string}> */
    public static function localeProvider(): iterable
    {
        yield 'German' => ['de'];
        yield 'English' => ['en'];
    }

    /** @param array<string, mixed> $values @return list<string> */
    private function keys(array $values, string $prefix = ''): array
    {
        $keys = [];
        foreach ($values as $key => $value) {
            $path = $prefix === '' ? (string) $key : $prefix . '.' . $key;
            if (is_array($value)) {
                array_push($keys, ...$this->keys($value, $path));
            } else {
                $keys[] = $path;
            }
        }
        sort($keys);

        return $keys;
    }
}
