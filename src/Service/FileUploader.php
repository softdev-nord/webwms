<?php

declare(strict_types=1);

namespace WebWMS\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Service',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'FileUploader'
)]
readonly class FileUploader
{
    public function __construct(
        private string $targetDirectory,
        private string $publicDirectory,
        private ValidatorInterface $validator,
    ) {
    }

    public function upload(UploadedFile $uploadedFile): string
    {
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);

        return $originalFilename . '.' . $uploadedFile->guessExtension();
    }

    public function isValidImage(UploadedFile $uploadedFile): bool
    {
        $image = new Assert\Image([
            'maxSize' => '5m',
        ]);

        $constraintViolationList = $this->validator->validate($uploadedFile, $image);

        return $constraintViolationList->count() === 0;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }

    public function getPublicDirectory(): string
    {
        return $this->publicDirectory;
    }
}
