<?php

declare(strict_types=1);

namespace WebWMS\Documentation\Application;

final class SafeMarkdownRenderer
{
    /** @return array{html: string, toc: list<array{level: int, id: string, title: string}>} */
    public function render(string $markdown, string $documentationPath): array
    {
        $lines = preg_split('/\R/', $markdown) ?: [];
        $html = [];
        $toc = [];
        $paragraph = [];
        $list = [];
        $listType = null;
        $code = [];
        $codeLanguage = '';

        $flushParagraph = function () use (&$paragraph, &$html, $documentationPath): void {
            if ($paragraph === []) {
                return;
            }
            $html[] = '<p>' . $this->inline(implode(' ', array_map('trim', $paragraph)), $documentationPath) . '</p>';
            $paragraph = [];
        };
        $flushList = function () use (&$list, &$listType, &$html, $documentationPath): void {
            if ($list === [] || $listType === null) {
                return;
            }
            $items = array_map(fn (string $item): string => '<li>' . $this->inline(trim($item), $documentationPath) . '</li>', $list);
            $html[] = sprintf('<%1$s>%2$s</%1$s>', $listType, implode('', $items));
            $list = [];
            $listType = null;
        };

        for ($index = 0, $count = count($lines); $index < $count; ++$index) {
            $line = rtrim($lines[$index]);
            if ($codeLanguage !== '') {
                if (str_starts_with($line, '```')) {
                    $html[] = '<pre><code class="language-' . htmlspecialchars($codeLanguage, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '">' . htmlspecialchars(implode("\n", $code), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</code></pre>';
                    $code = [];
                    $codeLanguage = '';
                } else {
                    $code[] = $line;
                }
                continue;
            }
            if (preg_match('/^```([a-z0-9_-]*)$/i', trim($line), $match) === 1) {
                $flushParagraph();
                $flushList();
                $codeLanguage = $match[1] === '' ? 'text' : strtolower($match[1]);
                continue;
            }
            if (preg_match('/^(#{1,6})\s+(.+)$/', $line, $match) === 1) {
                $flushParagraph();
                $flushList();
                $level = strlen($match[1]);
                $title = trim($match[2]);
                $id = $this->anchor($title);
                if ($level >= 2 && $level <= 3) {
                    $toc[] = ['level' => $level, 'id' => $id, 'title' => $title];
                }
                $html[] = sprintf('<h%d id="%s">%s</h%d>', $level, $id, $this->inline($title, $documentationPath), $level);
                continue;
            }
            if ($this->isTableHeader($lines, $index)) {
                $flushParagraph();
                $flushList();
                [$table, $lastLine] = $this->table($lines, $index, $documentationPath);
                $html[] = $table;
                $index = $lastLine;
                continue;
            }
            if (preg_match('/^\s*[-*]\s+(.+)$/', $line, $match) === 1 || preg_match('/^\s*\d+\.\s+(.+)$/', $line, $match) === 1) {
                $flushParagraph();
                $type = preg_match('/^\s*\d+\./', $line) === 1 ? 'ol' : 'ul';
                if ($listType !== null && $listType !== $type) {
                    $flushList();
                }
                $listType = $type;
                $list[] = $match[1];
                continue;
            }
            if ($line === '') {
                $flushParagraph();
                $flushList();
                continue;
            }
            if ($list !== []) {
                $list[array_key_last($list)] .= ' ' . trim($line);
            } else {
                $paragraph[] = $line;
            }
        }

        $flushParagraph();
        $flushList();
        if ($codeLanguage !== '') {
            $html[] = '<pre><code>' . htmlspecialchars(implode("\n", $code), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</code></pre>';
        }

        return ['html' => implode("\n", $html), 'toc' => $toc];
    }

    /** @param list<string> $lines */
    private function isTableHeader(array $lines, int $index): bool
    {
        if (!str_contains($lines[$index], '|') || !isset($lines[$index + 1])) {
            return false;
        }

        return preg_match('/^\s*\|?\s*:?-{3,}:?\s*(\|\s*:?-{3,}:?\s*)+\|?\s*$/', $lines[$index + 1]) === 1;
    }

    /** @param list<string> $lines @return array{string, int} */
    private function table(array $lines, int $index, string $documentationPath): array
    {
        $headers = $this->cells($lines[$index]);
        $rows = [];
        $cursor = $index + 2;
        while (isset($lines[$cursor]) && str_contains($lines[$cursor], '|') && trim($lines[$cursor]) !== '') {
            $rows[] = $this->cells($lines[$cursor]);
            ++$cursor;
        }
        $head = implode('', array_map(fn (string $cell): string => '<th>' . $this->inline($cell, $documentationPath) . '</th>', $headers));
        $body = '';
        foreach ($rows as $row) {
            $body .= '<tr>' . implode('', array_map(fn (string $cell): string => '<td>' . $this->inline($cell, $documentationPath) . '</td>', $row)) . '</tr>';
        }

        return ['<div class="table-responsive"><table class="table table-striped align-middle" data-table-tools="false"><thead><tr>' . $head . '</tr></thead><tbody>' . $body . '</tbody></table></div>', $cursor - 1];
    }

    /** @return list<string> */
    private function cells(string $line): array
    {
        return array_map('trim', explode('|', trim(trim($line), '|')));
    }

    private function inline(string $text, string $documentationPath): string
    {
        $tokens = [];
        $text = preg_replace_callback('/`([^`]+)`/', function (array $match) use (&$tokens): string {
            $token = '%%CODE' . count($tokens) . '%%';
            $tokens[$token] = '<code>' . htmlspecialchars($match[1], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</code>';

            return $token;
        }, $text) ?? $text;
        $text = preg_replace_callback('/\[([^]]+)]\(([^)]+)\)/', function (array $match) use (&$tokens, $documentationPath): string {
            $token = '%%LINK' . count($tokens) . '%%';
            $href = $this->link($match[2], $documentationPath);
            $tokens[$token] = '<a href="' . htmlspecialchars($href, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '">' . htmlspecialchars($match[1], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</a>';

            return $token;
        }, $text) ?? $text;
        $text = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $text = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $text) ?? $text;
        $text = preg_replace('/(?<!\*)\*([^*]+)\*(?!\*)/', '<em>$1</em>', $text) ?? $text;

        return strtr($text, $tokens);
    }

    private function link(string $target, string $documentationPath): string
    {
        if (preg_match('/^(https?:\/\/|mailto:|#)/i', $target) === 1) {
            return $target;
        }
        if (preg_match('/^(?:\.\/)?([a-z0-9][a-z0-9-]*)\.md(#[a-z0-9-]+)?$/i', $target, $match) === 1) {
            return rtrim($documentationPath, '/') . '/' . strtolower($match[1]) . ($match[2] ?? '');
        }

        return '#';
    }

    private function anchor(string $title): string
    {
        $normalized = strtr(mb_strtolower($title), ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss']);

        return trim(preg_replace('/[^a-z0-9]+/', '-', $normalized) ?? '', '-');
    }
}
