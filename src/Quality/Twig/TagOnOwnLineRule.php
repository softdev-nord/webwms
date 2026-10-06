<?php

declare(strict_types=1);

namespace WebWMS\Quality\Twig;

use TwigCsFixer\Rules\AbstractFixableRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

/**
 * Ensures that Twig block tags and HTML tags are placed on separate lines.
 *
 * Twig output expressions and Twig blocks inside HTML attributes are preserved
 * because introducing line breaks there could alter attribute values.
 */
class TagOnOwnLineRule extends AbstractFixableRule
{
    private bool $insideHtmlTag = false;

    private ?string $htmlAttributeQuote = null;

    private string $lineBreak = "\n";

    protected function process(int $tokenIndex, Tokens $tokens): void
    {
        if ($tokenIndex === 0) {
            $this->resetState($tokens);
        }

        $token = $tokens->get($tokenIndex);

        if ($token->isMatching(Token::BLOCK_START_TYPE)) {
            $this->processTwigBlockStart(
                tokenIndex: $tokenIndex,
                tokens: $tokens,
            );

            return;
        }

        if ($token->isMatching(Token::BLOCK_END_TYPE)) {
            $this->processTwigBlockEnd(
                tokenIndex: $tokenIndex,
                tokens: $tokens,
            );

            return;
        }

        if ($token->isMatching(Token::TEXT_TYPE)) {
            $this->processHtmlContent(
                tokenIndex: $tokenIndex,
                token: $token,
                tokens: $tokens,
            );
        }
    }

    private function resetState(Tokens $tokens): void
    {
        $this->insideHtmlTag = false;
        $this->htmlAttributeQuote = null;
        $this->lineBreak = $this->detectLineBreak($tokens);
    }

    private function processTwigBlockStart(
        int $tokenIndex,
        Tokens $tokens,
    ): void {
        /*
         * Twig-Steuerstrukturen innerhalb eines HTML-Tags beziehungsweise
         * eines Attributwertes dürfen nicht auf eine eigene Zeile verschoben
         * werden.
         *
         * Beispiel:
         *
         * <div class="{% if active %}active{% endif %}">
         */
        if ($this->insideHtmlTag || $this->isInsideHtmlTag($tokenIndex, $tokens)) {
            return;
        }

        if (!$this->hasContentBeforeOnSameLine($tokenIndex, $tokens)) {
            return;
        }

        $fixer = $this->addFixableError(
            'A Twig block tag must start on its own line.',
            $tokens->get($tokenIndex),
        );

        $fixer?->addNewlineBefore($tokenIndex);
    }

    private function processTwigBlockEnd(
        int $tokenIndex,
        Tokens $tokens,
    ): void {
        $blockStartIndex = $this->findBlockStart(
            tokenIndex: $tokenIndex,
            tokens: $tokens,
        );

        if (
            $this->insideHtmlTag
            || (
                $blockStartIndex !== null
                && $this->isInsideHtmlTag($blockStartIndex, $tokens)
            )
        ) {
            return;
        }

        if (!$this->hasContentAfterOnSameLine($tokenIndex, $tokens)) {
            return;
        }

        $fixer = $this->addFixableError(
            'A Twig block tag must end on its own line.',
            $tokens->get($tokenIndex),
        );

        $fixer?->addNewline($tokenIndex);
    }

    private function processHtmlContent(
        int $tokenIndex,
        Token $token,
        Tokens $tokens,
    ): void {
        $originalContent = $token->getValue();
        $formattedContent = '';
        $length = strlen($originalContent);

        for ($position = 0; $position < $length; ++$position) {
            $character = $originalContent[$position];
            $nextCharacter = $originalContent[$position + 1] ?? null;

            if (
                !$this->insideHtmlTag
                && $character === '<'
                && $this->isHtmlTagStart($nextCharacter)
            ) {
                $formattedContent = $this->addLineBreakBeforeHtmlTag(
                    formattedContent: $formattedContent,
                    tokenIndex: $tokenIndex,
                    characterPosition: $position,
                    tokens: $tokens,
                );

                $this->insideHtmlTag = true;
            }

            if ($this->insideHtmlTag) {
                $this->processAttributeQuote(
                    character: $character,
                    content: $originalContent,
                    position: $position,
                );
            }

            $formattedContent .= $character;

            if (
                $this->insideHtmlTag
                && $this->htmlAttributeQuote === null
                && $character === '>'
            ) {
                $this->insideHtmlTag = false;

                [$formattedContent, $position] = $this->addLineBreakAfterHtmlTag(
                    formattedContent: $formattedContent,
                    originalContent: $originalContent,
                    characterPosition: $position,
                    tokenIndex: $tokenIndex,
                    tokens: $tokens,
                );
            }
        }

        if ($formattedContent === $originalContent) {
            return;
        }

        $fixer = $this->addFixableError(
            'HTML tags must be placed on separate lines.',
            $token,
        );

        $fixer?->replaceToken(
            $tokenIndex,
            $formattedContent,
        );
    }

