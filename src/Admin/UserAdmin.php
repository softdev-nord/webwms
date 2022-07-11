<?php

namespace WebWMS\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

final class UserAdmin extends AbstractAdmin
{
    protected $classnameLabel = 'Benutzer';

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('customer_nr', IntegerType::class, [
                'placeholder' => 'No author selected',
            ])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('username')
            ->add('roles')
            ->add('firstname')
            ->add('lastname')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('username')
            ->addIdentifier('roles')
            ->addIdentifier('firstname')
            ->addIdentifier('lastname')
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('Customer');
    }
}
