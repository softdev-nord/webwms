<?php

declare(strict_types=1);

namespace WebWMS\Components\Module;

use Composer\Package\CompletePackageInterface;

/**
 * @package:    WebWMS\Components\Module
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ModuleFromFileSystem
 */
class ModuleFromFileSystem
{
    protected string $baseClass;

    protected string $path;

    protected bool $managedByComposer;

    protected CompletePackageInterface $composerPackage;

    public function getBaseClass(): string
    {
        return $this->baseClass;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getManagedByComposer(): bool
    {
        return $this->managedByComposer;
    }

    public function getComposerPackage(): CompletePackageInterface
    {
        return $this->composerPackage;
    }

    public function getName(): string
    {
        $baseClass = $this->baseClass;

        $pos = mb_strrpos($baseClass, '\\');

        return $pos === false ? $this->baseClass : mb_substr($this->baseClass, $pos + 1);
    }

    /**
     * @param array<mixed> $options
     *
     * @return $this
     */
    public function assign(array $options)
    {
        foreach ($options as $key => $value) {
            if ($key === 'id' && method_exists($this, 'setId')) {
                $this->setId($value);

                continue;
            }

            try {
                $this->$key = $value; /* @phpstan-ignore-line */
            } catch (\Error|\Exception $error) {
                // nth
            }
        }

        return $this;
    }
}
