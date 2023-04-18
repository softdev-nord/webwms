<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use WebWMS\Entity\Configuration;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ConfigurationTest
 *
 * @covers \WebWMS\Entity\Configuration
 */
final class ConfigurationTest extends TestCase
{
    private Configuration $configuration;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->configuration = new Configuration();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->configuration);
    }

    public function testGetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(Configuration::class))
            ->getProperty('id');
        $property->setValue($this->configuration, $expected);
        $this->assertSame($expected, $this->configuration->getId());
    }

    public function testSetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(Configuration::class))
            ->getProperty('id');
        $this->configuration->setId($expected);
        $this->assertSame($expected, $property->getValue($this->configuration));
    }

    public function testGetName(): void
    {
        $expected = 'name';
        $property = (new \ReflectionClass(Configuration::class))
            ->getProperty('name');
        $property->setValue($this->configuration, $expected);
        $this->assertSame($expected, $this->configuration->getName());
    }

    public function testSetName(): void
    {
        $expected = 'name';
        $property = (new \ReflectionClass(Configuration::class))
            ->getProperty('name');
        $this->configuration->setName($expected);
        $this->assertSame($expected, $property->getValue($this->configuration));
    }

    public function testGetValue(): void
    {
        $expected = 'value';
        $property = (new \ReflectionClass(Configuration::class))
            ->getProperty('value');
        $property->setValue($this->configuration, $expected);
        $this->assertSame($expected, $this->configuration->getValue());
    }

    public function testSetValue(): void
    {
        $expected = 'value';
        $property = (new \ReflectionClass(Configuration::class))
            ->getProperty('value');
        $this->configuration->setValue($expected);
        $this->assertSame($expected, $property->getValue($this->configuration));
    }

    public function testGetLabel(): void
    {
        $expected = 'label';
        $property = (new \ReflectionClass(Configuration::class))
            ->getProperty('label');
        $property->setValue($this->configuration, $expected);
        $this->assertSame($expected, $this->configuration->getLabel());
    }

    public function testSetLabel(): void
    {
        $expected = 'label';
        $property = (new \ReflectionClass(Configuration::class))
            ->getProperty('label');
        $this->configuration->setLabel($expected);
        $this->assertSame($expected, $property->getValue($this->configuration));
    }

    public function testGetDescription(): void
    {
        $expected = 'description';
        $property = (new \ReflectionClass(Configuration::class))
            ->getProperty('description');
        $property->setValue($this->configuration, $expected);
        $this->assertSame($expected, $this->configuration->getDescription());
    }

    public function testSetDescription(): void
    {
        $expected = 'description';
        $property = (new \ReflectionClass(Configuration::class))
            ->getProperty('description');
        $this->configuration->setDescription($expected);
        $this->assertSame($expected, $property->getValue($this->configuration));
    }

    public function testGetType(): void
    {
        $expected = 'type';
        $property = (new \ReflectionClass(Configuration::class))
            ->getProperty('type');
        $property->setValue($this->configuration, $expected);
        $this->assertSame($expected, $this->configuration->getType());
    }

    public function testSetType(): void
    {
        $expected = 'type';
        $property = (new \ReflectionClass(Configuration::class))
            ->getProperty('type');
        $this->configuration->setType($expected);
        $this->assertSame($expected, $property->getValue($this->configuration));
    }
}
