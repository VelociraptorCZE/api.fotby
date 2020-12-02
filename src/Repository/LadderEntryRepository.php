<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\LadderEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LadderEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method LadderEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method LadderEntry[]    findAll()
 * @method LadderEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LadderEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LadderEntry::class);
    }

    public function findLadderEntriesForMostGoalsScoredChallenge(int $playerId): array
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

        return [
            'bestPlayers' => $statement->fetchAllAssociative(),
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
