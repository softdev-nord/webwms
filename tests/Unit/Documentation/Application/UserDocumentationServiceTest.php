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

    public function testSearchHandlesEmptyUnknownAndSpecialQueries(): void
    {
        self::assertCount(count(UserDocumentationService::CHAPTERS), array_merge(...array_values($this->documentation->groupedDocuments('', 'de'))));
        self::assertSame([], $this->documentation->groupedDocuments('definitely-unknown-value', 'de'));
        self::assertSame([], $this->documentation->groupedDocuments('<script>alert(1)</script>', 'de'));
    }

    public function testSearchIncludesConfigurationGuidance(): void
    {
        $groups = $this->documentation->groupedDocuments('Pflichtfelder', 'de');
        $documents = array_merge(...array_values($groups));

        self::assertNotSame([], $documents);
        self::assertSame('chapter-guidance', $documents[0]['match']['anchor']);
    }

    public function testSearchIndexesViewFieldsAndReturnsStableViewAnchor(): void
    {
        $groups = $this->documentation->groupedDocuments('Provider-Code', 'de');
        $documents = array_merge(...array_values($groups));
        $gettingStarted = array_values(array_filter($documents, static fn (array $document): bool => $document['slug'] === 'getting-started'))[0];

        self::assertSame('view-workspace_login', $gettingStarted['match']['anchor']);
    }

    public function testLoginViewHasStructuredCoverageForEveryBusinessField(): void
    {
        $view = $this->documentation->document('getting-started', 'de')['views'][0];

        self::assertSame('security/workspace_login.html.twig', $view['template']);
        self::assertSame(['tenant_id', 'email', 'password', 'provider'], array_column($view['fields'], 'id'));
        self::assertSame([], array_filter($view['fields'], static fn (array $field): bool => in_array('', [$field['format'], $field['example'], $field['help'], $field['effect'], $field['errors']], true)));
        self::assertSame('/assets/images/handbook/security/workspace-login.svg', $view['image']['src']);
    }

    public function testLoginTemplateAndHandbookFieldCoverageStayInSync(): void
    {
        $view = $this->documentation->document('getting-started', 'de')['views'][0];
        $template = file_get_contents(dirname(__DIR__, 4) . '/templates/' . $view['template']);
        self::assertIsString($template);
        preg_match_all('/<(?:input|select|textarea)\b[^>]*\bname="([^"]+)"/i', $template, $matches);

        $technicalFields = ['_csrf_token'];
        $templateFields = array_values(array_unique(array_diff($matches[1], $technicalFields)));
        sort($templateFields);
        $documentedFields = array_column($view['fields'], 'id');
        sort($documentedFields);

        self::assertSame($templateFields, $documentedFields, 'Every business input in the documented view needs structured field guidance.');
    }

    #[DataProvider('localeProvider')]
    public function testAdministrationViewsHaveCompleteTemplateFieldAndImageCoverage(string $locale): void
    {
        $views = $this->documentation->document('users-roles-and-security', $locale)['views'];
        self::assertSame([
            'administration/index.html.twig',
            'administration/users.html.twig',
            'administration/user_new.html.twig',
            'administration/user_edit.html.twig',
            'administration/roles.html.twig',
            'administration/role_new.html.twig',
            'administration/role_edit.html.twig',
            'administration/api_clients.html.twig',
            'administration/api_client_new.html.twig',
            'administration/api_client_edit.html.twig',
            'administration/api_client_credential.html.twig',
        ], array_column($views, 'template'));

        $imageSources = [];
        foreach ($views as $view) {
            $template = file_get_contents(dirname(__DIR__, 4) . '/templates/' . $view['template']);
            self::assertIsString($template);
            preg_match_all('/<(?:input|select|textarea)\b[^>]*\bname="([^"]+)"/i', $template, $matches);
            $templateFields = array_values(array_unique(array_map(
                static fn (string $name): string => str_replace('[]', '', $name),
                array_filter($matches[1], static fn (string $name): bool => !str_starts_with($name, '_'))
            )));
            sort($templateFields);
            $documentedFields = array_column($view['fields'], 'id');
            sort($documentedFields);
            self::assertSame($templateFields, $documentedFields, sprintf('Field coverage differs for "%s".', $view['template']));

            self::assertFileExists(dirname(__DIR__, 4) . '/public' . $view['image']['src']);
            $imageSources[] = $view['image']['src'];
        }

        self::assertCount(count($imageSources), array_unique($imageSources), 'Every documented view needs a unique screenshot reference.');
    }

    #[DataProvider('localeProvider')]
    public function testEveryChapterIsTranslated(string $locale): void
    {
        foreach (UserDocumentationService::CHAPTERS as $chapter) {
            $document = $this->documentation->document($chapter['slug'], $locale);
            self::assertNotSame('', $document['title']);
            self::assertSame(['prerequisites', 'permissions', 'fields', 'statuses', 'errors'], array_keys($document['guidance']));
            self::assertNotSame([], $document['sections']);
            self::assertSame([], array_filter($document['sections'], static fn (array $section): bool => $section['image'] === null));
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

    public function testPreviousAndNextNavigationAreLocalized(): void
    {
        $document = $this->documentation->document('navigation-and-lists', 'en');

        self::assertSame('Getting started and workspace', $document['previous']['title']);
        self::assertSame('Users, roles, permissions and single sign-on', $document['next']['title']);
    }

    public function testGermanAndEnglishTranslationKeysAreIdentical(): void
    {
        $root = dirname(__DIR__, 4) . '/translations/handbook.';
        $german = Yaml::parseFile($root . 'de.yaml');
        $english = Yaml::parseFile($root . 'en.yaml');

        self::assertSame($this->keys($german), $this->keys($english));
    }

    public function testCatalogueContainsNoUnknownOrUnusedStructures(): void
    {
        $root = dirname(__DIR__, 4) . '/translations/handbook.';
        foreach (['de', 'en'] as $locale) {
            $catalogue = Yaml::parseFile($root . $locale . '.yaml');
            self::assertSame(['ui', 'category', 'error', 'chapter'], array_keys($catalogue));
            self::assertSame(
                ['page_title', 'headline', 'introduction', 'search', 'search_action', 'reset_search', 'no_results', 'result_for', 'breadcrumb', 'all_chapters', 'on_this_page', 'no_subchapters', 'previous', 'next', 'navigation', 'open_navigation', 'close_navigation', 'overview', 'chapters', 'current_chapter', 'content', 'sidebar_section', 'sidebar_link', 'prerequisites', 'permissions', 'fields', 'statuses', 'errors', 'documented_views', 'navigation_path', 'displayed_information', 'fields_and_inputs', 'field_name', 'input_help', 'effect_and_errors', 'required', 'optional', 'format', 'example', 'effect', 'validation', 'actions'],
                array_keys($catalogue['ui'])
            );

            foreach ($catalogue['chapter'] as $chapter) {
                self::assertSame([], array_diff(array_keys($chapter), ['title', 'summary', 'sections', 'views', 'guidance']));
                self::assertSame(['prerequisites', 'permissions', 'fields', 'statuses', 'errors'], array_keys($chapter['guidance']));
                foreach ($chapter['sections'] as $section) {
                    self::assertSame([], array_diff(array_keys($section), ['title', 'paragraphs', 'steps', 'items', 'image']));
                    self::assertArrayHasKey('image', $section);
                }
            }
        }
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
