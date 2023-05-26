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

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->configuration->setId($id);
        $this->assertEquals($id, $this->configuration->getId());

        // Test setName() and getName()
        $name = 'test_name';
        $this->configuration->setName($name);
        $this->assertEquals($name, $this->configuration->getName());

        // Test setValue() and getValue()
        $value = 'test_value';
        $this->configuration->setValue($value);
        $this->assertEquals($value, $this->configuration->getValue());

        // Test setLabel() and getLabel()
        $label = 'test_label';
        $this->configuration->setLabel($label);
        $this->assertEquals($label, $this->configuration->getLabel());

        // Test setDescription() and getDescription()
        $description = 'test_description';
        $this->configuration->setDescription($description);
        $this->assertEquals($description, $this->configuration->getDescription());

        // Test setType() and getType()
        $type = 'test_type';
        $this->configuration->setType($type);
        $this->assertEquals($type, $this->configuration->getType());
    }
}
