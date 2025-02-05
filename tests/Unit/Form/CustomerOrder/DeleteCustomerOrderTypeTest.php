<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\CustomerOrder;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\CustomerOrder;
use WebWMS\Form\CustomerOrder\DeleteCustomerOrderType;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Form\CustomerOrder',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'DeleteCustomerOrderTypeTest'
)]
#[CoversClass(DeleteCustomerOrderType::class)]
final class DeleteCustomerOrderTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::exactly(1))
            ->method('add')
            ->willReturnOnConsecutiveCalls(
                ['customerOrderId', HiddenType::class, self::anything()],
                ['delete', ButtonType::class, self::anything()],
                ['abort', ButtonType::class, self::anything()]
            );

        $deleteCustomerOrderType = new DeleteCustomerOrderType();
        $deleteCustomerOrderType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects($this->once())
            ->method('setDefaults')
            ->with(['data_class' => CustomerOrder::class]);

        $deleteCustomerOrderType = new DeleteCustomerOrderType();
        $deleteCustomerOrderType->configureOptions($resolver);
    }
}
