<?php

namespace TechFix\Core\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

use TechFix\Core\Domain\Enum\Role;

#[ORM\Entity]
#[ORM\Table(name: 'profiles')]
class Profile
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int|null $id = null;

    #[ORM\Column(type: 'string', length: 20, unique: true, enumType: Role::class)]
    private Role $role;

    public function __construct(Role $role)
    {
        $this->role = $role;
    }

    public function getId(): ?int { return $this->id; }
    public function getRole(): string { return $this->role->value; }
}