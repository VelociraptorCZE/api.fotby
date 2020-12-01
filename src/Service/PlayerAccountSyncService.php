<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\LadderEntry;
use App\Entity\Player;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Throwable;

class PlayerAccountSyncService extends ApiService
{
    protected array $requiredPayloadKeys = ['username'];
    protected string $defaultErrorMessage = 'Synchronization failed';

    private PlayerRepository $playerRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        PlayerRepository $playerRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->playerRepository = $playerRepository;
        $this->entityManager = $entityManager;
    }

    public function sync(array $payload): array
    {
        try {
            $this->validatePayload($payload);
            $player = $this->syncPlayerData($payload);
            $this->syncLadderData($payload, $player);

            return ['result' => true];
        } catch (Throwable $e) {
            return [
                'result' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function syncPlayerData(array $payload): Player
    {
        $player = $this->playerRepository->findPlayerByUsername($payload['username']);

        if ($player === null) {
            throw new InvalidArgumentException($this->defaultErrorMessage);
        }

        $playerAccount = $player->getPlayerAccount();
        $playerSkins = $playerAccount->getSkins();

        if (isset($payload['balance'])) {
            $playerAccount->setBalance((int)$payload['balance']);
        }

        if (isset($payload['addSkin']) && !in_array($payload['addSkin'], $playerSkins, true)) {
            $playerAccount->setSkins([$payload['addSkin'], ...$playerSkins]);
        }

        if (isset($payload['equippedSkin']) && in_array($payload['equippedSkin'], $playerAccount->getSkins(), true)) {
            $playerAccount->setEquippedSkin($payload['equippedSkin']);
        }

        $this->entityManager->persist($playerAccount);
        $this->entityManager->flush();

        return $player;
    }

    private function syncLadderData(array $payload, Player $player): void
    {
        $ladderEntry = $payload['ladderEntry'] ?? null;

        if (isset($ladderEntry)) {
            $ladderEntry = new LadderEntry;
            $ladderEntry->setPlayer($player);
            $ladderEntry->setGoalsScored((int)$ladderEntry['goalsScored']);
            $ladderEntry->setGoalsAgainst((int)$ladderEntry['goalsAgainst']);

            $this->entityManager->persist($ladderEntry);
            $this->entityManager->flush();
        }
    }
}