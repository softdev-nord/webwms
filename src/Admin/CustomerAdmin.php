<?php

namespace WebWMS\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

final class CustomerAdmin extends AbstractAdmin
{
    protected $classnameLabel = 'Kundenübersicht';

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('customer_nr', IntegerType::class, [
            ])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('customer_nr')
            ->add('customer_name')
            ->add('customer_address_addition')
            ->add('customer_address_street')
            ->add('customer_address_street_nr')
            ->add('customer_country_code')
            ->add('customer_zip_code')
            ->add('customer_city')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('customer_nr')
            ->addIdentifier('customer_name')
            ->addIdentifier('customer_address_addition')
            ->addIdentifier('customer_address_street')
            ->addIdentifier('customer_address_street_nr')
            ->addIdentifier('customer_country_code')
            ->addIdentifier('customer_zip_code')
            ->addIdentifier('customer_city')
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('Customer');
    }
}