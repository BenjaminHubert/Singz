<?php
namespace Singz\SocialBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Singz\SocialBundle\Entity\Follow;

class LoadFollowData  extends AbstractFixture implements OrderedFixtureInterface
{
	private $nb = 20;
	
	public function load(ObjectManager $manager)
	{
		//Create a data faker
		$faker = \Faker\Factory::create();
		for($i=0; $i<$this->nb; $i++){
			$follow = new Follow();
			$follow->setLeader($this->getReference('user '.rand(0, $this->nb-1)));
			$follow->setFollower($this->getReference('user '.rand(0, $this->nb-1)));
			$follow->setIsPending($faker->boolean);
			$manager->persist($follow);
		}
		$manager->flush();
	}

	public function getOrder()
	{
		return 3;
	}
}