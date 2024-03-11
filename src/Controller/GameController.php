<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Genre;
use App\Entity\Plateform;
use App\Entity\Rating;
use App\Entity\Stock;
use App\Service\BreadCrumbsService;
use App\Service\CallApiService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GameController extends AbstractController
{
    /*
     * Constructeur permettant d'instancier le service "CallApiService" et permettant de l'utiliser dans les méthodes de la classe
     * @param CallApiService $callApiService
     */
    public function __construct (private readonly CallApiService $callApiService)
    {
    }

    /*
     * Méthode permettant d'afficher le detail d'un jeu grâce au slug
     */
    #[Route("/{_locale<%app.supported_locales%>}/{gameSlug}", name: "app_show_game", priority: -1)]
    public function showGame (EntityManagerInterface $em, $gameSlug, BreadCrumbsService $breadCrumbsService): Response
    {
        $game = $em->getRepository(Game::class)->findOneBy(["slug" => $gameSlug]);
        if (!$game) {
            $this->addFlash("danger", 'Le jeu n\'existe pas');
            return $this->redirectToRoute("app_404");
        }
        $gameStock = $em->getRepository(Stock::class)->findStockByGameID($game->getId());
        $gamePlatform = $em->getRepository(Game::class)->findOneGameInPlatform(
          $game->getId(), $gameStock[0]["platform_id"]
        );
        $platform = $em->getRepository(Plateform::class)->findOneBy(["id" => $gamePlatform["platform_id"]]);

        $ratings = $em->getRepository(Rating::class)->findBy(["game" => $game->getId()]);

        // TODO: Refactor this
        /*$breadcrumbs->addRouteItem($game->getLabel(), "app_show_game", ['gameSlug' => $gameSlug]);
            $breadcrumbs->prependRouteItem($gamePlatform['platform_label'], "platform_game", ['categoryLabel' => $platform->getCategory()->getLabel(),'platformSlug' => $gamePlatform['platform_slug']]);
            $breadcrumbs->prependRouteItem($platform->getCategory()->getLabel(), "platform_categories", ['categoryLabel' => $platform->getCategory()->getLabel()]);
            $breadcrumbs->prependRouteItem("Accueil", "app_home");*/

        //TODO: This is a temporary solution until we can refactor the breadcrumbs service
        $breadCrumbsService->BCGenerate(["label" => $platform->getCategory()->getLabel(),
          "route" => "platform_categories",
          "params" => ["categoryLabel" => strtolower($platform->getCategory()->getLabel()),],],
          ["label" => $gamePlatform["platform_label"],
            "route" => "platform_game",
            "params" => ["categoryLabel" => $platform->getCategory()->getLabel(),
              "platformSlug" => $gamePlatform["platform_slug"],],], ["label" => $game->getLabel(),
            "route" => "app_show_game",
            "params" => ["gameSlug" => $gameSlug],], []);

        $moyenne = 0;
        // Verification si le jeu a des notes pour calculer la moyenne
        if ($ratings) {
            $somme = 0;
            foreach ($ratings as $rate) {
                $somme += $rate->getNote();
            }

            $moyenne = $somme / count($ratings);
        }

        return $this->render("game/show.html.twig", ["game" => $game,
          "gameStock" => $gameStock[0],
          "gamePlatform" => $gamePlatform,
          "ratings" => $ratings,
          "moyenne" => $moyenne,
          "category" => strtolower($platform->getCategory()->getLabel()),
          "description" => "Retrouvez toutes les informations concernant le jeu " . $game->getLabel(
            ) . " sur K-Gaming.",]);
    }

    /*
     * Méthode permettant d'afficher le detail d'un jeu dans une plateforme grâce aux slugs
     */
    #[Route("/{_locale<%app.supported_locales%>}/platform/{categoryLabel}/{platformSlug}/{gameSlug}", name: "app_show_game_platform")]
    public function showGameInPlatform (
      EntityManagerInterface $em,
      $platformSlug,
      $gameSlug,
      BreadCrumbsService $breadCrumbsService
    ): Response {
        $game = $em->getRepository(Game::class)->findOneBy(["slug" => $gameSlug]);
        $platform = $em->getRepository(Plateform::class)->findOneBy(["slug" => $platformSlug]);
        $gamePlatform = $em->getRepository(Game::class)->findOneGameInPlatform($game->getId(), $platform->getId());
        $gameStock = $em->getRepository(Stock::class)->findAvailableGameStockByPlatform(
          $game->getId(), $platform->getId()
        );

        $ratings = $em->getRepository(Rating::class)->findBy(["game" => $game->getId()]);

        // TODO: Refactor this
        /*$breadcrumbs->addRouteItem($game->getLabel(), "app_show_game", ['gameSlug' => $gameSlug]);
            $breadcrumbs->prependRouteItem($gamePlatform['platform_label'], "platform_game", ['platformSlug' => $gamePlatform['platform_slug'], 'categoryLabel' => $platform->getCategory()->getLabel()]);
            $breadcrumbs->prependRouteItem($platform->getCategory()->getLabel(), "platform_categories", ['categoryLabel' => $platform->getCategory()->getLabel()]);
            $breadcrumbs->prependRouteItem("Accueil", "app_home");*/

        //TODO: This is a temporary solution until we can refactor the breadcrumbs service
        $breadCrumbsService->BCGenerate(["label" => $platform->getCategory()->getLabel(),
          "route" => "platform_categories",
          "params" => ["categoryLabel" => $platform->getCategory()->getLabel()],],
          ["label" => $gamePlatform["platform_label"],
            "route" => "platform_game",
            "params" => ["platformSlug" => $gamePlatform["platform_slug"],
              "categoryLabel" => $platform->getCategory()->getLabel(),],], ["label" => $game->getLabel(),
            "route" => "app_show_game",
            "params" => ["gameSlug" => $gameSlug],], []);

        $moyenne = 0;
        // Verification si le jeu a des notes pour calculer la moyenne
        if ($ratings) {
            $somme = 0;
            foreach ($ratings as $rate) {
                $somme += $rate->getNote();
            }

            $moyenne = $somme / count($ratings);
        }

        return $this->render("game/show.html.twig", ["game" => $game,
          "gamePlatform" => $gamePlatform,
          "gameStock" => $gameStock,
          "ratings" => $ratings,
          "moyenne" => $moyenne,
          "category" => strtolower($platform->getCategory()->getLabel()),
          "description" => "Retrouvez toutes les informations concernant le jeu " . $game->getLabel(
            ) . " sur K-Gaming.",]);
    }

    /*
     * Méthode permettant d'afficher la liste des jeux en précommande
     */
    #[Route(path: ["fr" => "/fr/jeux/precommande", "en" => "/en/preorder/game",], name: "app_show_preorders")]
    public function showGameInPreorder (EntityManagerInterface $em): Response
    {
        $date = new DateTime(); // Date du jour
        $gamePreorder = $em->getRepository(Game::class)->findGameInPreorder(
          $date
        ); // Jeux en précommande (date de sortie > date du jour)

        return $this->render("game/preOrder/index.html.twig", ["gamePreorder" => $gamePreorder,
          "description" => "Retrouvez toutes les informations concernant les jeux en précommande sur K-Gaming.",]);
    }

    /*
     * Méthode permettant d'afficher la liste des jeux associés à un genre grâce au slug
     */
    #[Route("/{_locale<%app.supported_locales%>}/game/genre/{genreSlug}", name: "app_show_game_genre")]
    public function showGameByGenre (
      EntityManagerInterface $em,
      Request $request,
      PaginatorInterface $paginator,
      $genreSlug,
      BreadCrumbsService $breadCrumbsService,
	    CallApiService $callApiService
    ): Response {
        $gamesDB = $em->getRepository(Game::class)->findGameByGenre(
          $genreSlug
        ); // Récupérer la liste des jeux associés à un genre
        $genre = $em->getRepository(Genre::class)->findOneBy(["slug" => $genreSlug]);

	    $games = [];

		$gamesLabel = [];

		foreach($gamesDB as $game) {
			$gamesLabel[] = $game->getLabel();
		}

		$gamesInfos = $callApiService->getInfosByGames($gamesLabel);
		//dd($gamesInfos);

	    foreach($gamesDB as $key => $game) {
		    $games[] = ["game" => $game, "coverID" => $gamesInfos[$key]["cover"]["image_id"]];
	    }

	    //dd($games);

        // Pagination KNPPaginator
        $pagination = $paginator->paginate(
          $games,
          $request->query->get("page", 1), 9
        );

        $pagination->setCustomParameters(["align" => "center",
          "size" => "small",
          "style" => "bottom",
          "span_class" => "whatever",]);
        // Fin pagination


	    //dd($pagination);

        /*$breadcrumbs->addRouteItem($genre->getLabel(), "app_show_game_genre", ['genreSlug' => $genreSlug]);
            $breadcrumbs->prependRouteItem("Genres", "genre_list");
            $breadcrumbs->prependRouteItem("Accueil", "app_home");*/

        $breadCrumbsService->BCGenerate([], [], [], ["label" => $genre->getLabel(),
          "route" => "app_show_game_genre",
          "params" => ["genreSlug" => $genreSlug],]);

        return $this->render("game/genre/show.html.twig", ["games" => $pagination,
          "gameAvailable" => $games,
          "genre" => $genre,
          "description" => "Retrouvez toutes les informations concernant les jeux du genre " . $genre->getLabel(
            ) . " sur K-Gaming.",]);
    }

    /*
     * Méthodes permettant de récupérer une partie des informations d'un jeu grâce à l'API
     */
    public function getImageIDGame ($gameLabel): Response
    {
        return $this->render(
          "components/game/_game_image_id.html.twig",
          ["gameInfos" => $this->callApiService->getCoverByGame($gameLabel),]
        );
    }

    public function getSummaryGame ($gameLabel): Response
    {
        return $this->render(
          "components/game/_game_summary.html.twig",
          ["gameInfos" => $this->callApiService->getSummaryByGame($gameLabel),]
        );
    }

    public function getScreenshotsGame ($gameLabel): Response
    {
        return $this->render(
          "components/game/_game_screenshots.html.twig",
          ["gameInfos" => $this->callApiService->getScreenshotByGame($gameLabel),]
        );
    }

    public function getVideosGame ($gameLabel): Response
    {
        return $this->render(
          "components/game/_game_videos.html.twig", ["gameInfos" => $this->callApiService->getVideosByGame($gameLabel),]
        );
    }
    // Fin méthodes API
}
