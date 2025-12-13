<?php

namespace TechFix\Infrastructure\Http\Controller\Asset;

use TechFix\Core\UseCases\Asset\DeleteAssetUseCase;
use OpenApi\Attributes as OA;

class DeleteAssetController
{
    private DeleteAssetUseCase $useCase;

    public function __construct(DeleteAssetUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    #[OA\Delete(
        path: "/api/assets/{id}",
        summary: "Deleta asset por ID",
        tags: ["Assets"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "Identificador do asset",
                required: true,
                schema: new OA\Schema(type: "integer", format: "int64")
            )
        ],
        responses: [
            new OA\Response(response: 204, description: "Asset deletado com sucesso"),
            new OA\Response(
                response: 404,
                description: "Asset não encontrado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Bad request"),
            new OA\Response(response: 401, description: "Unauthorized")
        ]
    )]
    public function handle(array $routeParams = [], array $queryParams = []): void
    {
        header('Content-Type: application/json');

        try {
            if (!isset($routeParams['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'ID is required']);
                return;
            }

            $this->useCase->delete((int)$routeParams['id']);

            http_response_code(204);
        } catch (\Throwable $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}