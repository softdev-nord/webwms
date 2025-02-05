<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\FileUploader;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Service',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'FileUploaderTest'
)]
#[CoversClass(FileUploader::class)]
final class FileUploaderTest extends TestCase
{
    private const TARGET_DIRECTORY = '/path/to/target/directory';

    private const PUBLIC_DIRECTORY = '/path/to/public/directory';

    private FileUploader $fileUploader;

    private MockObject $mockObject;

    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(ValidatorInterface::class);
        $this->fileUploader = new FileUploader(self::TARGET_DIRECTORY, self::PUBLIC_DIRECTORY, $this->mockObject);
    }

    public function testUpload(): void
    {
        $file = $this->createMock(UploadedFile::class);
        $originalFilename = 'example.jpg';
        $safeFilename = 'example';
        $fileName = $safeFilename . '.jpg';

        $file->expects($this->once())
            ->method('getClientOriginalName')
            ->willReturn($originalFilename);
        $file->expects($this->once())
            ->method('guessExtension')
            ->willReturn('jpg');

        $result = $this->fileUploader->upload($file);

        self::assertSame($fileName, $result);
    }

    public function testIsValidImageReturnsTrueForValidImage(): void
    {
        $file = $this->createMock(UploadedFile::class);
        $constraintViolationList = new ConstraintViolationList([]);

        $this->mockObject
            ->expects($this->once())
            ->method('validate')
            ->with($file)
            ->willReturn($constraintViolationList);

        $result = $this->fileUploader->isValidImage($file);

        self::assertTrue($result);
    }

    public function testIsValidImageReturnsFalseForInvalidImage(): void
    {
        $file = $this->createMock(UploadedFile::class);
        $constraintViolation = $this->createMock(ConstraintViolationInterface::class);
        $constraintViolationList = new ConstraintViolationList([$constraintViolation]);

        $this->mockObject
            ->expects($this->once())
            ->method('validate')
            ->with($file)
            ->willReturn($constraintViolationList);

        $result = $this->fileUploader->isValidImage($file);

        self::assertFalse($result);
    }

    public function testGetTargetDirectory(): void
    {
        $result = $this->fileUploader->getTargetDirectory();

        self::assertSame(self::TARGET_DIRECTORY, $result);
    }

    public function testGetPublicDirectory(): void
    {
        $result = $this->fileUploader->getPublicDirectory();

        self::assertSame(self::PUBLIC_DIRECTORY, $result);
    }
}
