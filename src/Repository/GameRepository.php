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
            ->select('g.id', 'g.label', 'g.slug', 'g.price', 'g.date_release')
            ->where('g.date_release > :date')
            ->setParameter('date', new \DateTime())
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }

    public function findGamesInPlatformPagination($plateformID)
    {
        return $this->createQueryBuilder('g')
            ->leftJoin('g.plateforms', 'p')
            ->where('p.id = :plateformID')
            ->setParameter('plateformID', $plateformID)
            ->getQuery();
    }

    public function findGamesInPlatform($platformID): array
    {
        return $this->createQueryBuilder('g')
            ->leftJoin('g.plateforms', 'p')
            ->where('p.id = :platformID')
            ->setParameter('platformID', $platformID)
            ->getQuery()
            ->getResult();
    }

    public function findOneGameInPlatform($gameID, $platformID): array
    {
        return $this->createQueryBuilder('g')
            ->select('g.id AS game_id', 'g.label AS game_label', 'g.slug AS game_slug', 'g.price', 'g.date_release', 'p.id AS platform_id', 'p.label as platform_label', 'p.slug AS platform_slug', 'p.logo as platform_logo')
            ->leftJoin('g.plateforms', 'p')
            ->where('p.id = :platformID')
            ->andWhere('g.id = :gameID')
            ->setParameters(
                [
                    'platformID' => $platformID,
                    'gameID' => $gameID
                ]
            )
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
