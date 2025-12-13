<?php

namespace TechFix\Core\UseCases\DTO;

use TechFix\Core\Domain\Entity\Profile;

class CreateUserInputDto
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public string $profile
    ) {}
}