    private function addLineBreakBeforeHtmlTag(
        string $formattedContent,
        int $tokenIndex,
        int $characterPosition,
        Tokens $tokens,
    ): string {
        $hasContentBefore = trim($formattedContent) !== ''
            || (
                $characterPosition === 0
                && $this->hasContentBeforeOnSameLine(
                    tokenIndex: $tokenIndex,
                    tokens: $tokens,
                )
            );

        if (!$hasContentBefore) {
            return $formattedContent;
        }

        $formattedContent = rtrim(
            $formattedContent,
            " \t",
        );

        if ($this->endsWithLineBreak($formattedContent)) {
            return $formattedContent;
        }

        return $formattedContent . $this->lineBreak;
    }

    /**
     * @return array{string, int}
     */
    private function addLineBreakAfterHtmlTag(
        string $formattedContent,
        string $originalContent,
        int $characterPosition,
        int $tokenIndex,
        Tokens $tokens,
    ): array {
        $remainingContent = substr(
            $originalContent,
            $characterPosition + 1,
        );

        $hasContentAfter = trim($remainingContent) !== ''
            || $this->hasContentAfterOnSameLine(
                tokenIndex: $tokenIndex,
                tokens: $tokens,
            );

        if (!$hasContentAfter) {
            return [$formattedContent, $characterPosition];
        }

        /*
         * Horizontale Leerzeichen zwischen zwei Tags werden durch genau
         * einen Zeilenumbruch ersetzt.
         */
        while (
            isset($originalContent[$characterPosition + 1])
            && (
                $originalContent[$characterPosition + 1] === ' '
                || $originalContent[$characterPosition + 1] === "\t"
            )
        ) {
            ++$characterPosition;
        }

        if (!$this->endsWithLineBreak($formattedContent)) {
            $formattedContent .= $this->lineBreak;
        }

        return [$formattedContent, $characterPosition];
    }

    private function processAttributeQuote(
        string $character,
        string $content,
        int $position,
    ): void {
        if (
            $this->htmlAttributeQuote === null
            && ($character === '"' || $character === "'")
        ) {
            $this->htmlAttributeQuote = $character;

            return;
        }

        if (
            $this->htmlAttributeQuote !== null
            && $character === $this->htmlAttributeQuote
            && !$this->isEscaped(
                content: $content,
                position: $position,
            )
        ) {
            $this->htmlAttributeQuote = null;
        }
    }

    private function isHtmlTagStart(?string $character): bool
    {
        if ($character === null) {
            return false;
        }

        return $character === '/'
            || $character === '!'
            || $character === '?'
            || ctype_alpha($character);
    }

    private function hasContentBeforeOnSameLine(
        int $tokenIndex,
        Tokens $tokens,
    ): bool {
        for ($index = $tokenIndex - 1; $index >= 0; --$index) {
            $token = $tokens->get($index);

            if ($token->isMatching(Token::EOL_TYPE)) {
                return false;
            }

            if (
                !$token->isMatching(Token::WHITESPACE_TOKENS)
                && !$token->isMatching(Token::TAB_TOKENS)
            ) {
                return true;
            }
        }

        return false;
    }

    private function hasContentAfterOnSameLine(
        int $tokenIndex,
        Tokens $tokens,
    ): bool {
        for ($index = $tokenIndex + 1; $tokens->has($index); ++$index) {
            $token = $tokens->get($index);

            if (
                $token->isMatching(Token::EOL_TYPE)
                || $token->isMatching(Token::EOF_TYPE)
            ) {
                return false;
            }

            if (
                !$token->isMatching(Token::WHITESPACE_TOKENS)
                && !$token->isMatching(Token::TAB_TOKENS)
            ) {
                return true;
            }
        }

        return false;
    }

    private function findBlockStart(
        int $tokenIndex,
        Tokens $tokens,
    ): ?int {
        for ($index = $tokenIndex - 1; $index >= 0; --$index) {
            if ($tokens->get($index)->isMatching(Token::BLOCK_START_TYPE)) {
                return $index;
            }
        }

        return null;
    }

    private function isInsideHtmlTag(
        int $tokenIndex,
        Tokens $tokens,
    ): bool {
        $lineBeforeTag = '';

        for ($index = $tokenIndex - 1; $index >= 0; --$index) {
            $token = $tokens->get($index);

            if ($token->isMatching(Token::EOL_TYPE)) {
                break;
            }

            $lineBeforeTag = $token->getValue() . $lineBeforeTag;
        }

        $lastOpeningBracket = strrpos(
            $lineBeforeTag,
            '<',
        );

        $lastClosingBracket = strrpos(
            $lineBeforeTag,
            '>',
        );

        return $lastOpeningBracket !== false
            && (
                $lastClosingBracket === false
                || $lastOpeningBracket > $lastClosingBracket
            );
    }

    private function isEscaped(
        string $content,
        int $position,
    ): bool {
        $backslashes = 0;

        for ($index = $position - 1; $index >= 0; --$index) {
            if ($content[$index] !== '\\') {
                break;
            }

            ++$backslashes;
        }

        return $backslashes % 2 !== 0;
    }

    private function endsWithLineBreak(string $content): bool
    {
        return str_ends_with($content, "\n")
            || str_ends_with($content, "\r");
    }

    private function detectLineBreak(Tokens $tokens): string
    {
        foreach ($tokens->toArray() as $token) {
            if ($token->isMatching(Token::EOL_TYPE)) {
                return $token->getValue();
            }
        }

        return PHP_EOL;
    }
}