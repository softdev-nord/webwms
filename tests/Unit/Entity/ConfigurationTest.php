<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\Configuration;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Entity',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'ConfigurationTest'
)]
#[CoversClass(Configuration::class)]
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

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->configuration->setId($id);
        self::assertSame($id, $this->configuration->getId());

        // Test setName() and getName()
        $name = 'test_name';
        $this->configuration->setName($name);
        self::assertSame($name, $this->configuration->getName());

        // Test setValue() and getValue()
        $value = 'test_value';
        $this->configuration->setValue($value);
        self::assertSame($value, $this->configuration->getValue());

        // Test setLabel() and getLabel()
        $label = 'test_label';
        $this->configuration->setLabel($label);
        self::assertSame($label, $this->configuration->getLabel());

        // Test setDescription() and getDescription()
        $description = 'test_description';
        $this->configuration->setDescription($description);
        self::assertSame($description, $this->configuration->getDescription());

        // Test setType() and getType()
        $type = 'test_type';
        $this->configuration->setType($type);
        self::assertSame($type, $this->configuration->getType());
    }
}
