<?php
namespace Singz\SocialBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Singz\SocialBundle\Entity\Thread;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadThreadData  extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
	/**
	 * @var ContainerInterface
	 */
	private $container; 
	private $nb = 20;
	
	public function load(ObjectManager $manager)
	{
		/*//Getting the thread manager
		$threadManager = $this->container->get('fos_comment.manager.thread');
		//Adding fixtures
		for($i=0; $i<$this->nb; $i++){
			$id = $this->getReference('publication '.$i)->getId();
			$thread = $threadManager->findThreadById($id);
			if (null === $thread) {
				$thread = $threadManager->createThread();
				$thread->setId($id);
				$thread->setPermalink('http://singz.local/publication/'.$id);
				// Add the thread
				$threadManager->saveThread($thread);
			}			
			$this->setReference('thread '.$i, $thread);
		}
		$manager->flush();*/
	}

	public function getOrder()
	{
		return 3;
	}
	
	public function setContainer(ContainerInterface $container = null)
	{
		$this->container = $container;
	}
}