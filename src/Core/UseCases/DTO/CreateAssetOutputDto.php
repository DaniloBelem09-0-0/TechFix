<?php

namespace TechFix\Core\UseCases\DTO;

class CreateAssetOutputDto
{
    public function __construct(
        public int $id,
        public string $name,
        public string $patrimony_code,
        public string $serial_number
    ) {}
}