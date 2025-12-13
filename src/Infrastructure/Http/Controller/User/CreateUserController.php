<?php

namespace TechFix\Infrastructure\Http\Controller\User;

use OpenApi\Attributes as OA;
use TechFix\Core\Domain\Entity\Profile;
use TechFix\Core\UseCases\User\CreateUserUseCase;
use TechFix\Core\UseCases\DTO\CreateUserInputDto;
use TechFix\Core\Domain\Enum\Role;
use TechFix\Core\UseCases\Profile\ProfileUseCase;

class CreateUserController
{
    private CreateUserUseCase $useCase;

    public function __construct(CreateUserUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    #[OA\Post(
        path: "/api/users",
        summary: "Cria um novo usuário",
        tags: ["Users"],
        security: [["bearerAuth" => []]], 
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Danilo API"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "api@techfix.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "123456"),
                    new OA\Property(property: "profile", type: "string", example: "collaborator")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Usuário criado com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "name", type: "string"),
                        new OA\Property(property: "email", type: "string"),
                        new OA\Property(property: "profile", type: "string")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Erro de validação"),
            new OA\Response(response: 401, description: "Token não fornecido ou inválido"),
            new OA\Response(response: 403, description: "Acesso negado (apenas Admin)")
        ]
    )]
    public function handle(): void
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON']);
            return;
        }

        try {
            
            $inputDto = new CreateUserInputDto(
                $data['name'],
                $data['email'],
                $data['password'],
                $data['profile'],
            );

            $outputDto = $this->useCase->execute($inputDto);

            http_response_code(201);
            header('Content-Type: application/json');
            echo json_encode([
                'id' => $outputDto->id,
                'name' => $outputDto->name,
                'email' => $outputDto->email
            ]);

        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}