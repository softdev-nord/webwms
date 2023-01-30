<?php

declare(strict_types=1);

namespace WebWMS\Bundles\BundleGeneratorBundle\Entity;

use Symfony\Component\DependencyInjection\Container;

/**
 * @package:    WebWMS\Bundles\BundleGeneratorBundle\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        BaseBundle
 */
class BaseBundle
{
    private string $testsDirectory;

    public function __construct(
        private string $namespace,
        private string $name,
        private string $targetDirectory,
        private string $configurationFormat,
        private bool $isShared
    ) {
        $this->testsDirectory = $this->getTargetDirectory().'/Tests';
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getConfigurationFormat(): string
    {
        return $this->configurationFormat;
    }

    public function isShared(): bool
    {
        return $this->isShared;
    }

    /**
     * Returns the directory where the bundle will be generated.
     */
    public function getTargetDirectory(): string
    {
        return rtrim($this->targetDirectory, '/').'/'.trim(strtr($this->namespace, '\\', '/'), '/');
    }

    /**
     * Returns the name of the bundle without the Bundle suffix.
     */
    public function getBasename(): string
    {
        return substr($this->name, 0, -6);
    }

    /**
     * Returns the dependency injection extension alias for this bundle.
     */
    public function getExtensionAlias(): string
    {
        return Container::underscore($this->getBasename());
    }

    /**
     * Should a DependencyInjection directory be generated for this bundle?
     */
    public function shouldGenerateDependencyInjectionDirectory(): bool
    {
        return $this->isShared;
    }

    /**
     * What is the filename for the services.yaml/xml file?
     */
    public function getServicesConfigurationFilename(): string
    {
        if ('yaml' === $this->getConfigurationFormat() || 'annotation' === $this->configurationFormat) {
            return 'services.yaml';
        } else {
            return 'services.'.$this->getConfigurationFormat();
        }
    }

    /**
     * What is the filename for the routing.yaml/xml file?
     *
     * If false, no routing file will be generated
     */
    public function getRoutingConfigurationFilename(): string|bool
    {
        if ('annotation' == $this->getConfigurationFormat()) {
            return false;
        }

        return 'routing.'.$this->getConfigurationFormat();
    }

    /**
     * Returns the class name of the Bundle class.
     */
    public function getBundleClassName(): string
    {
        return $this->namespace.'\\'.$this->name;
    }

    public function setTestsDirectory($testsDirectory)
    {
        $this->testsDirectory = $testsDirectory;
    }

    public function getTestsDirectory(): string
    {
        return $this->testsDirectory;
    }
}
