<?php

declare(strict_types=1);

namespace WebWMS\Helper\FormHelper;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use WebWMS\Entity\CustomerOrderEntity;
use WebWMS\Entity\CustomerOrderPosEntity;
use WebWMS\Form\CustomerOrder\AddCustomerOrderType;
use WebWMS\Form\CustomerOrder\CustomerOrderPosType;
use WebWMS\Form\CustomerOrder\DeleteCustomerOrderType;
use WebWMS\Form\CustomerOrder\EditCustomerOrderType;

/**
 * @package:    WebWMS\Helper\FormHelper
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerOrderFormHelper
 */
class CustomerOrderFormHelper
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory
    ) {
    }

    /**
     * @param class-string<FormTypeInterface<mixed>> $type
     * @param mixed|null $data
     * @param array<string> $options
     */
    public function createForm(string $type, mixed $data = null, array $options = []): FormInterface
    {
        return $this->formFactory->create($type, $data, $options);
    }

    public function addCustomerOrderForm(): FormInterface
    {
        return $this->createForm(AddCustomerOrderType::class);
    }

    /**
     * @param CustomerOrderEntity|null $customerOrderEntity
     */
    public function editCustomerOrderForm(?CustomerOrderEntity $customerOrderEntity): FormInterface
    {
        return $this->createForm(EditCustomerOrderType::class, $customerOrderEntity);
    }

    /**
     * @param CustomerOrderEntity|null $customerOrderEntity
     */
    public function deleteCustomerOrderForm(?CustomerOrderEntity $customerOrderEntity): FormInterface
    {
        return $this->createForm(DeleteCustomerOrderType::class, $customerOrderEntity);
    }

    public function addCustomerOrderPosForm(): FormInterface
    {
        return $this->createForm(CustomerOrderPosType::class);
    }

    /**
     * @param CustomerOrderPosEntity|null $customerOrderPosEntity
     */
    public function editCustomerOrderPosForm(?CustomerOrderPosEntity $customerOrderPosEntity): FormInterface
    {
        return $this->createForm(CustomerOrderPosType::class, $customerOrderPosEntity);
    }

    /**
     * @param CustomerOrderPosEntity|null $customerOrderPosEntity
     */
    public function deleteCustomerOrderPosForm(?CustomerOrderPosEntity $customerOrderPosEntity): FormInterface
    {
        return $this->createForm(CustomerOrderPosType::class, $customerOrderPosEntity);
    }
}
