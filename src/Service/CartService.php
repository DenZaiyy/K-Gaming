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
        //FIXME: Faire en sorte d'ajouter une quantité si le produit est déjà dans le panier et le mettre à jour sur le rendu
        foreach ($cart as $item) {
            if ($item['game'] == $id && $item['platform'] == $idPlatform) {
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

    public function removeToCart(int $id, int $idPlatform): void
    {
        $cart = $this->getSession()->get('cart', []);

        foreach ($cart as $key => $item) {
            if ($item['game'] === $id && $item['platform'] === $idPlatform) {
                unset($cart[$key]);
            }
        }

        $this->getSession()->set('cart', $cart);
    }

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

    public function getTotalCart(): float|int
    {
        $cart = $this->getSession()->get('cart');

        $total = 0;

        if ($cart)
        {
            foreach ($cart as $item)
            {
                $game = $this->em->getRepository(Game::class)->find($item['game']);

                $quantity = $item['quantity'];
                $price = $game->getPrice();

                $total += $quantity * $price;
            }
        }

        return $total;
    }

    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }
}
