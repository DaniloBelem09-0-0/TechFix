<?php

namespace TechFix\Core\UseCases\DTO;

use TechFix\Core\Domain\Entity\Profile;
use TechFix\Core\Domain\Enum\Priority;

class CreateTicketInputDto
{
    public function __construct(
        public string $title,
        public string $description,
        public Priority $priority,
        public int $asset_id
    ) {}

}