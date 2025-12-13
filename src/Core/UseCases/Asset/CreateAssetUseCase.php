<?php

namespace TechFix\Core\UseCases\Asset;

use TechFix\Core\Domain\Entity\Asset;
use TechFix\Core\Domain\Repository\AssetRepositoryInterface;
use Exception;
use TechFix\Core\UseCases\DTO\CreateAssetInputDto;
use TechFix\Core\UseCases\DTO\CreateAssetOutputDto;

class CreateAssetUseCase
{
    private AssetRepositoryInterface $assetRepository;

    public function __construct(AssetRepositoryInterface $assetRepository)
    {
        $this->assetRepository = $assetRepository;
    }

    public function execute(CreateAssetInputDto $input): CreateAssetOutputDto
    {
        $existingAsset = $this->assetRepository->findByName($input->name);

        if ($existingAsset) {
            throw new Exception("Asset já cadastrado.");
        }

        $asset = new Asset(
            $input->name,
            $input->patrimony_code,
            $input->serial_number
        );

        $this->assetRepository->save($asset);

        return new CreateAssetOutputDto(
            $asset->getId(),
            $asset->getName(),
            $asset->getPatrimonyCode(),
            $asset->getSerialNumber()
        );
    }

}
