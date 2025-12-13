<?php

namespace TechFix\Core\UseCases\Ticket;

use TechFix\Core\Domain\Entity\Ticket;
use TechFix\Core\Domain\Repository\ProfileRepositoryInterface;
use TechFix\Core\Domain\Repository\TicketRepositoryInterface;

class TicketUseCase
{
    private TicketRepositoryInterface $ticketRepository;

    public function __construct(TicketRepositoryInterface $ticketRepository)
    {
        $this->ticketRepository = $ticketRepository;
    }

    public function findTicketById(int $id)
    {
        return $this->ticketRepository->findById($id);
    }

    public function save(Ticket $ticket):int
    {
        $this->ticketRepository->save($ticket);
        return $ticket->getId();
    }

    public function findAll(): array
    {
        return $this->ticketRepository->findAll();
    }

    public function delete(Ticket $ticket): void
    {
        $this->ticketRepository->delete($ticket);
    }
}