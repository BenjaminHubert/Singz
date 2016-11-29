<?php
namespace Singz\SocialBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Singz\UserBundle\Entity\Image;

class LoadImageData  extends AbstractFixture implements OrderedFixtureInterface
{
	private $nb = 2;

	public function load(ObjectManager $manager)
	{
		$uploadDir = 'web/uploads/userImage/';
		
		//Create a data faker
		$faker = \Faker\Factory::create();
		//Clear tmp/ 
		$files = glob($uploadDir.'*'); // get all file names
		foreach($files as $file){ // iterate files
			if(is_file($file))
				unlink($file); // delete file
		}
		// Fake data generation
		for($i=0; $i<$this->nb; $i++){
			// Create our video and set details
			$image = new Image();
			$imagePath = $faker->image($uploadDir, 640, 480, 'cats', false);
			while(empty($imagePath)){
				$imagePath = $faker->image($uploadDir, 640, 480, 'cats', false);
			}
			$image->setPath($imagePath);
			$manager->persist($image);
			//keep the object
			$this->addReference('image '.$i, $image);
		}
		$manager->flush();
	}

	public function getOrder()
	{
		return 0;
	}
}