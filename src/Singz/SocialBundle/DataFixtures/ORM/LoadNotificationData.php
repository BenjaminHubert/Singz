<?php
namespace Singz\SocialBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Singz\SocialBundle\Entity\Notification;

class LoadNotificationData  extends AbstractFixture implements OrderedFixtureInterface
{
	private $nb = 2;

	public function load(ObjectManager $manager)
	{
		//Create a data faker
		$faker = \Faker\Factory::create();
		for($i=0; $i<$this->nb; $i++){
			// Create our video and set details
			$notification = new Notification();
			$notification->setDate($faker->dateTime);
			$notification->setPublication($this->getReference('publication '.rand(0, $this->nb-1)));
			$notification->setUser($this->getReference('user '.rand(0, $this->nb-1)));
			$notification->setIsRead($faker->boolean);
			$manager->persist($notification);
		}
		$manager->flush();
	}

	public function getOrder()
	{
		return 3;
	}
}