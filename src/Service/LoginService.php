<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Player;
use App\Transformer\PlayerTransformer;
use InvalidArgumentException;
use Throwable;
use App\Repository\PlayerRepository;

class LoginService extends ApiService
{
    protected array $requiredPayloadKeys = ['username', 'password'];
    protected string $defaultErrorMessage = 'Incorrect username or password';

    private PasswordEncryptionService $passwordEncryptionService;
    private PlayerRepository $playerRepository;
    private PlayerTransformer $playerTransformer;
    private LadderService $ladderService;

    public function __construct(
        PasswordEncryptionService $passwordEncryptionService,
        PlayerRepository $playerRepository,
        PlayerTransformer $playerTransformer,
        LadderService $ladderService
    ) {
        $this->passwordEncryptionService = $passwordEncryptionService;
        $this->playerRepository = $playerRepository;
        $this->playerTransformer = $playerTransformer;
        $this->ladderService = $ladderService;
    }

    public function login(array $payload): array
    {
        try {
            $this->validatePayload($payload);
            $player = $this->tryLogin($payload);

            return [
                'result' => true,
                'player' => $this->playerTransformer->transform($player),
                'ladder' => $this->ladderService->getLadderData($player)
            ];
        } catch (Throwable $e) {
            return [
                'result' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function tryLogin(array $payload): Player
    {
        $player = $this->playerRepository->findPlayerByUsername($payload['username']);

        if ($player === null) {
            throw new InvalidArgumentException($this->defaultErrorMessage);
        }

        $isPasswordCorrect = $this->passwordEncryptionService->isPasswordCorrect(
            $payload['password'],
            $player->getPassword()
        );

        if (!$isPasswordCorrect) {
            throw new InvalidArgumentException($this->defaultErrorMessage);
        }

        return $player;
    }
}