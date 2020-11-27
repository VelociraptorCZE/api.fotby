<?php
declare(strict_types=1);

namespace App\Service;

use InvalidArgumentException;
use Throwable;
use App\Config;
use App\Repository\PlayerRepository;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class LoginService
{
    private const DEFAULT_ERROR_MESSAGE = 'Incorrect username or password';

    private PasswordEncryptionService $passwordEncryptionService;
    private PlayerRepository $playerRepository;

    public function __construct(
        PasswordEncryptionService $passwordEncryptionService,
        PlayerRepository $playerRepository
    ) {
        $this->passwordEncryptionService = $passwordEncryptionService;
        $this->playerRepository = $playerRepository;
    }

    public function login(array $payload): array
    {
        try {
            $this->validatePayload($payload);
            $playerId = $this->tryLogin($payload);

            return [
                'result' => true,
                'playerId' => $playerId,
                'playerInfo' => []
            ];
        } catch (Throwable $e) {
            return [
                'result' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function validatePayload(array $payload): void
    {
        $isPayloadValid = isset($payload['username'])
            && isset($payload['password'])
            && isset($payload['apiKey'])
            && $payload['apiKey'] === Config::SECRET_API_KEY;

        if (!$isPayloadValid) {
            throw new BadRequestException(self::DEFAULT_ERROR_MESSAGE);
        }
    }

    public function tryLogin(array $payload): int
    {
        $player = $this->playerRepository->findPlayerByUsername($payload['username']);
        $isPasswordCorrect = $this->passwordEncryptionService->isPasswordCorrect(
            $payload['password'],
            $player->getPassword()
        );

        if ($player === null || !$isPasswordCorrect) {
            throw new InvalidArgumentException(self::DEFAULT_ERROR_MESSAGE);
        }

        return $player->getId();
    }
}