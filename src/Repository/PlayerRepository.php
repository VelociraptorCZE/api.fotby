<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Player;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Player|null find($id, $lockMode = null, $lockVersion = null)
 * @method Player|null findOneBy(array $criteria, array $orderBy = null)
 * @method Player[]    findAll()
 * @method Player[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Player::class);
    }

    public function isUsernameUnique(string $username): bool
    {
        $result = $this
            ->createQueryBuilder('player')
            ->select('player.id')
            ->andWhere('player.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getArrayResult();

        return count($result) === 0;
    }
}
