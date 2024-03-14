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
    public function __construct(private readonly CallApiService $callApiService)
    {
    }

    /*
     * Méthode permettant d'afficher le detail d'un jeu grâce au slug
     */
    #[Route("/{_locale<%app.supported_locales%>}/{gameSlug}", name: "app_show_game", priority: -1)]
    public function showGame(EntityManagerInterface $em, $gameSlug, BreadCrumbsService $breadCrumbsService): Response
    {
        $game = $em->getRepository(Game::class)->findOneBy(["slug" => $gameSlug]);
        dd($game);

        if (!$game) {
            $this->addFlash("danger", 'Le jeu n\'existe pas');
            return $this->redirectToRoute("app_404");
        }

        $gameStock = $em->getRepository(Stock::class)->findStockByGameID($game->getId());
        $gamePlatform = $em->getRepository(Game::class)->findOneGameInPlatform($game->getId(), $gameStock[0]["platform_id"]);
        $platform = $em->getRepository(Plateform::class)->findOneBy(["id" => $gamePlatform["platform_id"]]);
        if (!$platform) {
            $this->addFlash("danger", 'La plateforme n\'existe pas');
            return $this->redirectToRoute("app_404");
        }
        $ratings = $em->getRepository(Rating::class)->findBy(["game" => $game->getId()]);

        //TODO: This is a temporary solution until we can refactor the breadcrumbs service
        $breadCrumbsService->BCGenerate([
            "label" => $platform->getCategory()->getLabel(),
            "route" => "platform_categories",
            "params" => ["categoryLabel" => strtolower($platform->getCategory()->getLabel())]
        ], [
            "label" => $gamePlatform["platform_label"],
            "route" => "platform_game",
            "params" => [
                "categoryLabel" => $platform->getCategory()->getLabel(),
                "platformSlug" => $gamePlatform["platform_slug"]
            ]
        ], [
            "label" => $game->getLabel(),
            "route" => "app_show_game",
            "params" => ["gameSlug" => $gameSlug]
        ], []);

        $average = 0;
        // Verification si le jeu a des notes pour calculer la moyenne
        if ($ratings) {
            $somme = 0;
            foreach ($ratings as $rate) {
                $somme += $rate->getNote();
            }

            $average = $somme / count($ratings);
        }

        return $this->render("game/show.html.twig", [
            "game" => $game,
            "gameStock" => $gameStock[0],
            "gamePlatform" => $gamePlatform,
            "ratings" => $ratings,
            "average" => $average,
            "category" => strtolower($platform->getCategory()->getLabel()),
            "description" => "Retrouvez toutes les informations concernant le jeu " . $game->getLabel() . " sur K-Gaming."
        ]);
    }

    /*
     * Méthode permettant d'afficher le detail d'un jeu dans une plateforme grâce aux slugs
     */
    #[Route("/{_locale<%app.supported_locales%>}/platform/{categoryLabel}/{platformSlug}/{gameSlug}", name: "app_show_game_platform")]
    public function showGameInPlatform(EntityManagerInterface $em, $platformSlug, $gameSlug, BreadCrumbsService $breadCrumbsService): Response
    {
//        $game = $em->getRepository(Game::class)->findOneBy(["slug" => $gameSlug]);
        $game = $this->callApiService->getInfosByGames(["gameSlug_Platform" => $gameSlug]);
//        dd($game);
        if (!$game) {
            $this->addFlash("danger", 'Le jeu n\'existe pas');
            return $this->redirectToRoute("app_404");
        }
        $platform = $em->getRepository(Plateform::class)->findOneBy(["slug" => $platformSlug]);
        if (!$platform) {
            $this->addFlash("danger", 'La plateforme n\'existe pas');
            return $this->redirectToRoute("app_404");
        }
        $gamePlatform = $em->getRepository(Game::class)->findOneGameInPlatform($game[0]['game']->getId(), $platform->getId());
        $gameStock = $em->getRepository(Stock::class)->findAvailableGameStockByPlatform($game[0]['game']->getId(), $platform->getId());

        $ratings = $em->getRepository(Rating::class)->findBy(["game" => $game[0]['game']->getId()]);

        //TODO: This is a temporary solution until we can refactor the breadcrumbs service
        $breadCrumbsService->BCGenerate(["label" => $platform->getCategory()->getLabel(),
            "route" => "platform_categories",
            "params" => ["categoryLabel" => $platform->getCategory()->getLabel()],],
            ["label" => $gamePlatform["platform_label"],
                "route" => "platform_game",
                "params" => ["platformSlug" => $gamePlatform["platform_slug"],
                    "categoryLabel" => $platform->getCategory()->getLabel(),],], ["label" => $game[0]['game']->getLabel(),
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

        return $this->render("game/show.html.twig", [
            "game" => $game,
            "gamePlatform" => $gamePlatform,
            "gameStock" => $gameStock,
            "ratings" => $ratings,
            "moyenne" => $moyenne,
            "category" => strtolower($platform->getCategory()->getLabel()),
            "description" => "Retrouvez toutes les informations concernant le jeu " . $game[0]['game']->getLabel() . " sur K-Gaming."
        ]);
    }

    /*
     * Méthode permettant d'afficher la liste des jeux en précommande
     */
    #[Route("/{_locale<%app.supported_locales%>}/preorder/games", name: "app_show_preorders")]
    public function showGameInPreorder(): Response
    {
        $date = new DateTime(); // Date du jour
        $gamePreorder = $this->callApiService->getInfosByGames(["preorders" => $date]);

        return $this->render("game/preOrder/index.html.twig", [
            "gamePreorder" => $gamePreorder,
            "description" => "Retrouvez toutes les informations concernant les jeux en précommande sur K-Gaming."
        ]);
    }

    /*
     * Méthode permettant d'afficher la liste des jeux associés à un genre grâce au slug
     */
    #[Route("/{_locale<%app.supported_locales%>}/game/gender/{genreSlug}", name: "app_show_game_genre")]
    public function showGameByGenre(EntityManagerInterface $em, Request $request, PaginatorInterface $paginator, $genreSlug, BreadCrumbsService $breadCrumbsService): Response
    {
        $genre = $em->getRepository(Genre::class)->findOneBy(["slug" => $genreSlug]);

        if (!$genre) {
            $this->addFlash("danger", 'Le genre n\'existe pas');
            return $this->redirectToRoute("app_404");
        }

        $games = $this->callApiService->getInfosByGames(["genre" => $genre->getSlug()]);

        // Pagination KNPPaginator
        $pagination = $paginator->paginate(
            $games, $request->query->get("page", 1), 6
        );

        $pagination->setCustomParameters(["align" => "center",
            "size" => "small",
            "style" => "bottom",
            "span_class" => "whatever",]);
        // Fin pagination

        $breadCrumbsService->BCGenerate([], [], [], ["label" => $genre->getLabel(),
            "route" => "app_show_game_genre",
            "params" => ["genreSlug" => $genre->getSlug()],]);

        return $this->render("game/genre/show.html.twig", [
            "games" => $pagination,
            "gameAvailable" => $games,
            "genre" => $genre,
            "description" => "Retrouvez toutes les informations concernant les jeux du genre " . $genre->getLabel() . " sur K-Gaming."
        ]);
    }

    public function getImageIDGame($gameLabel): Response
    {
        return $this->render(
            "components/game/_game_image_id.html.twig",
            ["gameInfos" => $this->callApiService->getCoverByGame($gameLabel),]
        );
    }
}
