<?php
namespace TechFix\Core\UseCases\DTO;

class LoginOutputDto
{
    public function __construct(public string $token) {}
}