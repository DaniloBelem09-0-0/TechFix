<?php

namespace TechFix\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityManagerInterface;
use TechFix\Core\Domain\Entity\Ticket;
use TechFix\Core\Domain\Repository\TicketRepositoryInterface;

class DoctrineTicketRepository implements TicketRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(Ticket $ticket): void
    {
        $this->entityManager->persist($ticket);
        $this->entityManager->flush();
    }

    public function findByEmail(string $email): ?Ticket
    {
        return $this->entityManager->getRepository(Ticket::class)->findOneBy(['email' => $email]);
    }

    public function delete(Ticket $ticket): void
    {
        $this->entityManager->remove($ticket);
        $this->entityManager->flush();
    }

    public function update(Ticket $ticket): void
    {
        $this->entityManager->persist($ticket);
        $this->entityManager->flush();
    }

    public function findByUsername(string $username): ?Ticket
    {
        return $this->entityManager->getRepository(Ticket::class)->findOneBy(['username' => $username]);
    }

    public function findById(int $id): ?Ticket
    {
        return $this->entityManager->getRepository(Ticket::class)->find($id);
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository(Ticket::class)->findAll();
    }
}