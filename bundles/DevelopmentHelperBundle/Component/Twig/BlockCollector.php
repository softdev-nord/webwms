<?php

declare(strict_types=1);

namespace WebWMS\Bundles\DevelopmentHelperBundle\Component\Twig;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

class BlockCollector
{
    public function __construct(
        private KernelInterface $kernel
    ) {
    }

    public function getBlocks(): array
    {
        $path = $this->kernel->getProjectDir().'/templates/';

        $finder = (new Finder())
            ->in($path)
            ->files()
            ->name('*.html.twig');

        $collectedBlocks = [];
        $regex = '/{%\s* block\s*([\w_]+)\s*%}/m';

        foreach ($finder->getIterator() as $file) {
            $fileContent = file_get_contents($file->getPathname());
            preg_match_all($regex, $fileContent, $matches, PREG_SET_ORDER, 0);

            foreach ($matches as $match) {
                $collectedBlocks[$match[1]][str_replace($path, '', $file->getPathname())] = 1;
            }
        }

        return $collectedBlocks;
    }
}
