<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Customer;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\Customer;
use WebWMS\Form\Customer\DeleteCustomerType;

/**
 * @package:    WebWMS\Tests\Unit\Form\Customer
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        DeleteCustomerTypeTest
 *
 * @covers \WebWMS\Form\Customer\DeleteCustomerType
 */
final class DeleteCustomerTypeTest extends TestCase
{
    private DeleteCustomerType $deleteCustomerType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->deleteCustomerType = new DeleteCustomerType();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->deleteCustomerType);
    }

    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->expects($this->exactly(1))
            ->method('add')
            ->withConsecutive(
                ['customerId', HiddenType::class, [
                    'attr' => [
                        'id' => 'customerId',
                        'data-type' => 'customerId',
                    ],
                ]],
                ['save', SubmitType::class, [
                    'label' => 'Löschen',
                    'attr' => [
                        'class' => 'btn btn-lg',
                    ],
                ]],
                ['abort', ButtonType::class, [
                    'label' => 'Abbrechen',
                    'attr' => [
                        'class' => 'btn btn-lg abort',
                    ],
                ]],
            );

        $optionsResolver = $this->createMock(OptionsResolver::class);

        $form = $this->deleteCustomerType;
        $form->buildForm($builder, (array) $optionsResolver);
    }

    public function testConfigureOptions(): void
    {
        $deleteArticleType = $this->deleteCustomerType;
        $resolver = $this->createMock(OptionsResolver::class);

        $resolver->expects($this->once())
            ->method('setDefaults')
            ->with([
                    'data_class' => Customer::class,
                ]
            );

        $deleteArticleType->configureOptions($resolver);
    }
}
