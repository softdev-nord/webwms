<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\ConfigurationEntity;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        ConfigurationTest
 */
#[CoversClass(ConfigurationEntity::class)]
final class ConfigurationTest extends TestCase
{
    private ConfigurationEntity $configurationEntity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configurationEntity = new ConfigurationEntity();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->configurationEntity);
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->configurationEntity->setId($id);
        self::assertEquals($id, $this->configurationEntity->getId());

        // Test setName() and getName()
        $name = 'test_name';
        $this->configurationEntity->setName($name);
        self::assertEquals($name, $this->configurationEntity->getName());

        // Test setValue() and getValue()
        $value = 'test_value';
        $this->configurationEntity->setValue($value);
        self::assertEquals($value, $this->configurationEntity->getValue());

        // Test setLabel() and getLabel()
        $label = 'test_label';
        $this->configurationEntity->setLabel($label);
        self::assertEquals($label, $this->configurationEntity->getLabel());

        // Test setDescription() and getDescription()
        $description = 'test_description';
        $this->configurationEntity->setDescription($description);
        self::assertEquals($description, $this->configurationEntity->getDescription());

        // Test setType() and getType()
        $type = 'test_type';
        $this->configurationEntity->setType($type);
        self::assertEquals($type, $this->configurationEntity->getType());
    }
}
