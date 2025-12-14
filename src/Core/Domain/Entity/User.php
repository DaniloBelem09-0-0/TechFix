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

    #[ORM\ManyToOne(targetEntity: Profile::class)]
    #[ORM\JoinColumn(name: 'profile_id', referencedColumnName: 'id', nullable: false)]
    private Profile $profile;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $twoFactorSecret = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isTwoFactorEnabled = false;

    public function __construct(string $name, string $email, string $password, Profile $profile)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = password_hash($password, PASSWORD_ARGON2I);
        $this->profile = $profile;
        $this->isTwoFactorEnabled = false;
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
    
    public function getProfile(): Profile { return $this->profile; }

    public function verifyPassword(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->password);
    }

    public function getTwoFactorSecret(): ?string
    {
        return $this->twoFactorSecret;
    }

    public function setTwoFactorSecret(?string $secret): void
    {
        $this->twoFactorSecret = $secret;
    }

    public function isTwoFactorEnabled(): bool
    {
        return $this->isTwoFactorEnabled;
    }

    public function setIsTwoFactorEnabled(bool $status): void
    {
        $this->isTwoFactorEnabled = $status;
    }


}