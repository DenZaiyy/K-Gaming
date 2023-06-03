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

    public function addToCart($gameSlug, $platformSlug): void
    {
        $cart = $this->getSession()->get('cart', []);
        $game = $this->em->getRepository(Game::class)->findOneBy(['slug' => $gameSlug]);
        $platform = $this->em->getRepository(Plateform::class)->findOneBy(['slug' => $platformSlug]);

        $found = false;

        foreach ($cart as $key => $item) {
            if ($item['game'] == $game && $item['platform'] == $platform) {
                $found = $key;
                break;
            }
        }

        if ($found !== false) {
            $cart[$found]['quantity']++;
        } else {
            $cart[] = [
                'game' => $game,
                'platform' => $platform,
                'quantity' => 1
            ];
        }

        $this->getSession()->set('cart', $cart);
    }

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
