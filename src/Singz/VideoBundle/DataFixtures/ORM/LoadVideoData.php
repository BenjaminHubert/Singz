<?php
namespace Singz\SocialBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Singz\VideoBundle\Entity\Video;

class LoadVideoData  extends AbstractFixture implements OrderedFixtureInterface
{
	private $nb = 20;

	public function load(ObjectManager $manager)
	{
		//Create a data faker
		$faker = \Faker\Factory::create();
		for($i=0; $i<$this->nb; $i++){
			// Create our video and set details
			$video = new Video();
			$video->setExtension('mp4');
			$manager->persist($video);
			//keep the object
			$this->addReference('video '.$i, $video);
		}
		$manager->flush();
	}

	public function getOrder()
	{
		return 1;
	}
}