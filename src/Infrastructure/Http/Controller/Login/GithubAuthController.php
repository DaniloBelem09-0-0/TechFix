<?php

namespace TechFix\Infrastructure\Http\Controller\Login;

use League\OAuth2\Client\Provider\Github;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use TechFix\Core\UseCases\Login\LoginGithubUseCase;
use OpenApi\Attributes as OA;

class GithubAuthController
{
    private LoginGithubUseCase $useCase;
    private Github $provider;

    public function __construct(LoginGithubUseCase $useCase)
    {
        $this->useCase = $useCase;
        
        $this->provider = new Github([
            'clientId'     => $_ENV['GITHUB_CLIENT_ID'],
            'clientSecret' => $_ENV['GITHUB_CLIENT_SECRET'],
            'redirectUri'  => $_ENV['GITHUB_REDIRECT_URI'],
        ]);
    }

    #[OA\Get(
        path: "/api/auth/github/url",
        summary: "Obtém a URL de login do GitHub",
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: 200,
                description: "URL para redirecionamento",
                content: new OA\JsonContent(properties: [new OA\Property(property: "url", type: "string")])
            )
        ]
    )]
    public function getUrl(): void
    {
        $options = [
            'scope' => ['user:email']
        ];
        
        $authUrl = $this->provider->getAuthorizationUrl($options);
        
        header('Content-Type: application/json');
        echo json_encode(['url' => $authUrl]);
    }

    #[OA\Get(
        path: "/api/auth/github/callback",
        summary: "Callback do GitHub",
        tags: ["Auth"],
        parameters: [
            new OA\Parameter(name: "code", in: "query", required: true, schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(response: 200, description: "JWT Gerado"),
            new OA\Response(response: 400, description: "Erro")
        ]
    )]
    public function callback(): void
    {
        if (ob_get_level()) ob_end_clean();

        if (empty($_GET['code'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Código não fornecido']);
            return;
        }

        try {
            $token = $this->provider->getAccessToken('authorization_code', [
                'code' => $_GET['code']
            ]);

            $userGithub = $this->provider->getResourceOwner($token);

            if (!$userGithub instanceof GithubResourceOwner) {
                throw new \Exception("Dados inválidos retornados pelo GitHub.");
            }

            $githubUserData = [
                'email'    => $userGithub->getEmail(),
                'name'     => $userGithub->getName() ?? $userGithub->getNickname(),
                'profile' => 'COLLABORATOR'
            ];

            $outputDto = $this->useCase->execute($githubUserData);

            header('Content-Type: application/json');
            echo json_encode([
                'access_token' => $outputDto->token,
                'token_type'   => 'Bearer',
                'expires_in'   => 3600
            ]);

        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => 'GitHub Login Failed: ' . $e->getMessage()]);
        }
        exit;
    }
}
