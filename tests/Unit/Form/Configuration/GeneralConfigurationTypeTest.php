<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Configuration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\Configuration;
use WebWMS\Form\Configuration\GeneralConfigurationType;

/**
 * @package:    WebWMS\Tests\Unit\Form\Configuration
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        GeneralConfigurationTypeTest
 *
 * @covers \WebWMS\Form\Configuration\GeneralConfigurationType
 */
final class GeneralConfigurationTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::exactly(1))
            ->method('add')
            ->withConsecutive(
                ['name'],
                ['value'],
                ['label'],
                ['description'],
                ['type']
            );

        $type = new GeneralConfigurationType();
        $type->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolverMock = $this->createMock(OptionsResolver::class);
        $resolverMock
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => Configuration::class]);

        $type = new GeneralConfigurationType();
        $type->configureOptions($resolverMock);
    }
}
