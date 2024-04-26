<?php

declare(strict_types=1);

namespace WebWMS\Helper\FormHelper;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use WebWMS\Entity\TransportRequestEntity;
use WebWMS\Form\TransportRequest\AddTransportRequestType;
use WebWMS\Form\TransportRequest\DeleteTransportRequestType;
use WebWMS\Form\TransportRequest\EditTransportRequestType;

/**
 * @package:    WebWMS\Helper\FormHelper
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        TransportRequestFormHelper
 */
class TransportRequestFormHelper
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

    public function addTransportRequestForm(): FormInterface
    {
        return $this->createForm(AddTransportRequestType::class);
    }

    public function editTransportRequestForm(?TransportRequestEntity $transportRequestEntity): FormInterface
    {
        return $this->createForm(EditTransportRequestType::class, $transportRequestEntity);
    }

    public function deleteTransportRequestForm(?TransportRequestEntity $transportRequestEntity): FormInterface
    {
        return $this->createForm(DeleteTransportRequestType::class, $transportRequestEntity);
    }
}
