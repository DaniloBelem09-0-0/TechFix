<?php

namespace TechFix\Infrastructure\Http\Controller\Login;

use OpenApi\Attributes as OA;
use TechFix\Core\UseCases\DTO\LoginInputDto;
use TechFix\Core\UseCases\Login\LoginUseCase;

class LoginController
{
    private LoginUseCase $useCase;

    public function __construct(LoginUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    #[OA\Post(
        path: "/api/login",
        summary: "Autentica um usuário e retorna um JWT",
        tags: ["Auth"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "email", type: "string", example: "admin@techfix.com"),
                    new OA\Property(property: "password", type: "string", example: "admin123")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Login realizado com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "token", type: "string", description: "JWT Token")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Credenciais inválidas")
        ]
    )]
    public function handle(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        try {
            $input = new LoginInputDto($data['email'], $data['password']);
            $output = $this->useCase->execute($input);

            echo json_encode([
                'token' => $output->token,
                'token_type'   => 'Bearer',     
                'expires_in'   => 3600
            ]);

        } catch (\Exception $e) {
            http_response_code(401); // Unauthorized
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}