<?php

namespace TechFix\Core\UseCases\Auth;

use Exception;
use PragmaRX\Google2FA\Google2FA;
use TechFix\Core\Domain\Repository\UserRepositoryInterface;

class ConfirmTwoFactorUseCase
{
    private UserRepositoryInterface $userRepository;
    private Google2FA $google2fa;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->google2fa = new Google2FA();
    }

    public function execute(int $userId, string $code): bool
    {
        $user = $this->userRepository->findById($userId);

        if (!$user || !$user->getTwoFactorSecret()) {
            throw new Exception("Configuração de 2FA não iniciada.");
        }

        $valid = $this->google2fa->verifyKey($user->getTwoFactorSecret(), $code);

        if ($valid) {
            $user->setIsTwoFactorEnabled(true);
            $this->userRepository->update($user);
            return true;
        }

        return false;
    }
}