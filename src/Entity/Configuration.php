<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'configuration')]
#[ORM\Entity(repositoryClass: 'WebWMS\Repository\ConfigurationRepository')]
class Configuration
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'name', type: 'string', length: 255, nullable: false)]
    private string $name;

    #[ORM\Column(name: 'value', type: 'text', nullable: false)]
    private string $value;

    #[ORM\Column(name: 'label', type: 'string', length: 255, nullable: false)]
    private string $label;

    #[ORM\Column(name: 'description', type: 'string', length: 255, nullable: true)]
    private string $description;

    #[ORM\Column(name: 'type', type: 'string', length: 255, nullable: false)]
    private string $type;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
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

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
