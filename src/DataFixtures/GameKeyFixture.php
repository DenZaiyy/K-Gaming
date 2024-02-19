<?php

namespace App\DataFixtures;

use App\Entity\Game;
use App\Entity\Plateform;
use App\Entity\Stock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\AsciiSlugger;

class GameKeyFixture extends Fixture
{
    private $games;
    private $platforms;

    public function load(ObjectManager $manager): void
    {
        // Fetch existing games and platforms from the database
        $this->games = $manager->getRepository(Game::class)->findAll();
        $this->platforms = $manager->getRepository(Plateform::class)->findAll();


        // Generate game keys and associate them with games and platforms
        foreach ($this->games as $game) {
            $platforms = $game->getPlateforms()->toArray();
            if (empty($platforms)) {
                continue; // Skip games without any associated platforms
            }

            foreach ($platforms as $platform) {
                for ($i = 0; $i < rand(1, 25); $i++) {
                    $stock = new Stock();

                    $stock->setGame($game);
                    $stock->setPlateform($platform);
                    $stock->setLicenseKey($this->getRandomKey());
                    $stock->setDateAvailability(new \DateTime());
                    $stock->setIsAvailable(true);

                    $manager->persist($stock);
                }
            }
        }

        $manager->flush();
    }

    private function getRandomKey(): string
    {
        $slugger = new AsciiSlugger();
        $uuid = $slugger->slug(strtoupper(bin2hex(random_bytes(8))));
        $uuidChunks = str_split($uuid, 4);

        return implode('-', $uuidChunks);
    }
}
