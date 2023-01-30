<?php

declare(strict_types=1);

namespace WebWMS\Form\User;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// use WebWMS\Entity\Feature;
// use WebWMS\Entity\Group;

class GroupFeatureType extends AbstractType
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @throws \Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
          ->add('features', EntityType::class, [
//            'class' => Feature::class,
            'multiple' => true,
            'expanded' => true,
            'choice_label' => 'name',
          ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
//          'data_class' => Group::class,
          'attr' => ['class="row g-3"'],
        ]);
    }
}
