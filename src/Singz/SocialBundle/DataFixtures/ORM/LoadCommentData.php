<?php
namespace Singz\SocialBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Singz\SocialBundle\Entity\Comment;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadCommentData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
	private $nb = 20;

	public function load(ObjectManager $manager)
	{
		//First depth comments array
		$comments = [];
		//Create a data faker
		$faker = \Faker\Factory::create();
		//FOS Comment manager
		$commentManager = $this->container->get('fos_comment.manager.comment');
		//First depth comments creation
		for($i=0; $i<$this->nb; $i++){
			$comment = $commentManager->createComment($this->getReference('thread '.rand(0, $this->nb-1)));
			$comment->setAuthor($this->getReference('user '.rand(0, $this->nb-1)));
			$comment->setBody($faker->text(250));
			$commentManager->saveComment($comment);
			$comments[] = $comment;
		}
		$manager->flush();
		//Second depth comments creation
		foreach($comments as $firstComment){
			if($faker->boolean){
				$parent = $firstComment;
				$comment = $commentManager->createComment($parent->getThread(), $parent);
				$comment->setAuthor($this->getReference('user '.rand(0, $this->nb-1)));
				$comment->setBody($faker->text(250));
				$commentManager->saveComment($comment);
			}
		}
		$manager->flush();
	}

	public function getOrder()
	{
		return 4;
	}
	
	public function setContainer(ContainerInterface $container = null)
	{
		$this->container = $container;
	}
}

