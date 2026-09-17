<?php

declare(strict_types=1);

namespace SoftDevNord\TestGenerator;

use SoftDevNord\TestGenerator\DependencyInjection\TestGeneratorExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SoftDevNordTestGenerator extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {

        if ($this->extension === null) {
            $this->extension = new TestGeneratorExtension();
        }

        return $this->extension;
    }
}
