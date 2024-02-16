<?php

declare(strict_types=1);

namespace WebWMS\Form\Stock;

use Override;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @package:    WebWMS\Form\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockInFinalCollectionType
 */
class StockInFinalCollectionType extends CollectionType
{
    /**
     * @SuppressWarnings("unused")
     */
    #[Override]
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder->add('freeStockLocations', CollectionType::class, [
            'entry_type' => StockInFinalType::class,
        ]);
    }

    #[Override]
    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'data_class' => StockInFinalType::class,
            'allow_add' => true,
            'allow_delete' => true,
        ]);
    }
}
