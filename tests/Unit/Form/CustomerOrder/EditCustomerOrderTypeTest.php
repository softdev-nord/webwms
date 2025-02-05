<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\CustomerOrder;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\CustomerOrder;
use WebWMS\Form\CustomerOrder\EditCustomerOrderType;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Form\CustomerOrder',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'EditCustomerOrderTypeTest'
)]
#[CoversClass(EditCustomerOrderType::class)]
final class EditCustomerOrderTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::exactly(1))
            ->method('add')
            ->willReturnOnConsecutiveCalls(
                ['customerOrderId', HiddenType::class, self::anything()],
                ['customerOrderNr', TextType::class, self::anything()],
                ['usrId', HiddenType::class, self::anything()],
                ['customerId', HiddenType::class, self::anything()],
                ['customerOrderReference', TextType::class, self::anything()],
                ['customerOrderDate', TextType::class, self::anything()],
                ['customerOrderCreationDate', TextType::class, self::anything()],
                ['save', ButtonType::class, self::anything()],
                ['abort', ButtonType::class, self::anything()]
            );

        $editCustomerOrderType = new EditCustomerOrderType();
        $editCustomerOrderType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects($this->once())
            ->method('setDefaults')
            ->with(['data_class' => CustomerOrder::class]);

        $editCustomerOrderType = new EditCustomerOrderType();
        $editCustomerOrderType->configureOptions($resolver);
    }
}
