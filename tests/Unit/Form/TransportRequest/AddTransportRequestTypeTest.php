<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\TransportRequest;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\TransportRequestEntity;
use WebWMS\Form\TransportRequest\AddTransportRequestType;

/**
 * @package:    WebWMS\Tests\Unit\Form\TransportRequestEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        AddTransportRequestTypeTest
 */
#[CoversClass(AddTransportRequestType::class)]
final class AddTransportRequestTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::once())
            ->method('add')
            ->withAnyParameters();

        $addTransportRequestType = new AddTransportRequestType();
        $addTransportRequestType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => TransportRequestEntity::class]);

        $addTransportRequestType = new AddTransportRequestType();
        $addTransportRequestType->configureOptions($resolver);
    }
}
