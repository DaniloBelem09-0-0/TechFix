<?php

namespace TechFix\Core\Domain\Repository;

use TechFix\Core\Domain\Entity\User;

interface UserRepositoryInterface
{
    public function save(User $user): void;
    public function findByEmail(string $email): ?User;
    public function findByUsername(string $username): ?User;
    public function findById(int $id): ?User;
    public function delete(User $user): void;
    public function update(User $user): void;
    
}