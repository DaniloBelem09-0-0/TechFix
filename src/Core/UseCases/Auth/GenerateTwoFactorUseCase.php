<?php

namespace TechFix\Core\UseCases\Auth;

use Exception;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use TechFix\Core\Domain\Repository\UserRepositoryInterface;

class GenerateTwoFactorUseCase
{
    private UserRepositoryInterface $userRepository;
    private Google2FA $google2fa;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->google2fa = new Google2FA();
    }

    public function execute(int $userId): array
    {
        $user = $this->userRepository->findById($userId); 

        if (!$user) {
            throw new Exception("Usuário não encontrado.");
        }

        $secret = $this->google2fa->generateSecretKey();

        $user->setTwoFactorSecret($secret);
        $this->userRepository->update($user);

        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            'TechFix',
            $user->getEmail(),
            $secret
        );

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($qrCodeUrl);

        return [
            'secret' => $secret, 
            'qr_code_svg' => base64_encode($qrCodeSvg)
        ];
    }
}