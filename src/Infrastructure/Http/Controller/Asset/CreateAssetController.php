<?php

namespace TechFix\Infrastructure\Http\Controller\Asset;

use TechFix\Core\UseCases\Asset\CreateAssetUseCase;
use TechFix\Core\UseCases\DTO\CreateAssetInputDto;
use OpenApi\Attributes as OA;

class CreateAssetController
{

    private CreateAssetUseCase $useCase;

    public function __construct(CreateAssetUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    #[OA\Post(
        path: "/api/assets",
        summary: "Cadastra um novo equipamento (Ativo)",
        description: "Cria um novo item no inventário. Requer permissão de ADMIN ou TECHNICIAN.",
        tags: ["Assets"],
        security: [["bearerAuth" => []]], 
        requestBody: new OA\RequestBody(
            description: "Dados do equipamento",
            required: true,
            content: new OA\JsonContent(
                required: ["name", "patrimony_code", "serial_number"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Notebook Dell Latitude 5420"),
                    new OA\Property(property: "patrimony_code", type: "string", description: "Código único de patrimônio", example: "PAT-2024-001"),
                    new OA\Property(property: "serial_number", type: "string", description: "Número de série do fabricante", example: "JFK-5542-X")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Ativo criado com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 10),
                        new OA\Property(property: "name", type: "string", example: "Notebook Dell Latitude 5420"),
                        new OA\Property(property: "patrimony_code", type: "string", example: "PAT-2024-001"),
                        new OA\Property(property: "serial_number", type: "string", example: "JFK-5542-X")
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Erro de validação ou Patrimônio já existente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string", example: "Patrimônio já cadastrado.")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Token não fornecido ou inválido"),
            new OA\Response(response: 403, description: "Acesso negado (Role insuficiente)")
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

            $inputDto = new CreateAssetInputDto(
                $data['name'],
                $data['patrimony_code'],
                $data['serial_number']
            );

            $outputDto = $this->useCase->execute($inputDto);

            http_response_code(201);
            header('Content-Type: application/json');
            echo json_encode([
                'id' => $outputDto->id,
                'name' => $outputDto->name,
                'patrimony_code' => $outputDto->patrimony_code,
                'serial_number' => $outputDto->serial_number
            ]);

        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}