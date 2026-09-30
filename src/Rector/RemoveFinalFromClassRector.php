<?php

declare(strict_types=1);

namespace WebWMS\Rector;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use Rector\Rector\AbstractRector;

class RemoveFinalFromClassRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof Class_ || !$node->isFinal()) {
            return null;
        }

        $node->flags &= ~Class_::MODIFIER_FINAL;

        return $node;
    }
}
