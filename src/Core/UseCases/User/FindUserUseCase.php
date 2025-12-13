<?php

namespace TechFix\Core\UseCases\User;

use TechFix\Core\Domain\Repository\UserRepositoryInterface;

class FindUserUseCase
{
    private UserRepositoryInterface $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function findByEmail(string $email)
    {
        return $this->repository->findByEmail($email);
    }

    public function findByUsername(string $username)
    {
        return $this->repository->findByUsername($username);
    }

    public function findById(int $id)
    {
        return $this->repository->findById($id);
    }
}