<?php

declare(strict_types=1);

namespace WebWMS\Form\Configuration;

use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\ConfigurationEntity;

/**
 * @package:    WebWMS\Form\ConfigurationController
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        GeneralConfigurationType
 */
class GeneralConfigurationType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     */
    #[Override]
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('name')
            ->add('value')
            ->add('label')
            ->add('description')
            ->add('type')
        ;
    }

    #[Override]
    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'data_class' => ConfigurationEntity::class,
        ]);
    }
}
