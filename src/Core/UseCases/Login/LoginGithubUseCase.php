<?php

namespace TechFix\Core\UseCases\Login;

use Exception;
use Firebase\JWT\JWT; 
use TechFix\Core\Domain\Entity\User;
use TechFix\Core\Domain\Repository\UserRepositoryInterface;
use TechFix\Core\UseCases\DTO\CreateUserInputDto;
use TechFix\Core\UseCases\DTO\LoginInputDto;
use TechFix\Core\UseCases\DTO\LoginOutputDto;
use TechFix\Core\UseCases\User\CreateUserUseCase;

class LoginGithubUseCase
{
    private UserRepositoryInterface $repository;

    private string $jwtSecret;
    private CreateUserUseCase $createUserUseCase;

    public function __construct(UserRepositoryInterface $repository, CreateUserUseCase $createUserUseCase)
    {
        $this->repository = $repository;
        $this->createUserUseCase = $createUserUseCase;

        $secret = $_ENV['JWT_SECRET'] ?? getenv('JWT_SECRET');
        if (!is_string($secret) || $secret === '') {
            throw new Exception('JWT_SECRET is not configured in environment');
        }
        $this->jwtSecret = $secret;
    }

    public function execute(array $loginInputDto): LoginOutputDto
    {
        $user = $this->repository->findByEmail($loginInputDto['email']);

        if (!$user) {
            $createUserDto = new CreateUserInputDto(
                name: $loginInputDto['name'],
                email: $loginInputDto['email'],
                password: bin2hex(random_bytes(8)), 
                profile: $loginInputDto['profile'] 
            );

            $this->createUserUseCase->execute($createUserDto);

            $user = $this->repository->findByEmail($loginInputDto['email']);

            if(!$user) {
                throw new Exception("Erro ao criar usuário vindo do Github.");
            }
        }

        $payload = [
            'iss' => 'techfix-api', 
            'iat' => time(),        
            'exp' => time() + 3600, 
            'sub' => $user->getId(),
            'email' => $user->getEmail(),
            'profile' => $user->getProfile()->getRole()
        ];

        $token = JWT::encode($payload, $this->jwtSecret, 'HS256');

        return new LoginOutputDto($token);
    }
}