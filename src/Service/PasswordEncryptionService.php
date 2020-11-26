<?php
declare(strict_types=1);

namespace App\Service;

class PasswordEncryptionService
{
    public function encryptPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT) ?: '';
    }

    public function isPasswordCorrect(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}