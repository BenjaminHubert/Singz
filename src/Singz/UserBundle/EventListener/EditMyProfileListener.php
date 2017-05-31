<?php 

namespace Singz\UserBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Listener responsible to change the redirection at the end of the password resetting
 */
class EditMyProfileListener implements EventSubscriberInterface
{
	private $router;

	public function __construct(UrlGeneratorInterface $router)
	{
		$this->router = $router;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents()
	{
		return array(
			FOSUserEvents::PROFILE_EDIT_SUCCESS => 'onEditProfileSuccess',
		);
	}

	public function onEditProfileSuccess(FormEvent $event)
	{
		$url = $this->router->generate('fos_user_profile_edit');

		$event->setResponse(new RedirectResponse($url));
	}
}