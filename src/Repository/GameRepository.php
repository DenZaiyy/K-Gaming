<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Game>
 *
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function save(Game $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Game $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findGamesInPreOrders(): array
    {
        return $this->createQueryBuilder('g')
            ->select('g.id', 'g.label', 'g.price', 'g.date_release')
            ->where('g.date_release > :date')
            ->setParameter('date', new \DateTime())
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }

    public function findOneGameInPlatforms($gameID, $plateformID): array
    {
        return $this->createQueryBuilder('g')
            ->select('g.id', 'g.label', 'g.price', 'g.date_release')
            ->leftJoin('game_plateform', 'gp', 'ON', 'g.id = gp.game_id')
            ->leftJoin('plateform', 'p', 'ON', 'p.id = gp.plateform_id')
            ->where('gp.plateform_id = :plateformID')
            ->andWhere('gp.game_id = :gameID')
            ->setParameter('plateformID', $plateformID)
            ->setParameter('gameID', $gameID)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Game[] Returns an array of Game objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('g.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Game
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
