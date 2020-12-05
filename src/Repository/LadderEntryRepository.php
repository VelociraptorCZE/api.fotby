<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\LadderEntry;
use App\Entity\Player;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;

/**
 * @method LadderEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method LadderEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method LadderEntry[]    findAll()
 * @method LadderEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LadderEntryRepository extends ServiceEntityRepository
{
    private PlayerRepository $playerRepository;

    public function __construct(ManagerRegistry $registry, PlayerRepository $playerRepository)
    {
        parent::__construct($registry, LadderEntry::class);
        $this->playerRepository = $playerRepository;
    }

    /** @return Player[] */
    public function findWinningPlayersForMostGoalsScoredChallenge(): array
    {
        $bestPlayers = $this->findBestPlayersForMostGoalsScoredChallenge();
        $minimumGoals = end($bestPlayers)['value'] ?? null;

        if ($minimumGoals === null) {
            throw new RuntimeException('There is no player in ladder');
        }

        $sql = 'select player_id from ladder_entry group by player_id having sum(goals_scored) >= :minimumGoals';
        $connection = $this->getEntityManager()->getConnection();

        $statement = $connection->prepare($sql);
        $statement->bindParam('minimumGoals', $minimumGoals);
        $statement->execute();

        $this->createQueryBuilder('ladderEntry')->delete()->getQuery()->execute();

        return $this->playerRepository->findBy([
            'id' => array_column($statement->fetchAllAssociative(), 'player_id')
        ]);
    }

    public function findBestPlayersForMostGoalsScoredChallenge(): array
    {
        $sql = <<<SQL
        select 
               sum(goals_scored) as value,
               p.username
        from ladder_entry
        left join player p on player_id = p.id
        group by player_id
        order by value desc
        limit 5
SQL;

        $connection = $this->getEntityManager()->getConnection();

        $statement = $connection->prepare($sql);
        $statement->execute();

        return $statement->fetchAllAssociative();
    }

    public function findLadderEntriesForMostGoalsScoredChallenge(int $playerId): array
    {
        return [
            'bestPlayers' => $this->findBestPlayersForMostGoalsScoredChallenge(),
            'objective' => 'Score as many goals as possible over any amount of matches',
            'playerEntry' => $this->findLadderPlayerPositionForMostGoalsScoredChallenge($playerId)
        ];
    }

    private function findLadderPlayerPositionForMostGoalsScoredChallenge(int $playerId): array
    {
        $sql = <<<SQL
        select
               sum(goals_scored) as value,
               p.username
        from ladder_entry
        left join player p on player_id = p.id
        where player_id = :playerId
SQL;

        $connection = $this->getEntityManager()->getConnection();

        $statement = $connection->prepare($sql);
        $statement->bindParam('playerId', $playerId);
        $statement->execute();

        return $statement->fetchAssociative();
    }
}
