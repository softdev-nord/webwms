<?php

declare(strict_types=1);

namespace WebWMS\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        FileUploader
 */
class FileUploader
{
    public function __construct(
        private readonly string $targetDirectory,
        private readonly string $publicDirectory,
        private readonly ValidatorInterface $validator
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

        $errors = $this->validator->validate($uploadedFile, $image);

        return $errors->count() === 0;
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
