<?php

namespace TechFix\Core\UseCases\Asset;

use TechFix\Core\Domain\Repository\AssetRepositoryInterface;

class DeleteAssetUseCase
{
    private AssetRepositoryInterface $assetRepository;

    public function __construct(AssetRepositoryInterface $assetRepository)
    {
        $this->assetRepository = $assetRepository;
    }

    public function delete(int $id): void
    {
        $this->assetRepository->deleteById($id);
    }
}