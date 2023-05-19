<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\Plateform;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService extends AbstractController
{
    public function __construct(private RequestStack $requestStack, private EntityManagerInterface $em)
    {
    }

    //FIXME: Faire en sorte d'ajouter une quantité si le produit est déjà dans le panier
    public function addToCart(int $id, $idPlatform): void
    {
        $cart = $this->getSession()->get('cart', []);

        $found = false;
        foreach ($cart as $item) {
            if ($item['game'] === $id && $item['platform'] === $idPlatform) {
                $item['quantity']++;
                $found = true;
            }
        }

        if (!$found) {
            $cart[] = [
                'game' => $id,
                'platform' => $idPlatform,
                'quantity' => 1
            ];
        }

        $this->getSession()->set('cart', $cart);
    }

    public function removeToCart(int $id): void
    {
        $cart = $this->getSession()->get('cart', []);

        foreach ($cart as $key => $item) {
            if ($item['game'] === $id) {
                unset($cart[$key]);
            }
        }

        $this->getSession()->set('cart', $cart);
    }

    public function increase(int $id): void
    {
        $cart = $this->getSession()->get('cart', []);

        if ($cart[$id]['quantity'] > 1) {
            $cart[$id]['quantity']++;
        } else {
            unset($cart[$id]);
        }

        $this->getSession()->set('cart', $cart);
    }

    public function decrease(int $id): void
    {
        $cart = $this->getSession()->get('cart', []);

        if ($cart[$id] > 1) {
            $cart[$id]--;
        } else {
            unset($cart[$id]);
        }

        $this->getSession()->set('cart', $cart);
    }

    public function removeCartAll()
    {
        return $this->getSession()->remove('cart');
    }

    public function getTotal(): array
    {
        $cart = $this->getSession()->get('cart');
        $cartData = [];

        if ($cart) {
            foreach ($cart as $key => $value) {
                $game = $this->em->getRepository(Game::class)->find($value['game']);
                $platform = $this->em->getRepository(Plateform::class)->find($value['platform']);

                if (!$game) {
                    // Remove the product from the cart if it no longer exists
                    $this->removeToCart($key);
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

    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }
}
