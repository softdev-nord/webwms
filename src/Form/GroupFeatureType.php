<?php

declare(strict_types=1);

namespace WebWMS\Form;

use WebWMS\Entity\Feature;
use WebWMS\Entity\Group;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupFeatureType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @return void
     * @throws \Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
          ->add('features', EntityType::class, [
            'class' => Feature::class,
            'multiple' => true,
            'expanded' => true,
            'choice_label' => 'name'
          ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
          'data_class' => Group::class,
          'attr' => ['class="row g-3"']
        ]);
    }
}
