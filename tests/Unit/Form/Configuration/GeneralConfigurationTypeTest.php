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
    private GeneralConfigurationType $generalConfigurationType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generalConfigurationType = new GeneralConfigurationType();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->generalConfigurationType);
    }

    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->expects(self::exactly(1))
           ->method('add')
           ->withConsecutive(
               ['name'],
               ['value'],
               ['label'],
               ['description'],
               ['type']
           );

        $optionsResolver = $this->createMock(OptionsResolver::class);

        $form = new GeneralConfigurationType();
        $form->buildForm($builder, (array) $optionsResolver);
    }

    public function testConfigureOptions(): void
    {
        $generalConfigurationType = $this->generalConfigurationType;
        $resolver = $this->createMock(OptionsResolver::class);

        $resolver->expects(self::once())
            ->method('setDefaults')
            ->with([
                    'data_class' => Configuration::class,
                ]
            );

        $generalConfigurationType->configureOptions($resolver);
    }
}
