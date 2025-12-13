<?php

namespace TechFix\Core\UseCases\User;

use Exception;
use TechFix\Core\Domain\Entity\User;
use TechFix\Core\Domain\Repository\UserRepositoryInterface;
use TechFix\Core\Domain\Repository\ProfileRepositoryInterface;
use TechFix\Core\UseCases\DTO\CreateUserInputDto;
use TechFix\Core\UseCases\DTO\CreateUserOutputDto;
use TechFix\Core\Domain\Enum\Role;

class CreateUserUseCase
{
    private UserRepositoryInterface $userRepository;
    private ProfileRepositoryInterface $profileRepository;

    public function __construct(UserRepositoryInterface $userRepository, ProfileRepositoryInterface $profileRepository)
    {
        $this->userRepository = $userRepository;
        $this->profileRepository = $profileRepository;
    }

    public function execute(CreateUserInputDto $input): CreateUserOutputDto
    {
        
        $existingUser = $this->userRepository->findByEmail($input->email);

        if ($existingUser) {
            throw new Exception("Email já cadastrado.");
        }

        $profileRole = $input->profile;
        $profileEntity = $this->profileRepository->findByRole($profileRole);

        if (!$profileEntity) {
            throw new Exception("Perfil '{$profileRole}' não encontrado.");
        }

        $user = new User(
            $input->name,
            $input->email,
            $input->password,
            $profileEntity
        );

        $this->userRepository->save($user);

        return new CreateUserOutputDto(
            $user->getId(),
            $user->getName(),
            $user->getEmail(),
            $user->getProfile()
        );
    }
}