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
		//Adding fixtures
// 		for($i=0; $i<$this->nb; $i++){
// 			$thread = new Thread();
// 			$thread->setPublication($this->getReference('publication '.rand(0, $this->nb-1)));
			
// 			$this->addReference('thread '.$i, $thread);
// 			$manager->persist($thread);
// 		}
// 		$manager->flush();
	}

	public function getOrder()
	{
		return 3;
	}
}