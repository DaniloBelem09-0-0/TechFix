<?php

namespace TechFix\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityManagerInterface;
use TechFix\Core\Domain\Entity\Asset;
use TechFix\Core\Domain\Repository\AssetRepositoryInterface;

class DoctrineAssetRepository implements AssetRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(Asset $asset): void
    {
        $this->entityManager->persist($asset);
        $this->entityManager->flush();
    }

    public function findById(int $id): ?Asset
    {
        return $this->entityManager->getRepository(Asset::class)->find($id);
    }

    public function deleteById(int $id): void
    {
        $asset = $this->findById($id);
        if ($asset) {
            $this->entityManager->remove($asset);
            $this->entityManager->flush();
        }
    }

    public function update(Asset $asset): void
    {
        $this->entityManager->persist($asset);
        $this->entityManager->flush();
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository(Asset::class)->findAll();
    }

    public function findByName(string $name): Asset|null
    {
        return $this->entityManager->getRepository(Asset::class)->findOneBy(['name' => $name]);
    }

    public function findByPatrimonyCode(string $patrimony_code): ?Asset
    {
        return $this->entityManager->getRepository(Asset::class)->findOneBy(['patrimony_code' => $patrimony_code]);
    }

    public function findBySerialNumber(string $serial_number): ?Asset
    {
        return $this->entityManager->getRepository(Asset::class)->findOneBy(['serial_number' => $serial_number]);
    }
}