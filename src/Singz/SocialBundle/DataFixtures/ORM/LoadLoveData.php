<?php
namespace Singz\SocialBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Singz\SocialBundle\Entity\Love;

class LoadLoveData  extends AbstractFixture implements OrderedFixtureInterface
{
	private $nb = 20;

	public function load(ObjectManager $manager)
	{
		//Create a data faker
		$faker = \Faker\Factory::create();
		for($i=0; $i<100; $i++){
			$love = new Love();
			$love->setDate($faker->dateTimeBetween('-30 days', 'now'));
			$love->setPublication($this->getReference('publication '.rand(0, $this->nb-1)));
			$love->setUser($this->getReference('user '.rand(0, $this->nb-1)));
			$manager->persist($love);
		}
		$manager->flush();
	}

	public function getOrder()
	{
		return 3;
	}
}