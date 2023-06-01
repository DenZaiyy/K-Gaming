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
		
	    $this->games = $manager->getRepository(Game::class)->findAll();
		$this->platforms = $manager->getRepository(Plateform::class)->findAll();
		
		for ($i = 0; $i <= 100; $i++) {
			$stock = new Stock();
			
			$stock->setGame($this->getRandomGame());
			$stock->setPlateform($this->getRandomPlatform());
			$stock->setLicenseKey($this->getRandomKey());
			$stock->setDateAvailability(new \DateTime());
			$stock->setIsAvailable(true);
			
			$manager->persist($stock);
		}
        $manager->flush();
    }
	
	private function getRandomGame()
	{
		return $this->games[rand(0, count($this->games) - 1)];
	}
	
	private function getRandomPlatform()
	{
		return $this->platforms[rand(0, count($this->platforms) - 1)];
	}
	
	private function getRandomKey()
	{
		$slugger = new AsciiSlugger();
		$uuid = $slugger->slug(strtoupper(bin2hex(random_bytes(8))));
        $uuidChunks = str_split($uuid, 4);
        $formattedUuid = implode('-', $uuidChunks);

        return $formattedUuid;
	}
}
