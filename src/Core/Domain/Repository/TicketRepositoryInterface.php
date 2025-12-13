<?php

namespace TechFix\Core\Domain\Repository;

use TechFix\Core\Domain\Entity\Ticket;

interface TicketRepositoryInterface
{
    public function save(Ticket $ticket): void;
    public function findByEmail(string $email): ?Ticket;
    public function findByUsername(string $username): ?Ticket;
    public function findById(int $id): ?Ticket;
    public function delete(Ticket $ticket): void;
    public function findAll(): array;
}