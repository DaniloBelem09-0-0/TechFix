<?php

namespace TechFix\Infrastructure\Http;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    description: "API de Service Desk TechFix",
    title: "TechFix API",
    contact: new OA\Contact(email: "suporte@techfix.com")
)]
#[OA\Server(
    url: "http://localhost:8085",
    description: "Servidor Docker Local"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth", 
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT",
    description: "Insira o token JWT aqui"
)]
class ApiDocumentation
{
    // Este arquivo fica vazio, serve apenas para guardar as anotações globais
}