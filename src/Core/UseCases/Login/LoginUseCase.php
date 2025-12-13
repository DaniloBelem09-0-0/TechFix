<?php

namespace TechFix\Core\UseCases\Login;

use Exception;
use Firebase\JWT\JWT; 
use TechFix\Core\Domain\Repository\UserRepositoryInterface;
use TechFix\Core\UseCases\DTO\LoginInputDto;
use TechFix\Core\UseCases\DTO\LoginOutputDto;

class LoginUseCase
{
    private UserRepositoryInterface $repository;

    private string $jwtSecret;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;

        $secret = $_ENV['JWT_SECRET'] ?? getenv('JWT_SECRET');
        if (!is_string($secret) || $secret === '') {
            throw new Exception('JWT_SECRET is not configured in environment');
        }
        $this->jwtSecret = $secret;
    }

    public function execute(LoginInputDto $input): LoginOutputDto
    {
        $user = $this->repository->findByEmail($input->email);

        if (!$user) {
            throw new Exception("Credenciais inválidas."); 
        }

        if (!$user->verifyPassword($input->password)) {
            throw new Exception("Credenciais inválidas.");
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