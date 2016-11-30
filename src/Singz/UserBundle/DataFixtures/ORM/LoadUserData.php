<?php
namespace Singz\UserBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
	/**
	 * @var ContainerInterface
	 */
	private $container; 
	private $nb = 2;
	
	public function load(ObjectManager $manager)
	{
		// Get our userManager, you must implement `ContainerAwareInterface`
		$userManager = $this->container->get('fos_user.user_manager');

		//Create a data faker
		$faker = \Faker\Factory::create();
		for($i=0; $i<$this->nb; $i++){
			// Create our user and set details
			$user = $userManager->createUser();
			$user->setUsername($faker->name);
			$user->setEmail($faker->email);
			$user->setPlainPassword($faker->password);
			$user->setEnabled(true);
			if($faker->boolean){
				$user->setRoles(['ROLE_SINGZER']);
			}else{
				$user->setRoles(['ROLE_STARZ']);
			}
			
			$user->setImage($this->getReference('image '.rand(0, $this->nb-1)));
			$user->setBiography($faker->text);
			
			// Update the user
			$userManager->updateUser($user, true);
			
			//keep the object
			$this->addReference('user '.$i, $user);
		}
	}
	
	public function getOrder()
	{
		return 1;
	}
	
	public function setContainer(ContainerInterface $container = null)
	{
		$this->container = $container;
	}
}