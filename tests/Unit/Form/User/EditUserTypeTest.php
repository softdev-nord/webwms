<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\User;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\User;
use WebWMS\Form\User\EditUserType;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Form\User',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'EditUserTypeTest'
)]
#[CoversClass(EditUserType::class)]
final class EditUserTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::exactly(1))
            ->method('add')
            ->willReturnOnConsecutiveCalls(
                ['username', TextType::class, self::anything()],
                ['firstname', TextType::class, self::anything()],
                ['lastname', TextType::class, self::anything()],
                ['role', ChoiceType::class, self::anything()],
                ['password', RepeatedType::class, self::anything()],
                ['save', ButtonType::class, self::anything()],
                ['abort', ButtonType::class, self::anything()]
            );

        $editUserType = new EditUserType();
        $editUserType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolverMock = $this->createMock(OptionsResolver::class);
        $resolverMock
            ->expects($this->once())
            ->method('setDefaults')
            ->with(['data_class' => User::class]);

        $editUserType = new EditUserType();
        $editUserType->configureOptions($resolverMock);
    }
}
