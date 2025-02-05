<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\TransportRequest;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\TransportRequest;
use WebWMS\Form\TransportRequest\AddTransportRequestType;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Form\TransportRequest',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'AddTransportRequestTypeTest'
)]
#[CoversClass(AddTransportRequestType::class)]
final class AddTransportRequestTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::exactly(1))
            ->method('add')
            ->willReturnOnConsecutiveCalls(
                ['id', HiddenType::class, self::anything()],
                ['suId', HiddenType::class, self::anything()],
                ['trNr', HiddenType::class, self::anything()],
                ['trPos', HiddenType::class, self::anything()],
                ['trPrio', HiddenType::class, self::anything()],
                ['articleNr', HiddenType::class, self::anything()],
                ['trQuantity', HiddenType::class, self::anything()],
                ['stockCoordinate', HiddenType::class, self::anything()],
                ['stockNr', HiddenType::class, self::anything()],
                ['stockLevel1', HiddenType::class, self::anything()],
                ['stockLevel2', HiddenType::class, self::anything()],
                ['stockLevel3', HiddenType::class, self::anything()],
                ['stockLevel4', HiddenType::class, self::anything()],
                ['trAccess', HiddenType::class, self::anything()],
                ['trDispatch', HiddenType::class, self::anything()],
                ['trState', HiddenType::class, self::anything()],
                ['orderUsername', HiddenType::class, self::anything()],
                ['bookingMethod', HiddenType::class, self::anything()],
                ['docId', HiddenType::class, self::anything()],
                ['orderNr', HiddenType::class, self::anything()],
                ['orderPos', HiddenType::class, self::anything()],
                ['charge', HiddenType::class, self::anything()],
                ['loadingEquipment', HiddenType::class, self::anything()],
                ['confirmationState', HiddenType::class, self::anything()],
                ['trUsername', HiddenType::class, self::anything()],
                ['trComputerIp', HiddenType::class, self::anything()],
                ['trBlocked', HiddenType::class, self::anything()],
                ['trStartDate', HiddenType::class, self::anything()],
                ['trEdited', HiddenType::class, self::anything()],
                ['trType', HiddenType::class, self::anything()],
                ['createdAt', HiddenType::class, self::anything()],
                ['updatedAt', HiddenType::class, self::anything()]
            );

        $addTransportRequestType = new AddTransportRequestType();
        $addTransportRequestType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects($this->once())
            ->method('setDefaults')
            ->with(['data_class' => TransportRequest::class]);

        $addTransportRequestType = new AddTransportRequestType();
        $addTransportRequestType->configureOptions($resolver);
    }
}
