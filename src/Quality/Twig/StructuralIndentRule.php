<?php

declare(strict_types=1);

namespace WebWMS\Quality\Twig;

use TwigCsFixer\Rules\AbstractFixableRule;
use TwigCsFixer\Rules\ConfigurableRuleInterface;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

class StructuralIndentRule extends AbstractFixableRule implements ConfigurableRuleInterface
{
    /**
     * @var list<string>
     */
    private const OPENING_TWIG_TAGS = [
        'apply',
        'autoescape',
        'block',
        'cache',
        'component',
        'embed',
        'for',
        'if',
        'macro',
        'sandbox',
        'verbatim',
        'with',
    ];

    /**
     * @var array<string, string>
     */
    private const CLOSING_TWIG_TAGS = [
        'endapply' => 'apply',
        'endautoescape' => 'autoescape',
        'endblock' => 'block',
        'endcache' => 'cache',
        'endcomponent' => 'component',
        'endembed' => 'embed',
        'endfor' => 'for',
        'endif' => 'if',
        'endmacro' => 'macro',
        'endsandbox' => 'sandbox',
        'endverbatim' => 'verbatim',
        'endwith' => 'with',
    ];

    /**
     * @var array<string, list<string>>
     */
    private const BRANCH_TWIG_TAGS = [
        'case' => ['switch'],
        'default' => ['switch'],
        'else' => ['if', 'for'],
        'elseif' => ['if'],
    ];

    /**
     * @var list<string>
     */
    private const VOID_HTML_TAGS = [
        'area',
        'base',
        'br',
        'col',
        'embed',
        'hr',
        'img',
        'input',
        'link',
        'meta',
        'param',
        'source',
        'track',
        'wbr',
    ];

    public function __construct(
        private readonly int $spacesPerLevel = 4,
    ) {
        if ($this->spacesPerLevel < 1) {
            throw new \InvalidArgumentException(
                'The number of spaces per indentation level must be positive.',
            );
        }
    }

    /**
     * @return array{spacesPerLevel: int}
     */
    public function getConfiguration(): array
    {
        return [
            'spacesPerLevel' => $this->spacesPerLevel,
        ];
    }

    protected function process(int $tokenIndex, Tokens $tokens): void
    {
        if ($tokenIndex !== 0) {
            return;
        }

        /**
         * @var list<string> $structure
         *
         * Einträge besitzen beispielsweise folgende Form:
         *
         * - twig:block
         * - twig:if
         * - html:div
         * - html:form
         */
        $structure = [];

        foreach ($this->createLines($tokens) as $line) {
            $trimmedContent = trim($line['content']);

            if ($trimmedContent === '') {
                continue;
            }

            $twigTag = $this->findLeadingTwigTag($trimmedContent);
            $htmlTags = $this->findHtmlTags($trimmedContent);

            /*
             * Ein schließender Twig-Tag wird vor der Einrückung aus dem
             * Struktur-Stack entfernt. Dadurch steht er auf derselben Höhe
             * wie sein öffnender Tag.
             */
            if (
                $twigTag !== null
                && isset(self::CLOSING_TWIG_TAGS[$twigTag])
            ) {
                $this->closeStructure(
                    structure: $structure,
                    expectedEntry: sprintf(
                        'twig:%s',
                        self::CLOSING_TWIG_TAGS[$twigTag],
                    ),
                );
            }

            /*
             * else und elseif stehen auf derselben Höhe wie das zugehörige
             * if beziehungsweise for. Der übergeordnete Twig-Eintrag wird
             * deshalb vor der Einrückung entfernt und anschließend wieder
             * geöffnet.
             */
            $branchParent = null;

            if (
                $twigTag !== null
                && isset(self::BRANCH_TWIG_TAGS[$twigTag])
            ) {
                $branchParent = $this->closeBranchStructure(
                    structure: $structure,
                    expectedParents: self::BRANCH_TWIG_TAGS[$twigTag],
                );
            }

            /*
             * Wenn die Zeile mit einem schließenden HTML-Tag beginnt, muss
             * dieses vor der Einrückung aus dem Stack entfernt werden.
             */
            $leadingClosingHtmlTag = $this->findLeadingClosingHtmlTag(
                content: $trimmedContent,
                htmlTags: $htmlTags,
            );

            if ($leadingClosingHtmlTag !== null) {
                $this->closeStructure(
                    structure: $structure,
                    expectedEntry: sprintf(
                        'html:%s',
                        $leadingClosingHtmlTag,
                    ),
                );
            }

            $this->fixLineIndentation(
                line: $line,
                expectedIndentation: str_repeat(
                    ' ',
                    count($structure) * $this->spacesPerLevel,
                ),
                tokens: $tokens,
            );

            /*
             * Öffnende Twig-Strukturen gelten erst für nachfolgende Zeilen.
             */
            if (
                $twigTag !== null
                && in_array(
                    $twigTag,
                    self::OPENING_TWIG_TAGS,
                    true,
                )
            ) {
                $structure[] = sprintf('twig:%s', $twigTag);
            }

            /*
             * Die von einem Branch vorübergehend geschlossene Struktur wird
             * nach der Branch-Zeile erneut geöffnet.
             */
            if ($branchParent !== null) {
                $structure[] = $branchParent;
            }

            /*
             * HTML-Tags werden in ihrer tatsächlichen Reihenfolge verarbeitet.
             * Der bereits vor der Einrückung behandelte erste Closing-Tag wird
             * hierbei übersprungen.
             */
            $leadingClosingTagSkipped = false;

            foreach ($htmlTags as $htmlTag) {
                if ($htmlTag['closing']) {
                    if (
                        !$leadingClosingTagSkipped
                        && $leadingClosingHtmlTag !== null
                        && $htmlTag['name'] === $leadingClosingHtmlTag
                    ) {
                        $leadingClosingTagSkipped = true;

                        continue;
                    }

                    $this->closeStructure(
                        structure: $structure,
                        expectedEntry: sprintf(
                            'html:%s',
                            $htmlTag['name'],
                        ),
                    );

                    continue;
                }

                if (
                    $htmlTag['selfClosing']
                    || in_array(
                        $htmlTag['name'],
                        self::VOID_HTML_TAGS,
                        true,
                    )
                ) {
                    continue;
                }

                $structure[] = sprintf(
                    'html:%s',
                    $htmlTag['name'],
                );
            }
        }
    }

