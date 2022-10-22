<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="template_types")
 */
class TemplateType
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private string $icon;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private string $service;

    /**
     * @ORM\OneToMany(targetEntity="WebWMS\Entity\Template", mappedBy="templateType")
     */
    private Collection $templates;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private ?string $editorTemplate;

    public function __construct()
    {
        $this->templates = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setIcon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function setService(string $service): static
    {
        $this->service = $service;

        return $this;
    }

    public function getService(): string
    {
        return $this->service;
    }

    public function addTemplate(Template $template): static
    {
        $this->templates[] = $template;

        return $this;
    }

    public function removeTemplate(Template $template): void
    {
        $this->templates->removeElement($template);
    }

    public function getTemplates(): ArrayCollection
    {
        return $this->templates;
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
}
