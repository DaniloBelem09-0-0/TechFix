<?php

namespace TechFix\Core\Domain\Repository;

use TechFix\Core\Domain\Entity\Profile;

interface ProfileRepositoryInterface
{
    public function findByRole(string $role): ?Profile;
}