    /**
     * @param list<string> $structure
     */
    private function closeStructure(
        array &$structure,
        string $expectedEntry,
    ): void {
        for ($index = count($structure) - 1; $index >= 0; --$index) {
            if ($structure[$index] !== $expectedEntry) {
                continue;
            }

            /*
             * Der gefundene Eintrag und gegebenenfalls noch darüberliegende,
             * nicht korrekt geschlossene Strukturen werden entfernt.
             */
            array_splice($structure, $index);

            return;
        }
    }

    /**
     * @param list<string> $structure
     * @param list<string> $expectedParents
     */
    private function closeBranchStructure(
        array &$structure,
        array $expectedParents,
    ): ?string {
        for ($index = count($structure) - 1; $index >= 0; --$index) {
            foreach ($expectedParents as $expectedParent) {
                $expectedEntry = sprintf(
                    'twig:%s',
                    $expectedParent,
                );

                if ($structure[$index] !== $expectedEntry) {
                    continue;
                }

                array_splice($structure, $index);

                return $expectedEntry;
            }
        }

        return null;
    }

    /**
     * @param list<array{
     *     name: string,
     *     closing: bool,
     *     selfClosing: bool
     * }> $htmlTags
     */
    private function findLeadingClosingHtmlTag(
        string $content,
        array $htmlTags,
    ): ?string {
        if ($htmlTags === []) {
            return null;
        }

        if (
            preg_match(
                '/^<\s*\/\s*([a-zA-Z][a-zA-Z0-9:-]*)\b/',
                $content,
                $matches,
            ) !== 1
        ) {
            return null;
        }

        return strtolower($matches[1]);
    }

    /**
     * @return list<array{
     *     name: string,
     *     closing: bool,
     *     selfClosing: bool
     * }>
     */
    private function findHtmlTags(string $content): array
    {
        $pattern = <<<'REGEX'
            ~
            <
            \s*
            (?<closing>/)?
            \s*
            (?<name>[a-zA-Z][a-zA-Z0-9:-]*)
            \b
            (?:
                [^>"']+
                |
                "[^"]*"
                |
                '[^']*'
            )*
            >
            ~ix
            REGEX;

        $result = preg_match_all(
            $pattern,
            $content,
            $matches,
            PREG_SET_ORDER,
        );

        if ($result === false || $result === 0) {
            return [];
        }

        $htmlTags = [];

        foreach ($matches as $match) {
            $completeTag = $match[0];

            $htmlTags[] = [
                'name' => strtolower($match['name']),
                'closing' => ($match['closing'] ?? '') === '/',
                'selfClosing' => str_ends_with(
                    rtrim($completeTag),
                    '/>',
                ),
            ];
        }

