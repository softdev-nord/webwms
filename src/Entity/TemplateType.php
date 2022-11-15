<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'template_type')]
class TemplateType
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'name', type: 'string', length: 50, nullable: false)]
    private string $name;

    #[ORM\Column(name: 'icon', type: 'string', length: 50, nullable: false)]
    private string $icon;

    #[ORM\Column(name: 'service', type: 'string', length: 150, nullable: false)]
    private string $service;

    #[ORM\Column(name: 'editor_template', type: 'string', length: 50, nullable: true)]
    private ?string $editorTemplate;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt;

    /** One Template Type has many Templates. This is the inverse side. */
    #[ORM\OneToMany(mappedBy: 'templateType', targetEntity: Template::class)]
    private Collection $templates;

    public function __construct()
    {
        $this->templates = new ArrayCollection();
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

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getService(): string
    {
        return $this->service;
    }

    public function setService(string $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getEditorTemplate(): ?string
    {
        return $this->editorTemplate;
    }

    public function setEditorTemplate(?string $editorTemplate): self
    {
        $this->editorTemplate = $editorTemplate;

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

    public function getTemplates(): Collection
    {
        return $this->templates;
    }

    public function setTemplate(Template $template): self
    {
        $this->templates[] = $template;

        return $this;
    }

    public function removeTemplate(Template $template): void
    {
        $this->templates->removeElement($template);
    }
}
