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
			->select('g.id', 'g.label', 'g.slug', 'g.price', 'g.date_release')
			->leftJoin('s.game', 'g')
			->leftJoin('s.purchase', 'p')
			->Where('s.is_available = false')
			->andWhere('s.purchase IS NOT NULL')
			->groupBy('g.id', 'g.label', 'g.price', 'g.date_release')
			->orderBy('COUNT(s.id)', 'DESC')
			->setMaxResults(3)
			->getQuery()
			->getResult();
	}
	
	public function findStockByGameID($gameID): array
	{
		return $this->createQueryBuilder('s')
			->select('p.id AS platform_id', 'g.id AS game_id', 'g.label', 'COUNT(s.game) AS total', 'p.label AS platform_label', 'p.logo')
			->leftJoin('s.game', 'g')
			->leftJoin('s.plateform', 'p')
			->where('g.id = :gameID')
			->andwhere('s.is_available = true')
			->groupBy('g.label', 'p.label', 'p.logo', 'p.id')
			->setParameter('gameID', $gameID)
			->getQuery()
			->getResult();
	}
	
	public function findAvailableGameStockByPlatform($gameID, $platformID): array
	{
		return $this->createQueryBuilder('s')
			->select('p.id AS platform_id', 'g.id AS game_id', 'g.label', 'g.slug', 'p.label AS platform_label', 'p.slug AS platform_slug', 'COUNT(s.game) AS total')
			->leftJoin('s.game', 'g')
			->leftJoin('s.plateform', 'p')
			->where('g.id = :gameID')
			->andWhere('p.id = :platformID')
			->andWhere('s.is_available = true')
			->setParameters([
				'gameID' => $gameID,
				'platformID' => $platformID
			])
			->getQuery()
			->getResult();
	}
	
	public function findLicenseKeyAvailableByGamesAndPlatform($gameID, $platformID, $quantity): array
	{
		return $this->createQueryBuilder('s')
			->leftJoin('s.game', 'g')
			->leftJoin('s.plateform', 'p')
			->where('g.id = :gameID')
			->andWhere('p.id = :platformID')
			->andWhere('s.is_available = true')
			->setMaxResults($quantity)
			->setParameters([
				'gameID' => $gameID,
				'platformID' => $platformID
			])
			->getQuery()
			->getResult();
	}

    public function deleteStockGameIfNotInPlatform(): array
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.game', 'g')
            ->leftJoin('s.plateform', 'p')
            ->where('g.plateforms != p.id')
            ->delete()
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
