<?php
namespace Singz\SocialBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Singz\SocialBundle\Entity\Comment;

class LoadCommentData  extends AbstractFixture implements OrderedFixtureInterface
{
	private $nb = 2;

	public function load(ObjectManager $manager)
	{
		//Create a data faker
		$faker = \Faker\Factory::create();
		for($i=0; $i<$this->nb; $i++){
			// Create our video and set details
			$comment = new Comment();
			$comment->setDate($faker->dateTime);
			$comment->setPublication($this->getReference('publication '.rand(0, $this->nb-1)));
			$comment->setUser($this->getReference('user '.rand(0, $this->nb-1)));
			$manager->persist($comment);
		}
		$manager->flush();
	}

	public function getOrder()
	{
		return 3;
	}
}