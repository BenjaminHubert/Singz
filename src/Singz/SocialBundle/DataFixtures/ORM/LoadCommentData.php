<?php
namespace Singz\SocialBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Singz\SocialBundle\Entity\Comment;

class LoadCommentData extends AbstractFixture implements OrderedFixtureInterface
{
	private $nb = 20;

	public function load(ObjectManager $manager)
	{
		//Create a data faker
		$faker = \Faker\Factory::create();
		//First depth comments creation
		for($i=0; $i<$this->nb; $i++){
			$comment = new Comment();
			$comment->setAuthor($this->getReference('user '.rand(0, $this->nb-1)));
			$comment->setThread($this->getReference('publication '.rand(0, $this->nb-1))->getThread());
			$comment->setBody($faker->text(250));
			$comment->setState($faker->randomElement([
				Comment::STATE_VISIBLE,
				Comment::STATE_DELETED,
				Comment::STATE_SPAM,
				Comment::STATE_PENDING
			]));

			//Second depth comments creation
			if($faker->boolean){
				$child = new Comment();
				$child->setAuthor($this->getReference('user '.rand(0, $this->nb-1)));
				$child->setThread($comment->getThread());
				$child->setBody($faker->text(250));
				if($comment->getState() == Comment::STATE_VISIBLE){
					$child->setState($faker->randomElement([
						Comment::STATE_VISIBLE,
						Comment::STATE_DELETED,
						Comment::STATE_SPAM,
						Comment::STATE_PENDING
					]));
				}else{
					$child->setState(Comment::STATE_DELETED);
				}
				$child->setParent($comment);
				$manager->persist($child);
			}
			$manager->persist($comment);
		}
		$manager->flush();
	}

	public function getOrder()
	{
		return 4;
	}
}

