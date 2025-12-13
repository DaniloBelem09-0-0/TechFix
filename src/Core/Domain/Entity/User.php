<?php

namespace TechFix\Core\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int|null $id = null;

    #[ORM\Column(type: 'string', length: 150)]
    private string $name;

    #[ORM\Column(type: 'string', unique: true)]
    private string $email;

    #[ORM\Column(type: 'string')]
    private string $password;

    // --- CORREÇÃO AQUI ---
    // 1. O tipo da propriedade deve ser Profile (o Objeto), não string.
    #[ORM\ManyToOne(targetEntity: Profile::class)]
    #[ORM\JoinColumn(name: 'profile_id', referencedColumnName: 'id', nullable: false)]
    private Profile $profile;

    // 2. O construtor deve exigir um Objeto Profile
    public function __construct(string $name, string $email, string $password, Profile $profile)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = password_hash($password, PASSWORD_ARGON2I);
        
        $this->profile = $profile;
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
    
    // O Getter já estava certo, mas agora a propriedade bate com o retorno
    public function getProfile(): Profile { return $this->profile; }

    public function verifyPassword(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->password);
    }
}