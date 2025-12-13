<?php

namespace TechFix\Core\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

use TechFix\Core\Domain\Enum\Priority;

#[ORM\Entity]
#[ORM\Table(name: 'tickets')]
class Ticket
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int|null $id = null;

    #[ORM\Column(type: 'string', length: 100)]
    private ?string $title = null;

    #[ORM\Column(type: 'string', length: 100)]
    private ?string $description = null;

    #[ORM\Column(type: 'integer')]
    private ?int $asset = null;

    #[ORM\Column(type: 'string', length: 20, enumType: Priority::class)]
    private ?Priority $priority = null;

    public function __construct(string $title, string $description, int $asset, Priority $priority)
    {
        $this->title = $title;
        $this->description = $description;
        $this->asset = $asset;
        $this->priority = $priority;
    }

    public function getId(): ?int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getDescription(): string { return $this->description; }
    public function getAsset(): int { return $this->asset; }
    public function getPriority(): Priority { return $this->priority; }
}