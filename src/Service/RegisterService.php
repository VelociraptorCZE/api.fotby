<?php
declare(strict_types=1);

namespace App\Service;

use DateTime;
use Throwable;
use App\Entity\Player;
use App\Repository\PlayerRepository;
use Doctrine\Migrations\Finder\Exception\NameIsReserved;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

class RegisterService extends ApiService
{
    protected array $requiredPayloadKeys = ['email', 'username', 'password'];
    protected string $defaultErrorMessage = 'Account cannot be created';

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

            ['username' => $username, 'password' => $password, 'email' => $email] = $payload;
            $hash = $this->passwordEncryptionService->encryptPassword($password);

            $this->validatePlayerCredentials($username, $email);
            $this->validatePassword($username, $password, $hash);
            $this->saveUser($username, $email, $hash);

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

    private function saveUser(string $username, string $email, string $hash): void
    {
        $player = new Player;
        $player->setUsername($username);
        $player->setPassword($hash);
        $player->setEmail($email);
        $player->setCreatedAt(new DateTime);

        $this->entityManager->persist($player);
        $this->entityManager->flush();
    }

    private function validatePassword(string $username, string $password, string $hash): void
    {
        if (strlen($hash) === 0) {
            throw new InvalidArgumentException($this->defaultErrorMessage);
        }

        if ($username === $password) {
            throw new InvalidArgumentException('Password must differ from the username');
        }

        if (strlen($password) < 8) {
            throw new InvalidArgumentException('Password must be at least 8 characters long');
        }
    }

    private function validatePlayerCredentials(string $username, string $email): void
    {
        if (!$this->playerRepository->isPlayerDataUnique($username, $email)) {
            throw new NameIsReserved('This name or email is already taken');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
        }
    }
}