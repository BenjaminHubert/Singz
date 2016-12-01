<?php
namespace Singz\SocialBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Singz\SocialBundle\Entity\Thread;

class LoadThreadData  extends AbstractFixture implements OrderedFixtureInterface
{
	private $nb = 20;
	
	public function load(ObjectManager $manager)
	{
		//Create a data faker
		$faker = \Faker\Factory::create();
		//Allow to avoid double id entry
		$IdAlreadyUsed = array();
		
		for($i=0; $i<$this->nb; $i++){
			// Create our video and set details
			$thread = new Thread();
			$id = $this->randWithout(0, $this->nb-1, $IdAlreadyUsed);
			$IdAlreadyUsed[] = $id;
			$thread->setId($this->getReference('publication '.$id)->getId());
			$thread->setPermalink('http://singz.local/publication/show/'.$thread->getId());
			$thread->setCommentable($faker->boolean);
			$thread->setNumComments(0);
			$thread->setLastCommentAt(NULL);
			$manager->persist($thread);
		}
		$manager->flush();
	}
	
	private function randWithout($from, $to, array $exceptions) {
		sort($exceptions); // lets us use break; in the foreach reliably
		$number = rand($from, $to - count($exceptions)); // or mt_rand()
		foreach ($exceptions as $exception) {
			if ($number >= $exception) {
				$number++; // make up for the gap
			} else /*if ($number < $exception)*/ {
				break;
			}
		}
		return $number;
	}

	public function getOrder()
	{
		return 3;
	}
}