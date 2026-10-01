<?php

declare(strict_types=1);

namespace WebWMS\Documentation\Application;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class UserDocumentationService
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

    public function __construct(
        private TranslatorInterface $translator
    ) {
    }

    /** @return array<string, list<array{slug: string, title: string, summary: string, match: null|array{anchor: string, excerpt: string}}>> */
    public function groupedDocuments(?string $query = null, ?string $locale = null): array
    {
        $needle = mb_strtolower(trim((string) $query));
        $groups = [];
        foreach (self::CATEGORY_ORDER as $category) {
            $groups[$this->translate('category.' . $category, $locale)] = [];
        }

        foreach (self::CHAPTERS as $chapter) {
            $document = $this->translatedDocument($chapter['slug'], $locale);
            $match = $needle === '' ? null : $this->match($document['body'], $needle);
            $haystack = mb_strtolower($document['title'] . ' ' . $document['summary'] . ' ' . $document['body']);
            if ($needle !== '' && !str_contains($haystack, $needle)) {
                continue;
            }

            $category = $this->translate('category.' . $chapter['category'], $locale);
            $groups[$category][] = [
                'slug' => $chapter['slug'],
                'title' => $document['title'],
                'summary' => $document['summary'],
                'match' => $match,
            ];
        }

        return array_filter($groups);
    }

    /** @return array{slug: string, category: string, title: string, summary: string, markdown: string, previous: null|array{slug: string, title: string}, next: null|array{slug: string, title: string}} */
    public function document(string $slug, ?string $locale = null): array
    {
        if (preg_match('/^[a-z0-9][a-z0-9-]*$/', $slug) !== 1) {
            throw new NotFoundHttpException($this->translate('error.not_found', $locale));
        }

        $index = array_search($slug, array_column(self::CHAPTERS, 'slug'), true);
        if ($index === false) {
            throw new NotFoundHttpException($this->translate('error.not_found', $locale));
        }

        $chapter = self::CHAPTERS[$index];
        $document = $this->translatedDocument($slug, $locale);

        return [
            'slug' => $slug,
            'category' => $this->translate('category.' . $chapter['category'], $locale),
            'title' => $document['title'],
            'summary' => $document['summary'],
            'markdown' => '# ' . $document['title'] . "\n\n" . $document['body'],
            'previous' => $index > 0 ? $this->navigation(self::CHAPTERS[$index - 1]['slug'], $locale) : null,
            'next' => isset(self::CHAPTERS[$index + 1]) ? $this->navigation(self::CHAPTERS[$index + 1]['slug'], $locale) : null,
        ];
    }

    /** @return array{title: string, summary: string, body: string} */
    private function translatedDocument(string $slug, ?string $locale): array
    {
        $prefix = 'chapter.' . str_replace('-', '_', $slug) . '.';

        return [
            'title' => $this->translate($prefix . 'title', $locale),
            'summary' => $this->translate($prefix . 'summary', $locale),
            'body' => $this->translate($prefix . 'body', $locale),
        ];
    }

    /** @return array{slug: string, title: string} */
    private function navigation(string $slug, ?string $locale): array
    {
        return ['slug' => $slug, 'title' => $this->translatedDocument($slug, $locale)['title']];
    }

    /** @return null|array{anchor: string, excerpt: string} */
    private function match(string $body, string $needle): ?array
    {
        $lines = preg_split('/\R/', $body) ?: [];
        $anchor = '';
        foreach ($lines as $line) {
            if (preg_match('/^#{2,3}\s+(.+)$/', trim($line), $heading) === 1) {
                $anchor = $this->anchor($heading[1]);
            }
            if (!str_contains(mb_strtolower($line), $needle)) {
                continue;
            }

            $plain = preg_replace(['/!\[([^]]*)]\([^)]+\)/', '/\[([^]]+)]\([^)]+\)/', '/[`*_>#|-]/', '/\s+/'], ['$1', '$1', '', ' '], $line) ?? $line;

            return ['anchor' => $anchor, 'excerpt' => mb_strimwidth(trim($plain), 0, 220, '…')];
        }

        return null;
    }

    private function translate(string $key, ?string $locale): string
    {
        return $this->translator->trans($key, [], 'handbook', $locale);
    }

    private function anchor(string $title): string
    {
        $normalized = strtr(mb_strtolower($title), ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss']);

        return trim(preg_replace('/[^a-z0-9]+/', '-', $normalized) ?? '', '-');
    }
}
