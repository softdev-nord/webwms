<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\CustomerOrder;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\CustomerOrderPos;
use WebWMS\Form\CustomerOrder\CustomerOrderPosType;

/**
 * @package:    WebWMS\Tests\Unit\Form\CustomerOrder
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerOrderPosTypeTest
 *
 * @covers \WebWMS\Form\CustomerOrder\CustomerOrderPosType
 */
final class CustomerOrderPosTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::exactly(1))
            ->method('add')
            ->withConsecutive(
                ['id', HiddenType::class, self::anything()],
                ['customerOrderId', HiddenType::class, self::anything()],
                ['quantity', TextType::class, self::anything()],
                ['articleId', HiddenType::class, self::anything()],
                ['articleNr', TextType::class, self::anything()],
                ['articleName', TextType::class, self::anything()],
            );

        $type = new CustomerOrderPosType();
        $type->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => CustomerOrderPos::class]);

        $type = new CustomerOrderPosType();
        $type->configureOptions($resolver);
    }
}
