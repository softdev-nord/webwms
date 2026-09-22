<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Documentation\Application;

use PHPUnit\Framework\TestCase;
use WebWMS\Documentation\Application\SafeMarkdownRenderer;

final class SafeMarkdownRendererTest extends TestCase
{
    public function testRendersNavigationTablesAndEscapedContent(): void
    {
        $markdown = <<<'MARKDOWN'
# Anleitung

## Bestand prüfen

Siehe [Picking](picking-api.md) und `<script>alert(1)</script>`.

| Status | Bedeutung |
|---|---|
| `open` | Offen |
MARKDOWN;

        $result = (new SafeMarkdownRenderer())->render($markdown, '/v3/help');

        self::assertStringContainsString('href="/v3/help/picking-api"', $result['html']);
        self::assertStringContainsString('&lt;script&gt;', $result['html']);
        self::assertStringNotContainsString('<script>', $result['html']);
        self::assertStringContainsString('<table class="table table-striped align-middle" data-table-tools="false">', $result['html']);
        self::assertSame([['level' => 2, 'id' => 'bestand-pruefen', 'title' => 'Bestand prüfen']], $result['toc']);
    }

    public function testRejectsUnsafeLinkTargets(): void
    {
        $result = (new SafeMarkdownRenderer())->render('[Öffnen](javascript:alert(1))', '/v3/help');

        self::assertStringContainsString('href="#"', $result['html']);
        self::assertStringNotContainsString('javascript:', $result['html']);
    }
}
