<?php

namespace TechFix\Core\UseCases\DTO;

class CreateAssetInputDto
{
    public function __construct(
        public string $name,
        public string $patrimony_code,
        public string $serial_number
    ) {}
}
