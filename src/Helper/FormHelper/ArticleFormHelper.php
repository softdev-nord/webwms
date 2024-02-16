<?php

declare(strict_types=1);

namespace WebWMS\Helper\FormHelper;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use WebWMS\Entity\ArticleEntity;
use WebWMS\Form\Article\AddArticleType;
use WebWMS\Form\Article\DeleteArticleType;
use WebWMS\Form\Article\EditArticleType;

/**
 * @package:    WebWMS\Helper\FormHelper
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ArticleFormHelper
 */
class ArticleFormHelper
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory
    ) {
    }

    /**
     * @param class-string<FormTypeInterface<mixed>> $type
     * @param mixed|null $data
     * @param array<string> $options
     */
    public function createForm(string $type, mixed $data = null, array $options = []): FormInterface
    {
        return $this->formFactory->create($type, $data, $options);
    }

    public function addArticleForm(): FormInterface
    {
        return $this->createForm(AddArticleType::class);
    }

    public function editArticleForm(?ArticleEntity $articleEntity): FormInterface
    {
        return $this->createForm(EditArticleType::class, $articleEntity);
    }

    public function deleteArticleForm(?ArticleEntity $articleEntity): FormInterface
    {
        return $this->createForm(DeleteArticleType::class, $articleEntity);
    }
}
