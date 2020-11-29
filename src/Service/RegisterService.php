<?php
declare(strict_types=1);

namespace App\Service;

use Throwable;
use App\Factory\PlayerFactory;
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
    private PlayerFactory $playerFactory;

    public function __construct(
        PasswordEncryptionService $passwordEncryptionService,
        PlayerRepository $playerRepository,
        EntityManagerInterface $entityManager,
        PlayerFactory $playerFactory
    ) {
        $this->passwordEncryptionService = $passwordEncryptionService;
        $this->playerRepository = $playerRepository;
        $this->entityManager = $entityManager;
        $this->playerFactory = $playerFactory;
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
        $player = $this->playerFactory->create($username, $hash, $email);

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