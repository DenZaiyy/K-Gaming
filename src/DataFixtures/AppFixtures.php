<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Game;
use App\Entity\Genre;
use App\Entity\Plateform;
use App\Entity\User;
use App\Service\CallApiService;
use DateTime;
use DateTimeZone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(private readonly CallApiService $callApiService, private readonly SluggerInterface $slugger, private readonly PasswordHasherFactoryInterface $passwordHasherFactory)
    {
    }

    public static function getGroups(): array
    {
        return ['test'];
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);
        $user->setEmail('support@k-grischko.fr');
        $user->setUsername('admin');
        $user->setPassword($this->passwordHasherFactory->getPasswordHasher(User::class)->hash('admin'));
        $user->setIsVerified(true);
        $user->setIsBanned(false);
        $manager->persist($user);


        $categories = [
            [
                'label' => 'PC'
            ],
            [
                'label' => 'PlayStation'
            ],
            [
                'label' => 'Xbox'
            ]
        ];

        foreach ($categories as $category) {
            $product = new Category();
            $product->setLabel($category['label']);
            $manager->persist($product);
        }
        $manager->flush();

        $platforms = [
            [
                'label' => 'Steam',
                'logo' => 'https://www.svgrepo.com/show/452107/steam.svg',
                'category' => 'PC'
            ],
            [
                'label' => 'Origin',
                'logo' => 'https://www.svgrepo.com/show/354154/origin.svg',
                'category' => 'PC'
            ],
            [
                'label' => 'Battle.net',
                'logo' => 'https://eu.shop.battle.net/static/favicon-32x32.png',
                'category' => 'PC'
            ],
            [
                'label' => 'Epic Games',
                'logo' => 'https://www.svgrepo.com/show/341792/epic-games.svg',
                'category' => 'PC'
            ],
            [
                'label' => 'Ubisoft',
                'logo' => 'https://www.svgrepo.com/show/342320/ubisoft.svg',
                'category' => 'PC'
            ],
            [
                'label' => 'PlayStation 4',
                'logo' => 'https://www.svgrepo.com/show/473756/playstation4.svg',
                'category' => 'PlayStation'
            ],
            [
                'label' => 'PlayStation 5',
                'logo' => 'https://www.svgrepo.com/show/473757/playstation5.svg',
                'category' => 'PlayStation'
            ],
            [
                'label' => 'Xbox One',
                'logo' => 'https://www.svgrepo.com/show/303464/xbox-one-3-logo.svg',
                'category' => 'Xbox'
            ],
            [
                'label' => 'Xbox Series X/S',
                'logo' => 'https://upload.wikimedia.org/wikipedia/commons/a/af/Xbox_Series_X_logo.svg',
                'category' => 'Xbox'
            ],
        ];

        foreach ($platforms as $platform) {
            $product = new Plateform();
            $product->setLabel($platform['label']);
            $product->setSlug($this->slugger->slug($platform['label'])->lower());
            $product->setLogo($platform['logo']);
            $product->setCategory($manager->getRepository(Category::class)->findOneBy(['label' => $platform['category']]));
            $manager->persist($product);
        }
        $manager->flush();

        $genres = [
            [
                'label' => 'FPS',
                'image' => 'fps.webp'
            ],
            [
                'label' => 'Aventure',
                'image' => 'aventure.webp'
            ],
            [
                'label' => 'Simulation',
                'image' => 'simulation.webp'
            ],
            [
                'label' => 'Stratégie',
                'image' => 'strategie.webp'
            ],
            [
                'label' => 'MMO',
                'image' => 'mmorpg.webp'
            ],
            [
                'label' => 'Sport',
                'image' => 'sport.webp'
            ],
            [
                'label' => 'Action',
                'image' => 'action.webp'
            ], [
                'label' => 'PVP En ligne',
                'image' => 'pvp.webp'
            ],
            [
                'label' => 'Cross-Plateforme',
                'image' => 'cross-platform.webp'
            ],
            [
                'label' => 'Survie',
                'image' => 'survie.webp'
            ],
            [
                'label' => 'Multijoueur',
                'image' => 'multijoueur.webp'
            ],
            [
                'label' => 'Combat',
                'image' => 'combat.webp'
            ],
            [
                'label' => 'RPG',
                'image' => 'rpg.webp'
            ],
            [
                'label' => 'Moba',
                'image' => 'moba.webp'
            ],
            [
                'label' => 'Horreur',
                'image' => 'horreur.webp'
            ],
            [
                'label' => 'Course',
                'image' => 'course.webp'
            ],
            [
                'label' => 'Arcade',
                'image' => 'arcade.webp'
            ],
            [
                'label' => 'Rôle',
                'image' => 'role.webp'
            ],
        ];

        foreach ($genres as $genre) {
            $product = new Genre();
            $product->setLabel($genre['label']);
            $product->setSlug($this->slugger->slug($genre['label'])->lower());
            $product->setImage($genre['image']);
            $manager->persist($product);
        }
        $manager->flush();

        $igdbGames = $this->callApiService->getRandomsGames(30);

        foreach ($igdbGames as $game) {
            $product = new Game();
            $product->setLabel($game['name']);
            $product->setSlug($game['slug']);
            $product->setPrice(random_int(20, 60));
            $product->setDateRelease(new DateTime('now', new DateTimeZone('Europe/Paris')));
            $product->setIsPromotion(false);
            $product->setIsSellable(true);
            $product->addGenre($manager->getRepository(Genre::class)->findOneBy(['label' => $genres[random_int(0, count($genres) - 1)]['label']]));
            $product->addGenre($manager->getRepository(Genre::class)->findOneBy(['label' => $genres[random_int(0, count($genres) - 1)]['label']]));
            $product->addPlateform($manager->getRepository(Plateform::class)->findOneBy(['label' => $platforms[random_int(0, count($platforms) - 1)]['label']]));
            $manager->persist($product);
        }

        $manager->flush();
    }
}
