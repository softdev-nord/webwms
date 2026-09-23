<?php

declare(strict_types=1);

namespace WebWMS\Documentation\Application;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class UserDocumentationService
{
    private const array CATEGORY_ORDER = [
        'Grundlagen & Administration',
        'Lager & Bestand',
        'Wareneingang',
        'Warenausgang',
        'Integration & Technik',
    ];

    public function __construct(
        private string $projectDir
    ) {
    }

    /** @return array<string, list<array{slug: string, title: string, summary: string}>> */
    public function groupedDocuments(?string $query = null): array
    {
        $needle = mb_strtolower(trim((string) $query));
        $groups = array_fill_keys(self::CATEGORY_ORDER, []);

        foreach ($this->documentFiles() as $slug => $file) {
            $markdown = $this->read($file);
            $title = $this->title($markdown, $slug);
            $summary = $this->summary($markdown);
            if ($needle !== '' && !str_contains(mb_strtolower($title . ' ' . $summary . ' ' . $markdown), $needle)) {
                continue;
            }

            $groups[$this->category($slug)][] = ['slug' => $slug, 'title' => $title, 'summary' => $summary];
        }

        return array_filter($groups);
    }

    /** @return array{slug: string, title: string, markdown: string, previous: null|array{slug: string, title: string}, next: null|array{slug: string, title: string}} */
    public function document(string $slug): array
    {
        if (preg_match('/^[a-z0-9][a-z0-9-]*$/', $slug) !== 1) {
            throw new NotFoundHttpException('Die Dokumentationsseite wurde nicht gefunden.');
        }

        $documents = [];
        foreach ($this->groupedDocuments() as $group) {
            foreach ($group as $item) {
                $documents[] = ['slug' => $item['slug'], 'title' => $item['title']];
            }
        }

        $index = array_search($slug, array_column($documents, 'slug'), true);
        if ($index === false) {
            throw new NotFoundHttpException('Die Dokumentationsseite wurde nicht gefunden.');
        }

        $file = $this->documentFiles()[$slug] ?? null;
        if ($file === null) {
            throw new NotFoundHttpException('Die Dokumentationsseite wurde nicht gefunden.');
        }

        $markdown = $this->read($file);

        return [
            'slug' => $slug,
            'title' => $this->title($markdown, $slug),
            'markdown' => $markdown,
            'previous' => $index > 0 ? $documents[$index - 1] : null,
            'next' => isset($documents[$index + 1]) ? $documents[$index + 1] : null,
        ];
    }

    /** @return array<string, string> */
    private function documentFiles(): array
    {
        $files = glob($this->projectDir . '/docs/user/*.md');
        if ($files === false) {
            return [];
        }

        $documents = [];
        foreach ($files as $file) {
            $documents[pathinfo($file, PATHINFO_FILENAME)] = $file;
        }
        ksort($documents);

        return $documents;
    }

    private function read(string $file): string
    {
        $content = file_get_contents($file);
        if ($content === false) {
            throw new \RuntimeException(sprintf('Die Dokumentationsdatei "%s" konnte nicht gelesen werden.', basename($file)));
        }

        return $content;
    }

    private function title(string $markdown, string $slug): string
    {
        if (preg_match('/^#\s+(.+)$/m', $markdown, $match) === 1) {
            return trim($match[1]);
        }

        return ucfirst(str_replace('-', ' ', $slug));
    }

    private function summary(string $markdown): string
    {
        $withoutTitle = preg_replace('/^#\s+.+\R?/', '', $markdown, 1) ?? $markdown;
        $splitParagraphs = preg_split('/\R\s*\R/', trim($withoutTitle));
        $paragraphs = $splitParagraphs !== false ? $splitParagraphs : [];
        foreach ($paragraphs as $paragraph) {
            if ($paragraph === '' || str_starts_with(ltrim($paragraph), '#') || str_starts_with(ltrim($paragraph), '```')) {
                continue;
            }

            $text = preg_replace(['/\[([^]]+)]\([^)]+\)/', '/[`*_>#|-]/', '/\s+/'], ['$1', '', ' '], $paragraph) ?? $paragraph;

            return mb_strimwidth(trim($text), 0, 180, '…');
        }

        return 'Anwenderdokumentation für WebWMS 3.0.';
    }

    private function category(string $slug): string
    {
        if (preg_match('/^(getting-started|tenants-and-sites|access-security|administration-workspace|list-search-and-pagination|v3-demo)$/', $slug) === 1) {
            return 'Grundlagen & Administration';
        }
        if (preg_match('/^(inventory-inbound|planned-inbound|unplanned-receipts|inbound-discrepancy|inventory-putaway|inventory-replenishment)/', $slug) === 1) {
            return 'Wareneingang';
        }
        if (preg_match('/^(advanced-fulfillment|inventory-pick|inventory-packing|inventory-shipping|inventory-loading|inventory-returns)/', $slug) === 1) {
            return 'Warenausgang';
        }
        if (str_contains($slug, 'api') || str_contains($slug, 'integration') || in_array($slug, ['device-integration', 'measurement-integration', 'storage-automation', 'wcs-integration'], true)) {
            return 'Integration & Technik';
        }

        return 'Lager & Bestand';
    }
}
