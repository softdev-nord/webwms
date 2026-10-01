<?php

declare(strict_types=1);

namespace WebWMS\Documentation\Application;

use RuntimeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Yaml\Yaml;

class UserDocumentationService
{
    /** @var list<array{slug: string, category: string}> */
    public const array CHAPTERS = [
        ['slug' => 'getting-started', 'category' => 'basics'],
        ['slug' => 'navigation-and-lists', 'category' => 'basics'],
        ['slug' => 'users-roles-and-security', 'category' => 'administration'],
        ['slug' => 'system-configuration', 'category' => 'administration'],
        ['slug' => 'warehouse-and-stock', 'category' => 'warehouse'],
        ['slug' => 'inventory-counting', 'category' => 'warehouse'],
        ['slug' => 'inbound', 'category' => 'processes'],
        ['slug' => 'fulfillment-and-outbound', 'category' => 'processes'],
        ['slug' => 'integrations-and-devices', 'category' => 'integration'],
        ['slug' => 'platform-and-extensions', 'category' => 'platform'],
        ['slug' => 'api-and-automation', 'category' => 'integration'],
        ['slug' => 'troubleshooting', 'category' => 'basics'],
    ];

    /** @var list<string> */
    private const array CATEGORY_ORDER = ['basics', 'administration', 'warehouse', 'processes', 'integration', 'platform'];

    /** @var array<string, array<string, mixed>> */
    private array $catalogues = [];

    public function __construct(
        private readonly string $projectDir
    ) {
    }

    /** @return array<string, list<array{slug: string, title: string, summary: string, match: null|array{anchor: string, excerpt: string}}>> */
    public function groupedDocuments(?string $query = null, ?string $locale = null): array
    {
        $catalogue = $this->catalogue($locale);
        $needle = mb_strtolower(trim((string) $query));
        $groups = [];
        foreach (self::CATEGORY_ORDER as $category) {
            $groups[$this->string($catalogue, ['category', $category])] = [];
        }

        foreach (self::CHAPTERS as $chapterDefinition) {
            $chapter = $this->chapter($catalogue, $chapterDefinition['slug']);
            $match = $needle === '' ? null : $this->match($chapter['sections'], $needle);
            if ($needle !== '' && $match === null && !str_contains(mb_strtolower($chapter['title'] . ' ' . $chapter['summary']), $needle)) {
                continue;
            }

            $category = $this->string($catalogue, ['category', $chapterDefinition['category']]);
            $groups[$category][] = [
                'slug' => $chapterDefinition['slug'],
                'title' => $chapter['title'],
                'summary' => $chapter['summary'],
                'match' => $match,
            ];
        }

        return array_filter($groups);
    }

    /** @return array{slug: string, category: string, title: string, summary: string, sections: list<array{id: string, title: string, paragraphs: list<string>, steps: list<string>, items: list<string>, image: null|array{src: string, alt: string}}>, previous: null|array{slug: string, title: string}, next: null|array{slug: string, title: string}} */
    public function document(string $slug, ?string $locale = null): array
    {
        $catalogue = $this->catalogue($locale);
        if (preg_match('/^[a-z0-9][a-z0-9-]*$/', $slug) !== 1) {
            throw new NotFoundHttpException($this->string($catalogue, ['error', 'not_found']));
        }

        $index = array_search($slug, array_column(self::CHAPTERS, 'slug'), true);
        if ($index === false) {
            throw new NotFoundHttpException($this->string($catalogue, ['error', 'not_found']));
        }

        $definition = self::CHAPTERS[$index];
        $chapter = $this->chapter($catalogue, $slug);

        return [
            'slug' => $slug,
            'category' => $this->string($catalogue, ['category', $definition['category']]),
            'title' => $chapter['title'],
            'summary' => $chapter['summary'],
            'sections' => $chapter['sections'],
            'previous' => $index > 0 ? $this->navigation($catalogue, self::CHAPTERS[$index - 1]['slug']) : null,
            'next' => isset(self::CHAPTERS[$index + 1]) ? $this->navigation($catalogue, self::CHAPTERS[$index + 1]['slug']) : null,
        ];
    }

