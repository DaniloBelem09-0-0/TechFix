<?php

namespace TechFix\Core\Domain\Repository;

use TechFix\Core\Domain\Entity\Asset;

interface AssetRepositoryInterface
{
    public function save(Asset $asset): void;
    public function findById(int $id): ?Asset;
    public function findByName(string $name): ?Asset;
    public function findByPatrimonyCode(string $patrimony_code): ?Asset;
    public function findBySerialNumber(string $serial_number): ?Asset;
    public function deleteById(int $id): void;
    public function update(Asset $asset): void;
    public function findAll(): array;
}
