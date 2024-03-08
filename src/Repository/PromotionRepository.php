<?php

namespace App\Repository;

use App\Entity\Promotion;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Promotion>
 * @method Promotion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Promotion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Promotion[]    findAll()
 * @method Promotion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromotionRepository extends ServiceEntityRepository
{
    public function __construct (ManagerRegistry $registry)
    {
        parent::__construct($registry, Promotion::class);
    }

    public function findExpiredCoupon (): array
    {
        return $this->createQueryBuilder("p")->where("p.end_at < :date")->setParameter(
          "date", new DateTimeImmutable("now", new DateTimeZone("Europe/Paris"))
        )->getQuery()->getResult();
    }

    public function findExpiredCouponCount (): int
    {
        return $this->createQueryBuilder("p")->select("COUNT(p.id)")->where("p.end_at < :date")->setParameter(
          "date", new DateTimeImmutable("now", new DateTimeZone("Europe/Paris"))
        )->getQuery()->getSingleScalarResult();
    }

    public function findActiveCoupon (): array
    {
        return $this->createQueryBuilder("p")->where("p.start_at < :date")->andWhere("p.end_at > :date")->setParameter(
          "date", new DateTimeImmutable("now", new DateTimeZone("Europe/Paris"))
        )->getQuery()->getResult();
    }

    //    /**
    //     * @return Promotion[] Returns an array of Promotion objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Promotion
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
