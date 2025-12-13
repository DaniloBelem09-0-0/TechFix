<?php
namespace TechFix\Core\UseCases\DTO;

class LoginInputDto
{
    public function __construct(public string $email, public string $password) {}
}