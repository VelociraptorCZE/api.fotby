<?php
declare(strict_types=1);

namespace App\Service;

use InvalidArgumentException;
use Throwable;
use App\Repository\PlayerRepository;

class LoginService extends ApiService
{
    protected array $requiredPayloadKeys = ['username', 'password'];
    protected string $defaultErrorMessage = 'Incorrect username or password';

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

    public function tryLogin(array $payload): int
    {
        $player = $this->playerRepository->findPlayerByUsername($payload['username']);
        $isPasswordCorrect = $this->passwordEncryptionService->isPasswordCorrect(
            $payload['password'],
            $player->getPassword()
        );

        if ($player === null || !$isPasswordCorrect) {
            throw new InvalidArgumentException($this->defaultErrorMessage);
        }

        return $player->getId();
    }
}