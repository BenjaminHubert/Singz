<?php
namespace Singz\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Singz\SocialBundle\Entity\Publication;
use Singz\CoreBundle\Entity\Project;

class LoadProjectData  extends AbstractFixture implements OrderedFixtureInterface
{
	private $nb = 20;
	
	public function load(ObjectManager $manager)
	{
		//Create a data faker
		$faker = \Faker\Factory::create();
		for($i=0; $i<$this->nb; $i++){
			$project = new Project();
			$project->setName($faker->text(50));
			$project->setDescription($faker->text);
			$project->setRequester($this->getReference('user '.rand(0, $this->nb-1)));
			$project->setState($faker->randomElement(array(
				Project::STATE_DELETED,
				Project::STATE_DONE,
				Project::STATE_VISIBLE
			)));
			
			$manager->persist($project);
			//keep the object
			$this->addReference('project '.$i, $project);
		}
		$manager->flush();
	}

	public function getOrder()
	{
		return 2;
	}
}