<?php

namespace TechFix\Core\UseCases\Profile;

use TechFix\Core\Domain\Repository\ProfileRepositoryInterface;

class ProfileUseCase
{
    private ProfileRepositoryInterface $profileRepository;

    public function __construct(ProfileRepositoryInterface $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    public function findProfileByRole(string $role)
    {
        return $this->profileRepository->findByRole($role);
    }
}