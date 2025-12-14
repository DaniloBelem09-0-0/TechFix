<?php

namespace TechFix\Infrastructure\Http\Controller\Auth;

use TechFix\Core\UseCases\Auth\GenerateTwoFactorUseCase;
use TechFix\Core\UseCases\Auth\ConfirmTwoFactorUseCase;
use TechFix\Infrastructure\Http\Middleware\AuthMiddleware; 
use OpenApi\Attributes as OA;

class TwoFactorController
{
    private GenerateTwoFactorUseCase $generateUseCase;
    private ConfirmTwoFactorUseCase $confirmUseCase;

    public function __construct(
        GenerateTwoFactorUseCase $generateUseCase,
        ConfirmTwoFactorUseCase $confirmUseCase
    ) {
        $this->generateUseCase = $generateUseCase;
        $this->confirmUseCase = $confirmUseCase;
    }

    #[OA\Post(
        path: "/api/auth/2fa/generate",
        summary: "Inicia configuração 2FA (Gera QR Code)",
        tags: ["Auth"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "QR Code gerado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "secret", type: "string"),
                        new OA\Property(property: "qr_code_svg_base64", type: "string")
                    ]
                )
            )
        ]
    )]
    public function generate(): void
    {
        try {
            $middleware = new AuthMiddleware();
            $userPayload = $middleware->handle(); 
            $userId = $userPayload->sub;

            $data = $this->generateUseCase->execute($userId);

            header('Content-Type: application/json');
            echo json_encode($data);

        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    #[OA\Post(
        path: "/api/auth/2fa/confirm",
        summary: "Valida o código e ativa o 2FA",
        tags: ["Auth"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [new OA\Property(property: "code", type: "string", example: "123456")]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "2FA Ativado com sucesso"),
            new OA\Response(response: 400, description: "Código inválido")
        ]
    )]
    public function confirm(): void
    {
        $json = json_decode(file_get_contents('php://input'), true);
        $code = $json['code'] ?? '';

        try {
            $middleware = new AuthMiddleware();
            $userPayload = $middleware->handle();
            $userId = $userPayload->sub;

            $success = $this->confirmUseCase->execute($userId, $code);

            if ($success) {
                echo json_encode(['message' => '2FA ativado com sucesso!']);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Código incorreto ou expirado.']);
            }

        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}