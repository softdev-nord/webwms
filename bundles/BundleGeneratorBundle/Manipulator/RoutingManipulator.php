<?php

declare(strict_types=1);

namespace WebWMS\Bundles\BundleGeneratorBundle\Manipulator;

use Symfony\Component\DependencyInjection\Container;
use WebWMS\Bundles\BundleGeneratorBundle\Generator\Generator;

/**
 * @package:    WebWMS\Bundles\BundleGeneratorBundle\Manipulator
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        RoutingManipulator
 */
class RoutingManipulator extends Manipulator
{
    private string $file;

    /**
     * @param string $file The YAML routing file path
     */
    public function __construct(string $file)
    {
        $this->file = $file;
    }

    /**
     * Adds a routing resource at the top of the existing ones.
     *
     * @return bool Whether the operation succeeded
     *
     * @throws \RuntimeException If bundle is already imported
     */
    public function addResource(string $bundle, string $format, string $prefix = '/', string $path = 'routing'): bool
    {
        $current = '';
        $code = sprintf("%s:\n", $this->getImportedResourceYamlKey($bundle, $prefix));

        if (file_exists($this->file)) {
            $current = file_get_contents($this->file);

            // Don't add same bundle twice
            if (str_contains($current, '@'.$bundle)) {
                throw new \RuntimeException(sprintf('Bundle "%s" is already imported.', $bundle));
            }
        } elseif (!is_dir($dir = dirname($this->file))) {
            Generator::mkdir($dir);
        }

        if ('annotation' == $format) {
            $code .= sprintf("    resource: \"@%s/Controller/\"\n    type:     annotation\n", $bundle);
        } else {
            $code .= sprintf("    resource: \"@%s/Resources/config/%s.%s\"\n", $bundle, $path, $format);
        }
        $code .= sprintf("    prefix:   %s\n", $prefix);
        $code .= "\n";
        $code .= $current;

        if (false === Generator::dump($this->file, $code)) {
            return false;
        }

        return true;
    }

    public function getImportedResourceYamlKey($bundle, $prefix): string
    {
        $snakeCasedBundleName = Container::underscore(substr($bundle, 0, -6));
        $routePrefix = self::getRouteNamePrefix($prefix);

        return sprintf('%s%s%s', $snakeCasedBundleName, '' !== $routePrefix ? '_' : '', $routePrefix);
    }

    private static function getRouteNamePrefix($prefix): string
    {
        $prefix = preg_replace('/{(.*?)}/', '', $prefix); // {foo}_bar -> _bar
        $prefix = str_replace('/', '_', $prefix);
        $prefix = preg_replace('/_+/', '_', $prefix);     // foo__bar -> foo_bar
        $prefix = trim($prefix, '_');

        return $prefix;
    }
}
