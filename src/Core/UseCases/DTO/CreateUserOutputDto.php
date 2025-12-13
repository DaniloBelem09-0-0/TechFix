<?php

namespace TechFix\Core\UseCases\DTO;

use TechFix\Core\Domain\Entity\Profile;

class CreateUserOutputDto
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public Profile $profile
    ) {}
}