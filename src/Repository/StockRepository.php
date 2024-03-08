<?php

namespace App\Repository;

use App\Entity\Stock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Stock>
 * @method Stock|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stock|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stock[]    findAll()
 * @method Stock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockRepository extends ServiceEntityRepository
{
    public function __construct (ManagerRegistry $registry)
    {
        parent::__construct($registry, Stock::class);
    }

    public function save (Stock $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove (Stock $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Méthode pour récupérer la liste des jeux en tendance
     */
    public function findGamesInTendencies ($resultPerPage): array
    {
        return $this->createQueryBuilder("s")->select(
          "g.id", "g.label", "g.slug", "g.price", "g.is_promotion", "g.old_price", "g.promo_percent", "g.date_release"
        )->leftJoin("s.game", "g")->leftJoin("s.purchase", "p")->Where("s.is_available = false")->andWhere(
          "s.purchase IS NOT NULL"
        )->andWhere("g.is_sellable = true")->groupBy("g.id", "g.label", "g.slug", "g.price", "g.date_release")->orderBy(
          "COUNT(s.id)", "DESC"
        )->setMaxResults($resultPerPage)->getQuery()->getResult();
    }

    /**
     * Méthode pour récupérer la liste des stocks disponibles pour un jeu
     */
    public function findStockByGameID ($gameID): array
    {
        return $this->createQueryBuilder("s")->select(
          "p.id AS platform_id", "g.id AS game_id", "g.label", "COUNT(s.game) AS total", "g.promo_percent",
          "p.label AS platform_label", "p.logo"
        )->leftJoin("s.game", "g")->leftJoin("s.plateform", "p")->where("g.id = :gameID")->andwhere(
          "s.is_available = true"
        )->andWhere("g.is_sellable = true")->groupBy("g.label", "p.label", "p.logo", "p.id")->setParameter(
          "gameID", $gameID
        )->getQuery()->getResult();
    }

    /*
     * Méthode pour récupérer la liste des stocks disponibles pour un jeu associé a une plateforme
     */
    public function findAvailableGameStockByPlatform ($gameID, $platformID): array
    {
        return $this->createQueryBuilder("s") // crée une instance de QueryBuilder pour la table stock et l'alias "s"
        // select permet de sélectionner les colonnes que l'on souhaite récupérer
        ->select(
          "p.id AS platform_id", // permet de récupérer l'ID de la plateforme
          "g.id AS game_id", // permet de récupérer l'ID du jeu
          "g.label", // permet de récupérer le label du jeu
          "g.slug", // permet de récupérer le slug du jeu
          "p.label AS platform_label", // permet de récupérer le label de la plateforme
          "p.slug AS platform_slug", // permet de récupérer le slug de la plateforme
          "COUNT(s.game) AS total" // permet de compter le nombre de stock disponible
        )
          // "g" est l'alias de la table game
          ->leftJoin("s.game", "g")
          // "p" est l'alias de la table plateform
          ->leftJoin("s.plateform", "p")
          // condition permettant de récupérer le jeu en fonction de son ID
          ->where("g.id = :gameID")
          // condition permettant de récupérer la plateforme en fonction de son ID
          ->andWhere("p.id = :platformID")
          // condition permettant de récupérer les stocks disponibles
          ->andWhere("s.is_available = true")
          // condition permettant de vérifier que le jeu est vendable
          ->andWhere("g.is_sellable = true")
          // setParameters permet de définir les paramètres de la requête en fonction des variables passées en paramètre de la méthode
          ->setParameters(["gameID" => $gameID, // permet de définir la valeur de l'ID du jeu
            "platformID" => $platformID, // permet de définir la valeur de l'ID de la plateforme
          ])->getQuery() // getQuery permet de récupérer la requête
          ->getSingleResult(); // getSingleResult permet de récupérer un seul résultat
    }

    /**
     * Méthode pour récupérer la liste des stocks disponibles pour un jeu associé a une plateforme et selon la quantité demandé par l'utilisateur
     */
    public function findLicenseKeyAvailableByGamesAndPlatform ($gameID, $platformID, $quantity): array
    {
        // createQueryBuilder permet de créer une instance de QueryBuilder pour la table stock et l'alias "s"
        return $this->createQueryBuilder("s")
          // leftJoin permet de faire une jointure avec la table game et l'alias "g"
          ->leftJoin("s.game", "g")
          // leftJoin permet de faire une jointure avec la table plateform et l'alias "p"
          ->leftJoin("s.plateform", "p")
          // condition permettant de récupérer le jeu en fonction de son ID grâce au marqueur nommée ":gameID"
          ->where("g.id = :gameID")/* condition permettant de récupérer la plateforme
           en fonction de son ID grâce au marqueur nommée ":platformID" */ ->andWhere("p.id = :platformID")
          // condition permettant de récupérer les stocks disponibles
          ->andWhere("s.is_available = true")->andWhere("g.is_sellable = true")
          // setMaxResults permet de définir le nombre de résultats à récupérer
          ->setMaxResults($quantity)/*  setParameters permet de définir les paramètres de la requête
           en fonction des variables passées en paramètre de la méthode */ ->setParameters(["gameID" => $gameID,
            "platformID" => $platformID,])
          // getQuery permet de récupérer la requête avec les conditions définies précédemment
          ->getQuery()
          // getResult permet de récupérer les résultats en format tableau
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
