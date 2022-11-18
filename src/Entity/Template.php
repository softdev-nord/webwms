<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'templates')]
#[ORM\Entity(repositoryClass: 'WebWMS\Repository\TemplateRepository')]
class Template
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'name', type: 'string', length: 100, nullable: false)]
    private string $name;

    #[ORM\Column(name: 'text', type: 'text', nullable: false)]
    private string $text;

    #[ORM\Column(name: 'params', type: 'string', length: 255, nullable: false)]
    private string $params;

    #[ORM\Column(name: 'is_default', type: 'boolean', nullable: false)]
    private bool $isDefault;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt;

    /** Many Templates have one TemplateType. This is the owning side. */
#    #[ORM\ManyToOne(targetEntity: TemplateType::class, inversedBy: 'templates')]
#    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id')]
    private ?TemplateType $templateType = null;

    public function __construct()
    {
        $this->isDefault = false;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Template
    {
        $this->name = $name;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): Template
    {
        $this->text = $text;

        return $this;
    }

    public function getParams(): string
    {
        return $this->params;
    }

    public function setParams(string $params): self
    {
        $this->params = $params;

        return $this;
    }

    public function getIsDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): self
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function setTemplateType(?TemplateType $templateType): self
    {
        $this->templateType = $templateType;

        return $this;
    }

    public function getTemplateType(): ?TemplateType
    {
        return $this->templateType;
    }
}
