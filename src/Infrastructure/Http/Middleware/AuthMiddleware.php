<?php

namespace TechFix\Infrastructure\Http\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class AuthMiddleware
{
    private string $jwtSecret;

    public function __construct()
    {
        $this->jwtSecret = (string) ($_ENV['JWT_SECRET'] ?? getenv('JWT_SECRET') ?? '');
    }

    public function handle(array $allowedRoles = []): object
    {
        $headers = getallheaders();
        
        if (!isset($headers['Authorization'])) {
            $this->denyAccess('Token não fornecido');
        }

        if (!preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
            $this->denyAccess('Formato do token inválido');
        }

        $jwt = $matches[1];

        try {
            $decoded = JWT::decode($jwt, new Key($this->jwtSecret, 'HS256'));
            $userRole = $decoded->role ?? $decoded->profile ?? null;
            if (!empty($allowedRoles)) {
                if (!in_array(strtolower($userRole), $allowedRoles)) {
                    $this->denyAccess('Acesso Proibido: Role insuficiente', 403);
                }
            }

            return $decoded;

        } catch (Exception $e) {
            $this->denyAccess('Token inválido ou expirado: ' . $e->getMessage());
        }
        
        return (object)[]; 
    }

    private function denyAccess(string $message, int $code = 401): void
    {
        http_response_code($code);
        echo json_encode(['error' => $message]);
        exit;
    }
}
