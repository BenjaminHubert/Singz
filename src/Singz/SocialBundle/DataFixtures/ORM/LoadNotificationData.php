<?php
namespace Singz\SocialBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Singz\SocialBundle\Entity\Notification;

class LoadNotificationData  extends AbstractFixture implements OrderedFixtureInterface
{
	private $nb = 20;

	public function load(ObjectManager $manager)
	{
		//Create a data faker
// 		$faker = \Faker\Factory::create();
// 		for($i=0; $i<$this->nb; $i++){
// 			$notification = new Notification();
// 			$notification->setDate($faker->dateTime);
// 			$notification->setPublication($this->getReference('publication '.rand(0, $this->nb-1)));
// 			$notification->setUserFrom($this->getReference('user '.rand(0, $this->nb-1)));
// 			$notification->setUserTo($this->getReference('publication '.rand(0, $this->nb-1))->getUser());
// 			$notification->setIsRead($faker->boolean);
// 			$notification->setMessage($faker->text(250));
// 			$manager->persist($notification);
// 		}
// 		$manager->flush();
	}

	public function getOrder()
	{
		return 3;
	}
}