<?php
declare(strict_types=1);

namespace App\Service;

use DateTime;
use Throwable;
use App\Config;
use App\Entity\Player;
use App\Repository\PlayerRepository;
use Doctrine\Migrations\Finder\Exception\NameIsReserved;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class RegisterService
{
    private const DEFAULT_ERROR_MESSAGE = 'Account cannot be created';

    private PasswordEncryptionService $passwordEncryptionService;
    private PlayerRepository $playerRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        PasswordEncryptionService $passwordEncryptionService,
        PlayerRepository $playerRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->passwordEncryptionService = $passwordEncryptionService;
        $this->playerRepository = $playerRepository;
        $this->entityManager = $entityManager;
    }

    public function registerNewPlayer(array $payload): array
    {
        try {
            $this->validatePayload($payload);

            $username = $payload['username'];
            $password = $payload['password'];
            $hash = $this->passwordEncryptionService->encryptPassword($password);

            $this->validatePlayerName($username);
            $this->validatePassword($username, $password, $hash);
            $this->saveUser($username, $hash);

            return [
                'result' => true,
                'message' => 'Account has been successfully created'
            ];
        } catch (Throwable $e) {
            return [
                'result' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function saveUser(string $username, string $hash): void
    {
        $player = new Player;
        $player->setUsername($username);
        $player->setPassword($hash);
        $player->setCreatedAt(new DateTime);

        $this->entityManager->persist($player);
        $this->entityManager->flush();
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

    private function validatePassword(string $username, string $password, string $hash): void
    {
        if (strlen($hash) === 0) {
            throw new InvalidArgumentException(self::DEFAULT_ERROR_MESSAGE);
        }

        if ($username === $password) {
            throw new InvalidArgumentException('Password must differ from the username');
        }

        if (strlen($password) < 8) {
            throw new InvalidArgumentException('Password must be at least 8 characters long');
        }
    }

    private function validatePlayerName(string $username): void
    {
        if (!$this->playerRepository->isUsernameUnique($username)) {
            throw new NameIsReserved('This name is already taken');
        }
    }
}