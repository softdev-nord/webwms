<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Configuration;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\ConfigurationEntity;
use WebWMS\Form\Configuration\GeneralConfigurationType;

/**
 * @package:    WebWMS\Tests\Unit\Form\ConfigurationController
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        GeneralConfigurationTypeTest
 */
#[CoversClass(GeneralConfigurationType::class)]
final class GeneralConfigurationTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::once())
            ->method('add')
            ->withAnyParameters();

        $generalConfigurationType = new GeneralConfigurationType();
        $generalConfigurationType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolverMock = $this->createMock(OptionsResolver::class);
        $resolverMock
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => ConfigurationEntity::class]);

        $generalConfigurationType = new GeneralConfigurationType();
        $generalConfigurationType->configureOptions($resolverMock);
    }
}
