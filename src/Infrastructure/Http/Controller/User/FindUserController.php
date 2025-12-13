<?php

namespace TechFix\Infrastructure\Http\Controller\User;

use TechFix\Core\UseCases\User\FindUserUseCase;
use OpenApi\Attributes as OA;

class FindUserController
{
    private FindUserUseCase $useCase;

    public function __construct(FindUserUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    #[OA\Get(
        path: "/api/users/{id}",
        summary: "Busca usuário por ID",
        tags: ["Users"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id", 
                in: "path", 
                required: true, 
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Usuário encontrado"),
            new OA\Response(response: 404, description: "Usuário não encontrado")
        ]
    )]
    // --- ROTA 2: BUSCA POR EMAIL (/api/users?email=...) ---
    #[OA\Get(
        path: "/api/users",
        summary: "Busca usuário por Email",
        tags: ["Users"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "email", 
                in: "query",   // <--- Note que aqui é 'query', não 'path'
                required: true, 
                schema: new OA\Schema(type: "string", format: "email"),
                description: "Email do usuário para busca"
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Usuário encontrado"),
            new OA\Response(response: 404, description: "Usuário não encontrado")
        ]
    )]
    public function handle(array $routeParams = [], array $queryParams = []):void
    {
        if(isset($routeParams['id'])){
            $user = $this->useCase->findById((int)$routeParams['id']);
            $this->respond($user);
            return;
        }

        if(isset($queryParams['email'])){
            $user = $this->useCase->findByEmail($queryParams['email']);
            $this->respond($user);
            return;
        }

        if(isset($queryParams['name'])){
            //TODO colocar essa opção no swagger
            $user = $this->useCase->findByUsername($queryParams['name']);
            $this->respond($user);
            return;
        }

        http_response_code(400);
        echo json_encode(['error' => 'Parâmetros insuficientes para busca de usuário']);
    }

    private function respond($user): void
    {
        if ($user) {
            http_response_code(200);
            echo json_encode([
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'profile' => $user->getProfile()->getRole()
            ]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Usuário não encontrado']);
        }
    }
}