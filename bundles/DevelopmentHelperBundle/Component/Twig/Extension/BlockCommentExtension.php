<?php

declare(strict_types=1);

namespace WebWMS\Bundles\DevelopmentHelperBundle\Component\Twig\Extension;

use Twig\Extension\AbstractExtension;
use WebWMS\Bundles\DevelopmentHelperBundle\Component\Twig\NodeVisitor\BlogCommentNodeVisitor;

class BlockCommentExtension extends AbstractExtension
{
    public function __construct(
        private string $kernelRootDir,
        private array $twigExcludeKeywords
    ) {
    }

    public function getNodeVisitors()
    {
        return [new BlogCommentNodeVisitor($this->kernelRootDir, $this->twigExcludeKeywords)];
    }
}
