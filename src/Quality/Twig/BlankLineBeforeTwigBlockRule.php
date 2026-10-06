<?php

declare(strict_types=1);

namespace WebWMS\Quality\Twig;

use TwigCsFixer\Rules\AbstractFixableRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

/**
 * Ensures that every opening Twig block is preceded by one blank line.
 */
class BlankLineBeforeTwigBlockRule extends AbstractFixableRule
{
    protected function process(int $tokenIndex, Tokens $tokens): void
    {
        $token = $tokens->get($tokenIndex);

        if (!$token->isMatching(Token::BLOCK_START_TYPE)) {
            return;
        }

        if (!$this->isOpeningBlock($tokenIndex, $tokens)) {
            return;
        }

        /*
         * Vor dem ersten Inhalt einer Datei ist keine Leerzeile erforderlich.
         */
        if (!$this->hasPreviousContent($tokenIndex, $tokens)) {
            return;
        }

        $lineBreakCount = $this->countPreviousLineBreaks(
            tokenIndex: $tokenIndex,
            tokens: $tokens,
        );

        /*
         * Zwei Zeilenumbrüche entsprechen genau einer Leerzeile.
         */
        if ($lineBreakCount >= 2) {
            return;
        }

        $fixer = $this->addFixableError(
            'An opening Twig block must be preceded by a blank line.',
            $token,
        );

        if ($fixer === null) {
            return;
        }

        for (
            $missingLineBreaks = 2 - $lineBreakCount;
            $missingLineBreaks > 0;
            --$missingLineBreaks
        ) {
            $fixer->addNewlineBefore($tokenIndex);
        }
    }

    private function isOpeningBlock(
        int $tokenIndex,
        Tokens $tokens,
    ): bool {
        for ($index = $tokenIndex + 1; $tokens->has($index); ++$index) {
            $token = $tokens->get($index);

            if ($token->isMatching(Token::BLOCK_END_TYPE)) {
                return false;
            }

            if (
                $token->isMatching(
                    Token::BLOCK_NAME_TYPE,
                    'block',
                )
            ) {
                return true;
            }

            if (
                !$token->isMatching(Token::WHITESPACE_TOKENS)
                && !$token->isMatching(Token::TAB_TOKENS)
            ) {
                return false;
            }
        }

        return false;
    }

    private function hasPreviousContent(
        int $tokenIndex,
        Tokens $tokens,
    ): bool {
        for ($index = $tokenIndex - 1; $index >= 0; --$index) {
            $token = $tokens->get($index);

            if (
                $token->isMatching(Token::WHITESPACE_TOKENS)
                || $token->isMatching(Token::TAB_TOKENS)
                || $token->isMatching(Token::EOL_TYPE)
            ) {
                continue;
            }

            return true;
        }

        return false;
    }

    private function countPreviousLineBreaks(
        int $tokenIndex,
        Tokens $tokens,
    ): int {
        $lineBreakCount = 0;

        for ($index = $tokenIndex - 1; $index >= 0; --$index) {
            $token = $tokens->get($index);

            if ($token->isMatching(Token::EOL_TYPE)) {
                ++$lineBreakCount;

                continue;
            }

            if (
                $token->isMatching(Token::WHITESPACE_TOKENS)
                || $token->isMatching(Token::TAB_TOKENS)
            ) {
                continue;
            }

            break;
        }

        return $lineBreakCount;
    }
}
