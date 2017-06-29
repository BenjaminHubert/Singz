<?php

namespace Singz\UserBundle\Controller;

use Singz\SocialBundle\Entity\Follow;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction(Request $request, $username)
    {
    	//Get entity manager
        $em = $this->getDoctrine()->getManager();
		//Get the requested user
        $user = $em->getRepository('SingzUserBundle:User')->getUser($username, true);
        if(!$user){
        	throw $this->createNotFoundException('Utilisateur non trouvé');
        }
		//Get user's followers
        $followers = $em->getRepository('SingzSocialBundle:Follow')->getRealLeads($user);
		//Get user's leaders
        $leaders = $em->getRepository('SingzSocialBundle:Follow')->getRealFollows($user);
		//Display view
        return $this->render('SingzUserBundle:Default:index.html.twig', array(
            'user' => $user,
            'followers' => $followers,
            'leaders' => $leaders,
        ));
    }

    public function followAction(Request $request){

        if($request->isXmlHttpRequest()) {
            $idUser = $request->get('idUser');
            $idLeader = $request->get('idLeader');

            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository('SingzSocialBundle:Follow');
            $follow = $repository->findBy(array('user' => $idUser, 'leader' => $idLeader));

            if (empty($follow)) {
                $didFollow = false;
                $user = $em->getRepository('SingzUserBundle:User');
                $leader = $em->getRepository('SingzUserBundle:User');

                //New follow
                $follow = new Follow();
                $follow->setLeader($leader);
                $follow->setFollower($user);
                if ($leader->isPrivate) {
                    $follow->setIsPending(1);
                }

                $em->persist($follow);
            } else {
                $didFollow = false;
                $em->remove(array_pop($follow));
            }
            $em->flush();

            return new JsonResponse(array('didFollow' => $didFollow));
        }

        return new Response('Error', 400);
    }
}
