<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Configuration;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\Configuration;
use WebWMS\Form\Configuration\GeneralConfigurationType;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Form\Configuration',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'GeneralConfigurationTypeTest'
)]
#[CoversClass(GeneralConfigurationType::class)]
final class GeneralConfigurationTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::exactly(1))
            ->method('add')
            ->willReturnOnConsecutiveCalls(
                ['name'],
                ['value'],
                ['label'],
                ['description'],
                ['type']
            );

        $generalConfigurationType = new GeneralConfigurationType();
        $generalConfigurationType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolverMock = $this->createMock(OptionsResolver::class);
        $resolverMock
            ->expects($this->once())
            ->method('setDefaults')
            ->with(['data_class' => Configuration::class]);

        $generalConfigurationType = new GeneralConfigurationType();
        $generalConfigurationType->configureOptions($resolverMock);
    }
}
