<?php

declare(strict_types=1);

namespace WebWMS\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        FileUploader
 */
class FileUploader
{
    public function __construct(
        private string $targetDirectory,
        private string $publicDirectory,
        private ValidatorInterface $validator
    ) {
    }

    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            throw $e;
        }

        return $fileName;
    }

    public function isValidImage(UploadedFile $file): bool
    {
        $imageConstraint = new Assert\Image([
                'maxSize' => '5m',
            ]);

        $errors = $this->validator->validate($file, $imageConstraint);

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
