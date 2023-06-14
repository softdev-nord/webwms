<?php

declare(strict_types=1);

namespace WebWMS\Helper\FormHelper;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use WebWMS\Entity\Customer;
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
        private FormFactoryInterface $formFactory
    ) {
    }

    /**
     * @param class-string<FormTypeInterface<mixed>> $type
     * @param mixed|null $data
     * @param array<string> $options
     * @return FormInterface
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
     * @param Customer|null $customer
     * @return FormInterface
     */
    public function editCustomerForm(?Customer $customer): FormInterface
    {
        return $this->createForm(EditCustomerType::class, $customer);
    }

    /**
     * @param Customer|null $customer
     * @return FormInterface
     */
    public function deleteCustomerForm(?Customer $customer): FormInterface
    {
        return $this->createForm(DeleteCustomerType::class, $customer);
    }
}
