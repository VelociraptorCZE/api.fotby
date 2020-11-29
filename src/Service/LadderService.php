<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Player;
use App\Repository\LadderEntryRepository;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Throwable;

class LadderService
{
    private LadderEntryRepository $ladderEntryRepository;

    public function __construct(LadderEntryRepository $ladderEntryRepository)
    {
        $this->ladderEntryRepository = $ladderEntryRepository;
    }

    public function getLadderData(Player $player): array
    {
        try {
            return $this->ladderEntryRepository->findLadderEntriesForMostGoalsScoredChallenge($player->getId());
        } catch (Throwable $e) {
            throw new BadRequestException('Could not get ladder data');
        }
    }
}