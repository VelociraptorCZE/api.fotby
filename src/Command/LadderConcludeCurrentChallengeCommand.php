<?php
declare(strict_types=1);

namespace App\Command;

use App\Repository\LadderEntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class LadderConcludeCurrentChallengeCommand extends Command
{
    protected static $defaultName = 'ladder:conclude-current-challenge';

    private LadderEntryRepository $ladderEntryRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(LadderEntryRepository $ladderEntryRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct(self::$defaultName);
        $this->ladderEntryRepository = $ladderEntryRepository;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this->setDescription('Conclude current ladder challenge, award best players and start a new challenge');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $now = date('Y-m-d');

        try {
            $players = $this->ladderEntryRepository->findWinningPlayersForMostGoalsScoredChallenge();

            foreach ($players as $player)
            {
                $playerAccount = $player->getPlayerAccount();
                $playerAccount->setSkins(['SKIN_GOLDEN_COVID_BALL', ...$playerAccount->getSkins()]);

                $this->entityManager->persist($player);
                $this->entityManager->flush();
            }
        } catch (Throwable $e) {
            $output->writeln("<fg=red>Ladder conclusion failed: {$e->getMessage()}</>");
            $this->log("[{$now}]: Ladder conclusion failed: {$e->getMessage()}");

            return Command::FAILURE;
        }

        $output->writeln('<fg=green>All players were awarded successfully</>');
        $this->log("[{$now}]: Players were awarded successfully");

        return Command::SUCCESS;
    }

    private function log(string $message): void
    {
        file_put_contents(dirname(__DIR__) . '/../log/ladder-log.txt', "\n{$message}", FILE_APPEND);
    }
}
