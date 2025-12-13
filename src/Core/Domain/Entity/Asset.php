<?php

namespace TechFix\Core\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'assets')]
class Asset
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int|null $id = null;

    #[ORM\Column(type: 'string', length: 50, unique: true)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', unique: true)]
    private ?string $patrimony_code = null;

    #[ORM\Column(type: 'string', unique: true)]
    private ?string $serial_number = null;

    public function __construct(string $name, string $patrimony_code, string $serial_number)
    {
        $this->name = $name;
        $this->patrimony_code = $patrimony_code;
        $this->serial_number = $serial_number;
    }

    public function getName(): string { return $this->name; }
    public function getPatrimonyCode(): string { return $this->patrimony_code; }
    public function getSerialNumber(): string { return $this->serial_number; }  
    public function getId(): ?int { return $this->id; }
}


/**
*ASSETS {
*   int id PK
*   string name "Ex: Notebook Dell"
*   string patrimony_code UK "Ex: PAT-001"
*   string serial_number
 */