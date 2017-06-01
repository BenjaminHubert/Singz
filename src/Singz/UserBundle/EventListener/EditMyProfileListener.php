<?php 

namespace Singz\UserBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Doctrine\ORM\EntityManager;

/**
 * Listener responsible to change the redirection at the end of the password resetting
 */
class EditMyProfileListener implements EventSubscriberInterface
{
	private $router;
	private $em;

	public function __construct(UrlGeneratorInterface $router, EntityManager $em)
	{
		$this->router = $router;
		$this->em = $em;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents()
	{
		return array(
			FOSUserEvents::PROFILE_EDIT_SUCCESS => 'onEditProfileSuccess',
			FOSUserEvents::PROFILE_EDIT_COMPLETED => 'onEditProfileCompleted',
		);
	}

	public function onEditProfileSuccess(FormEvent $event)
	{
		// Redirect to the same page
		$url = $this->router->generate('fos_user_profile_edit');
		$event->setResponse(new RedirectResponse($url));
	}
	
	public function onEditProfileCompleted(FilterUserResponseEvent $args)
	{
		// Get the Image entity
		$image = $args->getUser()->getImage();
		$file = $image->getFile();
		if(!$file){
			return;
		}
		// Define the filename
		$fileName = $image->getId().'.'.$file->guessExtension();
		// Move the file to the directory where brochures are stored
		$file->move(
			$image->getUploadRootDir(),
			$fileName
		);
		// Update the path
		$image->setPath($fileName);
		// Update the database
		$this->em->flush();
		
	}
}