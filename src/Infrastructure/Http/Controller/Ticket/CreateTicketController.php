<?php

namespace TechFix\Infrastructure\Http\Controller\Ticket;

use OpenApi\Attributes as OA;
use TechFix\Core\UseCases\Ticket\TicketUseCase;
use TechFix\Core\Domain\Entity\Ticket;

class CreateTicketController
{
    private TicketUseCase $useCase;

    public function __construct(TicketUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    #[OA\Post(
        path: "/api/tickets",
        summary: "Cria um novo ticket",
        tags: ["Tickets"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Impressora não funciona"),
                    new OA\Property(property: "description", type: "string", example: "A impressora do setor financeiro está com erro E12"),
                    new OA\Property(property: "asset_id", type: "integer", example: 123),
                    new OA\Property(property: "priority", type: "string", example: "high")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Ticket criado com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "ticket_id", type: "integer"),
                        new OA\Property(property: "status", type: "string", example: "created")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Erro de validação"),
            new OA\Response(response: 401, description: "Token não fornecido ou inválido"),
            new OA\Response(response: 403, description: "Acesso negado")
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
            $input = new Ticket($data['title'], $data['description'], $data['asset_id'], $data['priority']);
            $output = $this->useCase->save($input);

            http_response_code(201);
            header('Content-Type: application/json');
            echo json_encode([
                'ticket_id' => $output,
                'status'    => 'created',
            ]);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

}