<?php
namespace Singz\SocialBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Singz\SocialBundle\Entity\Publication;

class LoadPublicationData  extends AbstractFixture implements OrderedFixtureInterface
{
	private $nb = 20;
	
	public function load(ObjectManager $manager)
	{
		//Create a data faker
		$faker = \Faker\Factory::create();
		for($i=0; $i<$this->nb; $i++){
			$publication = new Publication();
			$publication->setDescription($faker->text);
			$publication->setDate($faker->dateTimeBetween('-30 days', 'now'));
			$publication->setVideo($this->getReference('video '.rand(0, $this->nb-1)));
            $user = $this->getReference('user '.rand(0, $this->nb-1));
            $publication->setUser($user);
            $publication->setOwner($user);
			$manager->persist($publication);
			//keep the object
			$this->addReference('publication '.$i, $publication);
		}
		$manager->flush();
	}

	public function getOrder()
	{
		return 2;
	}
}