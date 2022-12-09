<?php

declare(strict_types=1);

namespace WebWMS\Bundles\BundleGeneratorBundle\Manipulator;

/**
 * @package:    WebWMS\Bundles\BundleGeneratorBundle\Manipulator
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        Manipulator
 */
class Manipulator
{
    protected array $tokens;
    protected int $line;

    /**
     * Sets the code to manipulate.
     *
     * @param array $tokens An array of PHP tokens
     * @param int   $line   The start line of the code
     */
    protected function setCode(array $tokens, int $line = 0)
    {
        $this->tokens = $tokens;
        $this->line = $line;
    }

    /**
     * Gets the next token.
     */
    protected function next(): ?string
    {
        while ($token = array_shift($this->tokens)) {
            $this->line += substr_count($this->value($token), "\n");

            if (is_array($token) && in_array($token[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT])) {
                continue;
            }

            return $token;
        }

        return null;
    }

    /**
     * Peeks the next token.
     */
    protected function peek(int $nb = 1): ?string
    {
        $i = 0;
        $tokens = $this->tokens;
        while ($token = array_shift($tokens)) {
            if (is_array($token) && in_array($token[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT])) {
                continue;
            }

            ++$i;
            if ($i == $nb) {
                return $token;
            }
        }

        return null;
    }

    /**
     * Gets the value of a token.
     *
     * @param string|string[] $token The token value
     */
    protected function value(array|string $token): string
    {
        return is_array($token) ? $token[1] : $token;
    }
}