    /** @return array{title: string, summary: string, sections: list<array{id: string, title: string, paragraphs: list<string>, steps: list<string>, items: list<string>, image: null|array{src: string, alt: string}}>} */
    private function chapter(array $catalogue, string $slug): array
    {
        $key = str_replace('-', '_', $slug);
        $chapters = $catalogue['chapter'] ?? null;
        $raw = is_array($chapters) ? ($chapters[$key] ?? null) : null;
        if (!is_array($raw)) {
            throw new RuntimeException(sprintf('Handbook chapter "%s" is missing.', $slug));
        }

        $sections = [];
        $rawSections = $raw['sections'] ?? null;
        if (!is_array($rawSections)) {
            throw new RuntimeException(sprintf('Handbook chapter "%s" has no sections.', $slug));
        }

        foreach ($rawSections as $id => $section) {
            if (!is_string($id) || !is_array($section)) {
                continue;
            }
            $sections[] = [
                'id' => $id,
                'title' => $this->requiredString($section, 'title', $slug . '.' . $id),
                'paragraphs' => $this->stringValues($section['paragraphs'] ?? []),
                'steps' => $this->stringValues($section['steps'] ?? []),
                'items' => $this->stringValues($section['items'] ?? []),
                'image' => $this->image($section['image'] ?? null),
            ];
        }

        return [
            'title' => $this->requiredString($raw, 'title', $slug),
            'summary' => $this->requiredString($raw, 'summary', $slug),
            'sections' => $sections,
        ];
    }

    /** @param list<array{id: string, title: string, paragraphs: list<string>, steps: list<string>, items: list<string>, image: null|array{src: string, alt: string}}> $sections @return null|array{anchor: string, excerpt: string} */
    private function match(array $sections, string $needle): ?array
    {
        foreach ($sections as $section) {
            foreach ([$section['title'], ...$section['paragraphs'], ...$section['steps'], ...$section['items']] as $text) {
                if (str_contains(mb_strtolower($text), $needle)) {
                    return ['anchor' => $section['id'], 'excerpt' => mb_strimwidth($text, 0, 220, '…')];
                }
            }
        }

        return null;
    }

    /** @return array{slug: string, title: string} */
    private function navigation(array $catalogue, string $slug): array
    {
        return ['slug' => $slug, 'title' => $this->chapter($catalogue, $slug)['title']];
    }

    /** @return array<string, mixed> */
    private function catalogue(?string $locale): array
    {
        $language = str_starts_with(mb_strtolower((string) $locale), 'en') ? 'en' : 'de';
        if (isset($this->catalogues[$language])) {
            return $this->catalogues[$language];
        }

        $catalogue = Yaml::parseFile($this->projectDir . '/translations/handbook.' . $language . '.yaml');
        if (!is_array($catalogue)) {
            throw new RuntimeException(sprintf('Handbook catalogue "%s" is invalid.', $language));
        }

        return $this->catalogues[$language] = $catalogue;
    }

    /** @param list<string> $path */
    private function string(array $catalogue, array $path): string
    {
        $value = $catalogue;
        foreach ($path as $segment) {
            $value = is_array($value) ? ($value[$segment] ?? null) : null;
        }
        if (!is_string($value)) {
            throw new RuntimeException(sprintf('Handbook value "%s" is missing.', implode('.', $path)));
        }

        return $value;
    }

    private function requiredString(array $values, string $key, string $context): string
    {
        $value = $values[$key] ?? null;
        if (!is_string($value) || trim($value) === '') {
            throw new RuntimeException(sprintf('Handbook value "%s.%s" is missing.', $context, $key));
        }

        return $value;
    }

    /** @return list<string> */
    private function stringValues(mixed $values): array
    {
        if (!is_array($values)) {
            return [];
        }

        return array_values(array_filter($values, is_string(...)));
    }

    /** @return null|array{src: string, alt: string} */
    private function image(mixed $image): ?array
    {
        if (!is_array($image)) {
            return null;
        }

        $source = $image['src'] ?? null;
        $alt = $image['alt'] ?? null;
        if (!is_string($source) || preg_match('#^/assets/images/handbook/[a-z0-9._/-]+$#i', $source) !== 1 || !is_string($alt)) {
            throw new RuntimeException('A handbook image definition is invalid.');
        }

        return ['src' => $source, 'alt' => $alt];
    }
}
