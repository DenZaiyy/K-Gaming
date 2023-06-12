<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

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
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
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

    public function findGameInPreorder($date): array
    {
        return $this->createQueryBuilder('g')
            ->where('g.date_release > :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }

	public function findGameByGenre($genre): array
	{
		return $this->createQueryBuilder('g')
			->leftJoin('g.genres', 'gr')
			->where('gr.slug = :Genre')
			->setParameter('Genre', $genre)
			->getQuery()
			->getResult();
	}

	public function findGameByGenrePagination($genre)
	{
		return $this->createQueryBuilder('g')
			->leftJoin('g.genres', 'gr')
			->where('gr.slug = :Genre')
			->setParameter('Genre', $genre)
			->getQuery();
	}

	/**
	 * Récupère les jeux en fonction de la recherche
	 * @param SearchData $search
	 * @return PaginationInterface
	 */
    public function findSearch(SearchData $search): PaginationInterface
    {
		$query = $this->getSearchQuery($search)->getQuery();

		$pagination = $this->paginator->paginate(
			$query,
			$search->page,
			9
		);

	    $pagination->setCustomParameters([
		    'align' => 'center',
		    'size' => 'small',
		    'style' => 'bottom',
		    'span_class' => 'whatever',
	    ]);

	    return $pagination;
    }

	/**
	 * Récupère le prix minimum et maximum correspondant à une recherche
	 * @return int[]
	 */
	public function findMinMax(SearchData $search): array
	{
		$results = $this->getSearchQuery($search, true)
			->select('MIN(g.price) as min', 'MAX(g.price) as max')
			->getQuery()
			->getScalarResult();
		return [(int)$results[0]['min'], (int)$results[0]['max']];
	}

	/**
	 * Récupère les jeux en lien avec une recherche (SearchData)
	 */
	private function getSearchQuery(SearchData $search, $ignorePrice = false) : QueryBuilder
	{
		$query = $this
			->createQueryBuilder('g')
			->select('g', 'gr')
			->join('g.genres', 'gr');

		if(!empty($search->q)) {
			$query = $query
				->andWhere('g.label LIKE :q')
				->setParameter('q', "%{$search->q}%");
		}

		if(!empty($search->min) && $ignorePrice === false) {
			$query = $query
				->andWhere('g.price >= :min')
				->setParameter('min', $search->min);
		}

		if(!empty($search->max) && $ignorePrice === false) {
			$query = $query
				->andWhere('g.price <= :max')
				->setParameter('max', $search->max);
		}

		if(!empty($search->preorder)) {
			$date = new \DateTime();
			$query = $query
				->andWhere('g.date_release > :date')
				->setParameter('date', $date);
		}

		if(!empty($search->genres)) {
			$query = $query
				->andWhere('gr.id IN (:genres)')
				->setParameter('genres', $search->genres);
		}

		return $query;
	}

}
