<?php

declare(strict_types=1);

namespace WebWMS\Helper\FormHelper;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use WebWMS\Form\Customer\AddCustomerType;
use WebWMS\Form\Customer\DeleteCustomerType;
use WebWMS\Form\Customer\EditCustomerType;

/**
 * @package:    WebWMS\Helper\FormHelper
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        CustomerFormHelper
 */
class CustomerFormHelper
{
    public function __construct(
        private FormFactoryInterface $formFactory
    ) {
    }

    public function createForm(string $type, mixed $data = null, array $options = []): FormInterface
    {
        return $this->formFactory->create($type, $data, $options);
    }

    public function addCustomerForm(): FormInterface
    {
        return $this->createForm(AddCustomerType::class);
    }

    public function editCustomerForm($customer): FormInterface
    {
        return $this->createForm(EditCustomerType::class, $customer);
    }

    public function deleteCustomerForm($customer): FormInterface
    {
        return $this->createForm(DeleteCustomerType::class, $customer);
    }
}
