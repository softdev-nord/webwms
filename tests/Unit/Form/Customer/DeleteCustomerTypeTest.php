<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Customer;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\Customer;
use WebWMS\Form\Customer\DeleteCustomerType;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Form\Customer',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'DeleteCustomerTypeTest'
)]
#[CoversClass(DeleteCustomerType::class)]
final class DeleteCustomerTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::exactly(1))
            ->method('add')
            ->willReturnOnConsecutiveCalls(
                ['customerId', HiddenType::class, self::anything()],
                ['save', ButtonType::class, self::anything()],
                ['abort', ButtonType::class, self::anything()]
            );

        $deleteCustomerType = new DeleteCustomerType();
        $deleteCustomerType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects($this->once())
            ->method('setDefaults')
            ->with(['data_class' => Customer::class]);

        $deleteCustomerType = new DeleteCustomerType();
        $deleteCustomerType->configureOptions($resolver);
    }
}
