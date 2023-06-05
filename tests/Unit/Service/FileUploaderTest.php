<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use WebWMS\Service\FileUploader;

/**
 * @package:    WebWMS\Tests\Unit\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        FileUploaderTest
 *
 * @covers \WebWMS\Service\FileUploader
 */
final class FileUploaderTest extends TestCase
{
    private const TARGET_DIRECTORY = '/path/to/target/directory';

    private const PUBLIC_DIRECTORY = '/path/to/public/directory';

    private FileUploader $fileUploader;

    /**
     * @var (ValidatorInterface&MockObject)|MockObject
     */
    private MockObject|ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->fileUploader = new FileUploader(self::TARGET_DIRECTORY, self::PUBLIC_DIRECTORY, $this->validator);
    }

    public function testUpload(): void
    {
        $file = $this->createMock(UploadedFile::class);
        $originalFilename = 'example.jpg';
        $safeFilename = 'example';
        $fileName = $safeFilename . '.jpg';

        $file->expects(self::once())
            ->method('getClientOriginalName')
            ->willReturn($originalFilename);
        $file->expects(self::once())
            ->method('guessExtension')
            ->willReturn('jpg');

        $result = $this->fileUploader->upload($file);

        self::assertSame($fileName, $result);
    }

    public function testIsValidImageReturnsTrueForValidImage(): void
    {
        $file = $this->createMock(UploadedFile::class);
        $constraintViolations = new ConstraintViolationList([]);

        $this->validator
            ->expects(self::once())
            ->method('validate')
            ->with($file)
            ->willReturn($constraintViolations);

        $result = $this->fileUploader->isValidImage($file);

        self::assertTrue($result);
    }

    public function testIsValidImageReturnsFalseForInvalidImage(): void
    {
        $file = $this->createMock(UploadedFile::class);
        $constraintViolation = $this->createMock(ConstraintViolationInterface::class);
        $constraintViolations = new ConstraintViolationList([$constraintViolation]);

        $this->validator
            ->expects(self::once())
            ->method('validate')
            ->with($file)
            ->willReturn($constraintViolations);

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
