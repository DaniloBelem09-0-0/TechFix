<?php

namespace TechFix\Core\UseCases\Asset;

use TechFix\Core\Domain\Entity\Asset;
use TechFix\Core\Domain\Repository\AssetRepositoryInterface;
use Exception;
use TechFix\Core\UseCases\DTO\CreateAssetInputDto;
use TechFix\Core\UseCases\DTO\CreateAssetOutputDto;

class FindAssetUseCase 
{
    private AssetRepositoryInterface $assetRepository;

    public function __construct(AssetRepositoryInterface $assetRepository)
    {
        $this->assetRepository = $assetRepository;
    }

    public function findById(int $id): ?Asset
    {
        $asset = $this->assetRepository->findById($id);

        if (!$asset) {
            throw new Exception("Asset not found.");
        }

        return $asset;
    }

    public function findByName(string $name): ?Asset
    {
        $asset = $this->assetRepository->findByName($name);

        if (!$asset) {
            throw new Exception("Asset not found.");
        }

        return $asset;
    }   

    public function findByPatrimonyCode(string $patrimony_code): ?Asset
    {
        $asset = $this->assetRepository->findByPatrimonyCode($patrimony_code);

        if (!$asset) {
            throw new Exception("Asset not found.");
        }

        return $asset;
    }

    public function findBySerialNumber(string $serial_number): ?Asset
    {
        $asset = $this->assetRepository->findBySerialNumber($serial_number);

        if (!$asset) {
            throw new Exception("Asset not found.");
        }

        return $asset;
    }

    public function findAll(): array
    {
        return $this->assetRepository->findAll();
    }
}