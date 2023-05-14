<?php

namespace App\Repository;

use App\Entity\Stock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Stock>
 *
 * @method Stock|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stock|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stock[]    findAll()
 * @method Stock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stock::class);
    }

    public function save(Stock $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Stock $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findGamesInTendencies(): array
    {
        return $this->createQueryBuilder('s')
            ->select('g.id', 'g.label', 'g.price', 'g.date_release')
            ->leftJoin('s.game', 'g')
            ->leftJoin('s.purchase', 'p')
            ->Where('s.is_available = false')
            ->groupBy('g.id', 'g.label', 'g.price', 'g.date_release')
            ->orderBy('COUNT(s.id)', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();
    }

    public function findStockByGameID($gameID): array
    {
        return $this->createQueryBuilder('s')
            ->select('g.label', 'COUNT(s.game) AS total')
            ->leftJoin('s.game', 'g')
            ->where('g.id = :gameID')
            ->andwhere('s.is_available = true')
            ->setParameter('gameID', $gameID)
            ->getQuery()
            ->getResult();
    }

    public function findStockGameByPlateform($gameID, $plateformID): array
    {
        return $this->createQueryBuilder('s')
            ->select('g.label', 'COUNT(s.game) AS total', 'p.label AS plateform', 'p.logo')
            ->leftJoin('s.game', 'g')
            ->leftJoin('s.plateform', 'p')
            ->where('g.id = :gameID')
            ->andWhere('p.id = :plateformID')
            ->andWhere('s.is_available = true')
            ->setParameter('gameID', $gameID)
            ->setParameter('plateformID', $plateformID)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Stock[] Returns an array of Stock objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Stock
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
