<?php
declare(strict_types=1);

namespace App\Service;

use Throwable;
use App\Repository\LadderEntryRepository;

class LadderService extends ApiService
{
    protected string $defaultErrorMessage = 'Could not get ladder data';

    private LadderEntryRepository $ladderEntryRepository;

    public function __construct(LadderEntryRepository $ladderEntryRepository)
    {
        $this->ladderEntryRepository = $ladderEntryRepository;
    }

    public function getLadderData(array $payload): array
    {
        try {
            $this->validatePayload($payload);

            return [
                'result' => true,
                'ladderData' => $this->ladderEntryRepository->findLadderEntriesForMostGoalsScoredChallenge()
            ];
        } catch (Throwable $e) {
            return [
                'result' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}