<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Game;
use App\Entity\Plateform;
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

	/*
	 * Méthode pour récupérer les jeux en précommande
	 */
    public function findGamesInPreOrders($resultPerPage): array
    {
        return $this->createQueryBuilder('g')
            ->where('g.date_release > :date')
	        ->andWhere('g.is_sellable = true')
            ->setParameter('date', new \DateTime())
            ->setMaxResults($resultPerPage)
            ->getQuery()
            ->getResult();
    }

	/*
	 * Méthode pour récupérer les jeux en fonction de la plateforme
	 */
    public function findGamesInPlatform($platformID): array
    {
        return $this->createQueryBuilder('g')
            /* leftJoin permettant de joindre la table plateform à la table game
             grâce à la collection de plateform dans l'entité game */
            ->leftJoin('g.plateforms', 'p')
            /* where permettant de récupérer les jeux en fonction de la plateforme
             grâce au marqueur nommé/interrogative (ici :platformID) */
            ->where('p.id = :platformID')
	        ->andWhere('g.is_sellable = true')
            /* setParameter permettant de définir la valeur du marqueur nommé/interrogative
             en fonction de la variable $platformID */
            ->setParameter('platformID', $platformID)
            /* getQuery permettant l'exécution de la requête */
            ->getQuery()
            /* getResult permettant de récupérer le résultat de la requête sous forme de tableau */
            ->getResult();
    }

	/*
	 * Méthode pour récupérer le détail d'un jeu dans une plateforme
	 */
    public function findOneGameInPlatform($gameID, $platformID): array
    {
        return $this->createQueryBuilder('g')
            ->select('g.id AS game_id', 'g.label AS game_label', 'g.slug AS game_slug', 'g.price', 'g.old_price', 'g.is_promotion AS inPromotion', 'g.date_release', 'p.id AS platform_id', 'p.label as platform_label', 'p.slug AS platform_slug', 'p.logo as platform_logo')
            ->leftJoin('g.plateforms', 'p')
            ->where('p.id = :platformID')
	        ->andWhere('g.is_sellable = true')
            ->andWhere('g.id = :gameID')
            ->setParameters(
                [
                    'platformID' => $platformID,
                    'gameID' => $gameID
                ]
            )
            ->getQuery()
            ->getSingleResult()
            ;
    }

	/*
	 * Méthode pour récupérer les jeux en précommande en fonction de la date de sortie
	 */
    public function findGameInPreorder($date): array
    {
        return $this->createQueryBuilder('g')
            ->where('g.date_release > :date')
	        ->andWhere('g.is_sellable = true')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }

	/*
	 * Méthode pour récupérer les jeux en fonction du genre
	 */
	public function findGameByGenre($genre): array
	{
		return $this->createQueryBuilder('g')
			->leftJoin('g.genres', 'gr')
			->where('gr.slug = :Genre')
			->andWhere('g.is_sellable = true')
			->setParameter('Genre', $genre)
			->getQuery()
			->getResult();
	}

	/*
	 * Méthode pour récupérer les jeux en fonction du genre pour la pagination
	 */
	public function findGameByGenrePagination($genre)
	{
		return $this->createQueryBuilder('g')
			->leftJoin('g.genres', 'gr')
			->where('gr.slug = :Genre')
			->andWhere('g.is_sellable = true')
			->setParameter('Genre', $genre)
			->getQuery();
	}

	/**
	 * Récupère les jeux en fonction de la recherche
	 * @param SearchData $search
	 * @return PaginationInterface
	 */
    public function findSearch(SearchData $search, $platformID, $resultPerPage): PaginationInterface
    {
		$query = $this->getSearchQuery($search, $platformID)->getQuery();

		$pagination = $this->paginator->paginate(
			$query,
			$search->page,
            $resultPerPage
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
	public function findMinMax(SearchData $search, $platformID): array
	{
		$results = $this->getSearchQuery($search, $platformID,true)
			->select('MIN(g.price) as min', 'MAX(g.price) as max')
			->getQuery()
			->getScalarResult();
		return [(int)$results[0]['min'], (int)$results[0]['max']];
	}

	/**
	 * Récupère les jeux en lien avec une recherche (SearchData)
	 */
	private function getSearchQuery(SearchData $search, Plateform $platform, $ignorePrice = false) : QueryBuilder
	{
		$query = $this
			->createQueryBuilder('g')
			->select('g', 'gr')
			->join('g.genres', 'gr')
			->leftJoin('g.plateforms', 'p')
			->andWhere('p.id = :platform')
			->andWhere('g.is_sellable = true')
			->setParameter('platform', $platform)
			;


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
