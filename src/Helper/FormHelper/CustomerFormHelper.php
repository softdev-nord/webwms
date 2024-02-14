<?php

declare(strict_types=1);

namespace WebWMS\Helper\FormHelper;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use WebWMS\Entity\CustomerEntity;
use WebWMS\Form\Customer\AddCustomerType;
use WebWMS\Form\Customer\DeleteCustomerType;
use WebWMS\Form\Customer\EditCustomerType;

/**
 * @package:    WebWMS\Helper\FormHelper
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerFormHelper
 */
class CustomerFormHelper
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

    public function addCustomerForm(): FormInterface
    {
        return $this->createForm(AddCustomerType::class);
    }

    /**
     * @param CustomerEntity|null $customerEntity
     */
    public function editCustomerForm(?CustomerEntity $customerEntity): FormInterface
    {
        return $this->createForm(EditCustomerType::class, $customerEntity);
    }

    /**
     * @param CustomerEntity|null $customerEntity
     */
    public function deleteCustomerForm(?CustomerEntity $customerEntity): FormInterface
    {
        return $this->createForm(DeleteCustomerType::class, $customerEntity);
    }
}
