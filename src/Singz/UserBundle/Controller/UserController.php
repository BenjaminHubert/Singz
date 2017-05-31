<?php

namespace Singz\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
    public function enabledAction($username, $state)
    {
        $em = $this->getDoctrine()->getManager();
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserBy(array('username' => $username));
        if($user == null) {
            throw $this->createNotFoundException('Utilisateur inexistant');
        }
        if($state == 'enabled'){
            $user->setEnabled(true);
        }elseif($state == 'disabled'){
            $user->setEnabled(false);
        }
        $em->flush();
        return $this->redirectToRoute('fos_user_security_logout');
    }
}
