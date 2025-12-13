<?php

namespace TechFix\Infrastructure\Http\Controller\Asset;

use TechFix\Core\UseCases\Asset\FindAssetUseCase;
use OpenApi\Attributes as OA;

class FindAssetController
{
    private FindAssetUseCase $useCase;

    public function __construct(FindAssetUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    #[OA\Get(
        path: "/api/assets/{id}",
        summary: "Busca asset por ID",
        tags: ["Assets"],
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
            new OA\Response(response: 200, description: "Asset encontrado"),
            new OA\Response(response: 404, description: "Asset não encontrado")
        ]
    )]
    #[OA\Get(
        path: "/api/assets",
        summary: "Busca assets por parâmetros (name, patrimony_code, serial_number) ou retorna todos",
        tags: ["Assets"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "name",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "patrimony_code",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "serial_number",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Asset(s) encontrado(s)"),
            new OA\Response(response: 404, description: "Asset não encontrado")
        ]
    )]
    public function handle(array $routeParams = [], array $queryParams = []): void
    {
        header('Content-Type: application/json');

        try {
            if (isset($routeParams['id'])) {
                $asset = $this->useCase->findById((int)$routeParams['id']);
                echo json_encode($asset);
                $this->respond($asset);
                return;
            }

            if (isset($queryParams['name'])) {
                $asset = $this->useCase->findByName($queryParams['name']);
                $this->respond($asset);
                return;
            }

            if (isset($queryParams['patrimony_code'])) {
                $asset = $this->useCase->findByPatrimonyCode($queryParams['patrimony_code']);
                $this->respond($asset);
                return;
            }

            if (isset($queryParams['serial_number'])) {
                $asset = $this->useCase->findBySerialNumber($queryParams['serial_number']);
                $this->respond($asset);
                return;
            }

            // sem identificadores -> retorna todos
            $assets = $this->useCase->findAll();
            $this->respondArray($assets);
        } catch (\Throwable $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function respond(?object $asset): void
    {
        if (!$asset) {
            http_response_code(404);
            echo json_encode(['error' => 'Asset not found']);
            return;
        }

        http_response_code(200);
        echo json_encode([
            'id' => $asset->getId() ?? null,
            'name' => $asset->getName() ?? null,
            'patrimony_code' => $asset->getPatrimonyCode() ?? null,
            'serial_number' => $asset->getSerialNumber() ?? null,
        ]);
    }

    private function respondArray(array $assets): void
    {
        if (empty($assets)) {
            http_response_code(404);
            echo json_encode(['error' => 'Asset not found']);
            return;
        }

        http_response_code(200);
        echo json_encode(array_map(fn($asset) => [
            'id' => $asset->getId() ?? null,
            'name' => $asset->getName() ?? null,
            'patrimony_code' => $asset->getPatrimonyCode() ?? null,
            'serial_number' => $asset->getSerialNumber() ?? null,
        ], $assets));
    }
}