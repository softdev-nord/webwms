<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use WebWMS\Repository\TemplateRepository;

/**
 * @ORM\Entity(repositoryClass=TemplateRepository::class)
 * @ORM\Table(name="templates")
 */
class Template
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="text")
     */
    private ?string $text = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $params = null;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private bool $isDefault;

    /**
     * @ORM\ManyToOne(targetEntity="\WebWMS\Entity\TemplateType", inversedBy="templates")
     * @ORM\JoinColumn(name="id", referencedColumnName="id")
     */
    private ?TemplateType $templateType = null;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->correspondences = new ArrayCollection();
        $this->isDefault = false;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId($id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Template
    {
        $this->name = $name;

        return $this;
    }

    public function setText(?string $text): Template
    {
        $this->text = $text;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setTemplateType(?TemplateType $templateType): Template
    {
        $this->templateType = $templateType;

        return $this;
    }

    /**
     * Get templateType.
     */
    public function getTemplateType(): ?TemplateType
    {
        return $this->templateType;
    }

    public function setParams(?string $params): ?Template
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Get params.
     */
    public function getParams(): ?string
    {
        return $this->params;
    }

    /**
     * Set isDefault.
     */
    public function setIsDefault(bool $isDefault): Template
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    /**
     * Get isDefault.
     */
    public function getIsDefault(): bool
    {
        return $this->isDefault;
    }
}
