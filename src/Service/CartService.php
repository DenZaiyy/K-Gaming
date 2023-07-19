<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\Plateform;
use App\Entity\Stock;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService extends AbstractController
{
    public function __construct(private RequestStack $requestStack, private EntityManagerInterface $em)
    {
    }

	/*
	 * Méthode pour ajouter un jeu au panier grâce au slug du jeu et de la plateforme
	 */
    public function addToCart($gameSlug, $platformSlug): void
    {
        // On récupère le panier dans la session actuelle
        $cart = $this->getSession()->get('cart', []);
        // On récupère l'objet jeu
        $game = $this->em->getRepository(Game::class)->findOneBy(['slug' => $gameSlug]);
        // On récupère l'objet plateforme
        $platform = $this->em->getRepository(Plateform::class)->findOneBy(['slug' => $platformSlug]);
        // On récupère le stock disponible du jeu pour la plateforme
        $gameStock = $this->em->getRepository(Stock::class)->findAvailableGameStockByPlatform($game->getId(), $platform->getId());

        // Variable pour vérifier si le jeu est déjà dans le panier
        $found = null;
        // Variable pour définir le type de message flash
        $flashType = 'success';
        // Variable pour définir le message flash
        $flashMessage = 'Le jeu a bien été ajouté au panier !';

        // Vérification si le jeu est déjà dans le panier
        foreach ($cart as $key => $item) {
            // Si le jeu est déjà dans le panier, on récupère sa position dans le panier
            if ($item['game']->getId() == $game->getId() && $item['platform']->getId() == $platform->getId()) {
                $found = $key;
                break;
            }
        }

        // Si le jeu est déjà dans le panier, on vérifie si la quantité est inférieure au stock disponible
        if ($found !== null) {
            if($cart[$found]['quantity'] < $gameStock[0]['total']) {
                // Si la quantité est inférieure au stock disponible, on ajoute 1 à la quantité
                $cart[$found]['quantity']++;
            } else {
                // Sinon, on affiche un message d'erreur
                $flashType = 'danger';
                $flashMessage = 'Vous ne pouvez pas ajouter plus de jeux à votre panier';
                // Et on redirige vers la page du jeu
                $this->redirectToRoute('app_show_game', ['gameSlug' => $gameSlug]);
            }
        } else {
            // Si le jeu n'est pas dans le panier, on l'ajoute au panier
            $cart[] = [
                'game' => $game, // On ajoute le jeu
                'platform' => $platform, // On ajoute la plateforme
                'quantity' => 1 // On définit la quantité à 1 par défaut
            ];
        }

        // On enregistre le panier dans la session
        $this->getSession()->set('cart', $cart);
        // On affiche le message flash
        $this->addFlash($flashType, $flashMessage);
    }

	/*
	 * Méthode pour supprimer un jeu du panier
	 */
    public function removeToCart($gameSlug, $platformSlug): void
    {
        $cart = $this->getSession()->get('cart', []);

        foreach ($cart as $key => $item) {
            if ($item['game']->getSlug() === $gameSlug && $item['platform']->getSlug() === $platformSlug) {
                unset($cart[$key]);
            }
        }

        $this->getSession()->set('cart', $cart);
    }

	/*
	 * Méthode pour modifier la quantité d'un jeu dans le panier grâce à l'ID et la quantité choisie dans le select
	 */
    public function changeQtt($id, $qtt): void
    {
        $cart = $this->getSession()->get('cart', []);

        if (array_key_exists($id, $cart)) {
            $cart[$id]['quantity'] = $qtt;
        } else {
            unset($cart[$id]);
        }

        $this->getSession()->set('cart', $cart);
    }

	/*
	 * Méthode pour vider le panier
	 */
    public function removeCartAll()
    {
        return $this->getSession()->remove('cart');
    }

	/*
	 * Méthode pour récupérer sous un tableau les données du panier
	 */
    public function getTotal(): array
    {
        $cart = $this->getSession()->get('cart');
        $cartData = [];

        if ($cart) {
            foreach ($cart as $value) {
                $game = $this->em->getRepository(Game::class)->find($value['game']);
                $platform = $this->em->getRepository(Plateform::class)->find($value['platform']);

                if (!$game || !$platform) {
                    // Remove the product from the cart if it no longer exists
                    $this->removeToCart($value['game'], $value['platform']);
                }

                $cartData[] = [
                    'game' => $game,
                    'platform' => $platform,
                    'quantity' => $value['quantity']
                ];
            }
        }

        return $cartData;
    }

    //function to get the total of the cart
    public function getTotalCart(): float|int
    {
        $cart = $this->getSession()->get('cart');

        $total = 0;

        if ($cart) {
            foreach ($cart as $item) {
                $game = $this->em->getRepository(Game::class)->find($item['game']);

                $quantity = $item['quantity'];
                $price = $game->getPrice();

                $total += $quantity * $price;
            }
        }

        return $total;
    }

    //function to get the session
    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }
}
