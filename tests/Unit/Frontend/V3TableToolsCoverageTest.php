<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Frontend;

use PHPUnit\Framework\TestCase;

final class V3TableToolsCoverageTest extends TestCase
{
    public function testEveryV3TemplateTableParticipatesInSharedListNavigation(): void
    {
        $projectDir = dirname(__DIR__, 3);
        $templates = glob($projectDir . '/templates/v3/**/*.html.twig');
        self::assertIsArray($templates);

        $uncovered = [];
        foreach ($templates as $template) {
            $content = file_get_contents($template);
            self::assertIsString($content);
            preg_match_all('/<table\b[^>]*>/', $content, $matches);
            foreach ($matches[0] as $table) {
                if (str_contains($table, 'data-table-tools="false"')) {
                    continue;
                }
                if (preg_match('/class="[^"]*\btable\b[^"]*"/', $table) !== 1) {
                    $uncovered[] = basename($template) . ': ' . $table;
                }
            }
        }

        self::assertSame([], $uncovered, 'V3 tables without shared search and pagination: ' . implode(', ', $uncovered));
    }

    public function testSharedAssetsAreLoadedByV3Layout(): void
    {
        $projectDir = dirname(__DIR__, 3);
        $head = file_get_contents($projectDir . '/templates/v3/subsections/head-js.html.twig');

        self::assertIsString($head);
        self::assertStringContainsString('dataTables.bootstrap5.js', $head);
        self::assertStringContainsString('v3-table-tools.js', $head);
        self::assertFileExists($projectDir . '/public/assets/js/v3-table-tools.js');
    }
}
