<?php

declare(strict_types=1);

namespace WebWMS\Helper\FormHelper;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use WebWMS\Entity\Article;
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
        private FormFactoryInterface $formFactory
    ) {
    }

    /**
     * @param class-string<FormTypeInterface<mixed>> $type
     * @param mixed|null $data
     * @param array<string> $options
     * @return FormInterface
     */
    public function createForm(string $type, mixed $data = null, array $options = []): FormInterface
    {
        return $this->formFactory->create($type, $data, $options);
    }

    public function addArticleForm(): FormInterface
    {
        return $this->createForm(AddArticleType::class);
    }

    /**
     * @param Article|null $article
     * @return FormInterface
     */
    public function editArticleForm(?Article $article): FormInterface
    {
        return $this->createForm(EditArticleType::class, $article);
    }

    /**
     * @param Article|null $article
     * @return FormInterface
     */
    public function deleteArticleForm(?Article $article): FormInterface
    {
        return $this->createForm(DeleteArticleType::class, $article);
    }
}
