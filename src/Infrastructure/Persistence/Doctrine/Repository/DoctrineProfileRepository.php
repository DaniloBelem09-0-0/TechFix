<?php

namespace TechFix\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityManagerInterface;
use TechFix\Core\Domain\Entity\Profile;
use TechFix\Core\Domain\Repository\ProfileRepositoryInterface;

class DoctrineProfileRepository implements ProfileRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function save(Profile $profile): void
    {
        $this->entityManager->persist($profile);
        $this->entityManager->flush();
    }

    public function delete(Profile $profile): void
    {
        $this->entityManager->remove($profile);
        $this->entityManager->flush();
    }

    public function update(Profile $profile): void
    {
        $this->entityManager->persist($profile);
        $this->entityManager->flush();
    }
    public function findByRole(string $role): ?Profile
    {
        return $this->entityManager->getRepository(Profile::class)->findOneBy(['role' => $role]);
    }
}