        return $htmlTags;
    }

    private function findLeadingTwigTag(string $content): ?string
    {
        if (
            preg_match(
                '/^\{%[-~]?\s*([a-zA-Z_][a-zA-Z0-9_]*)\b/',
                $content,
                $matches,
            ) !== 1
        ) {
            return null;
        }

        return strtolower($matches[1]);
    }

    /**
     * @return list<array{
     *     content: string,
     *     firstTokenIndex: int,
     *     indentationTokenIndexes: list<int>
     * }>
     */
    private function createLines(Tokens $tokens): array
    {
        $lines = [];
        $content = '';
        $firstTokenIndex = null;
        $indentationTokenIndexes = [];
        $collectingIndentation = true;

        foreach ($tokens->toArray() as $index => $token) {
            if ($token->isMatching(Token::EOF_TYPE)) {
                break;
            }

            if ($token->isMatching(Token::EOL_TYPE)) {
                $lines[] = [
                    'content' => $content,
                    'firstTokenIndex' => $firstTokenIndex ?? $index,
                    'indentationTokenIndexes' => $indentationTokenIndexes,
                ];

                $content = '';
                $firstTokenIndex = null;
                $indentationTokenIndexes = [];
                $collectingIndentation = true;

                continue;
            }

            $firstTokenIndex ??= $index;
            $content .= $token->getValue();

            if (
                $collectingIndentation
                && (
                    $token->isMatching(Token::WHITESPACE_TOKENS)
                    || $token->isMatching(Token::TAB_TOKENS)
                )
            ) {
                $indentationTokenIndexes[] = $index;

                continue;
            }

            $collectingIndentation = false;
        }

        if ($content !== '' || $firstTokenIndex !== null) {
            $lines[] = [
                'content' => $content,
                'firstTokenIndex' => $firstTokenIndex ?? 0,
                'indentationTokenIndexes' => $indentationTokenIndexes,
            ];
        }

        return $lines;
    }

    /**
     * @param array{
     *     content: string,
     *     firstTokenIndex: int,
     *     indentationTokenIndexes: list<int>
     * } $line
     */
    private function fixLineIndentation(
        array $line,
        string $expectedIndentation,
        Tokens $tokens,
    ): void {
        $actualIndentation = $this->extractIndentation(
            $line['content'],
        );

        if ($actualIndentation === $expectedIndentation) {
            return;
        }

        $firstContentTokenIndex = $this->findFirstContentTokenIndex(
            line: $line,
            tokens: $tokens,
        );

        if ($firstContentTokenIndex === null) {
            return;
        }

        $fixer = $this->addFixableError(
            sprintf(
                'Expected %d spaces of indentation, found %d.',
                strlen($expectedIndentation),
                $this->indentationWidth($actualIndentation),
            ),
            $tokens->get($firstContentTokenIndex),
        );

        if ($fixer === null) {
            return;
        }

        $indentationTokenIndexes = $line['indentationTokenIndexes'];

        if ($indentationTokenIndexes === []) {
            if ($expectedIndentation !== '') {
                $fixer->addContentBefore(
                    $firstContentTokenIndex,
                    $expectedIndentation,
                );
            }

            return;
        }

        $fixer->beginChangeSet();

        foreach ($indentationTokenIndexes as $position => $index) {
            $fixer->replaceToken(
                $index,
                $position === 0
                    ? $expectedIndentation
                    : '',
            );
        }

        $fixer->endChangeSet();
    }

    /**
     * @param array{
     *     content: string,
     *     firstTokenIndex: int,
     *     indentationTokenIndexes: list<int>
     * } $line
     */
    private function findFirstContentTokenIndex(
        array $line,
        Tokens $tokens,
    ): ?int {
        $index = $line['firstTokenIndex'];

        while ($tokens->has($index)) {
            $token = $tokens->get($index);

            if (
                !$token->isMatching(Token::WHITESPACE_TOKENS)
                && !$token->isMatching(Token::TAB_TOKENS)
                && !$token->isMatching(Token::EOL_TYPE)
                && !$token->isMatching(Token::EOF_TYPE)
            ) {
                return $index;
            }

            ++$index;
        }

        return null;
    }

    private function extractIndentation(string $content): string
    {
        preg_match('/^[\t ]*/', $content, $matches);

        return $matches[0] ?? '';
    }

    private function indentationWidth(string $indentation): int
    {
        return strlen(
            str_replace(
                "\t",
                str_repeat(' ', $this->spacesPerLevel),
                $indentation,
            ),
        );
    }
}
