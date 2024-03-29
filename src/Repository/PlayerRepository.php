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

    public function findPlayerByUsername(string $username): ?Player
    {
        return $this->findOneBy(['username' => $username]);
    }

    public function isPlayerDataUnique(string $username, string $email): bool
    {
        $result = $this
            ->createQueryBuilder('player')
            ->select('player.id')
            ->andWhere('player.username = :username')
            ->orWhere('player.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $email)
            ->getQuery()
            ->getArrayResult();

        return count($result) === 0;
    }
}
