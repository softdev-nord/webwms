<?php

declare(strict_types=1);

namespace WebWMS\Tests\Architecture;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

final class ModularArchitectureTest extends TestCase
{
    private const MODULES = [
        'Administration',
        'Documentation',
        'Fulfillment',
        'Inbound',
        'Integration',
        'Inventory',
        'Outbound',
        'Platform',
        'Shared',
        'Warehouse',
    ];

    private const LEGACY_NAMESPACES = [
        'WebWMS\\Controller\\',
        'WebWMS\\Dto\\',
        'WebWMS\\Entity\\',
        'WebWMS\\Form\\',
        'WebWMS\\Repository\\',
        'WebWMS\\Service\\',
    ];

    public function testNamespacesMatchTheirPaths(): void
    {
        $violations = [];

        foreach ($this->sourceFiles() as $file) {
            $namespace = $this->namespace($this->source($file));
            $relativePath = substr($file, strlen($this->sourceDirectory()) + 1, -4);
            $segments = explode(DIRECTORY_SEPARATOR, $relativePath);
            array_pop($segments);
            $expectedNamespace = 'WebWMS\\' . implode('\\', $segments);

            if ($namespace !== $expectedNamespace) {
                $violations[] = sprintf('%s declares "%s" instead of "%s".', $relativePath, $namespace, $expectedNamespace);
            }
        }

        self::assertSame([], $violations, implode("\n", $violations));
    }

    public function testLayerDependenciesPointInward(): void
    {
        $violations = [];

        foreach ($this->sourceFiles() as $file) {
            $source = $this->source($file);
            $layer = $this->layer($file);

            foreach ($this->imports($source) as $import) {
                if ($layer === 'Domain' && $this->isForbiddenDomainDependency($import)) {
                    $violations[] = $this->dependencyViolation($file, $layer, $import);
                }

                if ($layer === 'Application' && $this->containsLayer($import, ['Infrastructure', 'Presentation'])) {
                    $violations[] = $this->dependencyViolation($file, $layer, $import);
                }

                if ($layer === 'Infrastructure' && $this->containsLayer($import, ['Presentation'])) {
                    $violations[] = $this->dependencyViolation($file, $layer, $import);
                }

                if ($layer === 'Presentation' && $this->containsLayer($import, ['Infrastructure'])) {
                    $violations[] = $this->dependencyViolation($file, $layer, $import);
                }
            }
        }

        self::assertSame([], $violations, implode("\n", $violations));
    }

    public function testModulesDoNotDependOnAnotherModulesInfrastructure(): void
    {
        $violations = [];

        foreach ($this->sourceFiles() as $file) {
            $sourceModule = $this->module($file);

            foreach ($this->imports($this->source($file)) as $import) {
                if (preg_match('/^WebWMS\\\\([^\\\\]+)\\\\(?:.*\\\\)?Infrastructure\\\\/', $import, $matches) !== 1) {
                    continue;
                }

                if ($matches[1] !== $sourceModule) {
                    $violations[] = sprintf('%s depends on the infrastructure of %s through %s.', $this->relativePath($file), $matches[1], $import);
                }
            }
        }

        self::assertSame([], $violations, implode("\n", $violations));
    }

    public function testMigratedModulesDoNotDependOnLegacyNamespaces(): void
    {
        $violations = [];

        foreach ($this->sourceFiles() as $file) {
            foreach ($this->imports($this->source($file)) as $import) {
                foreach (self::LEGACY_NAMESPACES as $legacyNamespace) {
                    if (str_starts_with($import, $legacyNamespace)) {
                        $violations[] = sprintf('%s depends on legacy namespace %s.', $this->relativePath($file), $import);
                    }
                }
            }
        }

        self::assertSame([], $violations, implode("\n", $violations));
    }

    /** @return iterable<string> */
    private function sourceFiles(): iterable
    {
        foreach (self::MODULES as $module) {
            $directory = $this->sourceDirectory() . DIRECTORY_SEPARATOR . $module;

            if (!is_dir($directory)) {
                continue;
            }

            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

            /** @var SplFileInfo $file */
            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    yield $file->getPathname();
                }
            }
        }
    }

    private function sourceDirectory(): string
    {
        return dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'src';
    }

    private function relativePath(string $file): string
    {
        return substr($file, strlen(dirname(__DIR__, 2)) + 1);
    }

    private function source(string $file): string
    {
        $source = file_get_contents($file);

        self::assertIsString($source, sprintf('Unable to read %s.', $this->relativePath($file)));

        return $source;
    }

    private function namespace(string $source): string
    {
        preg_match('/^namespace\s+([^;]+);/m', $source, $matches);

        return $matches[1] ?? '';
    }

    /** @return list<string> */
    private function imports(string $source): array
    {
        preg_match_all('/^use\s+([^;]+);/m', $source, $matches);

        return $matches[1];
    }

    private function module(string $file): string
    {
        $relativePath = substr($file, strlen($this->sourceDirectory()) + 1);

        return explode(DIRECTORY_SEPARATOR, $relativePath)[0];
    }

    private function layer(string $file): ?string
    {
        $segments = explode(DIRECTORY_SEPARATOR, $file);

        foreach (['Domain', 'Application', 'Infrastructure', 'Presentation'] as $layer) {
            if (in_array($layer, $segments, true)) {
                return $layer;
            }
        }

        return null;
    }

    private function isForbiddenDomainDependency(string $import): bool
    {
        foreach (['Doctrine\\', 'Psr\\', 'Symfony\\', 'Twig\\'] as $frameworkNamespace) {
            if (str_starts_with($import, $frameworkNamespace)) {
                return true;
            }
        }

        return $this->containsLayer($import, ['Application', 'Infrastructure', 'Presentation']);
    }

    /** @param list<string> $layers */
    private function containsLayer(string $import, array $layers): bool
    {
        foreach ($layers as $layer) {
            if (str_contains($import, '\\' . $layer . '\\')) {
                return true;
            }
        }

        return false;
    }

    private function dependencyViolation(string $file, ?string $layer, string $import): string
    {
        return sprintf('%s in layer %s must not depend on %s.', $this->relativePath($file), $layer ?? 'unclassified', $import);
    }
}
