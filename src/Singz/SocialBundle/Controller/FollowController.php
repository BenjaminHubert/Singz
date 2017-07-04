<?php

namespace Singz\SocialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Singz\SocialBundle\Entity\Follow;

class FollowController extends Controller
{
	/**
	 * @Security("has_role('ROLE_USER')")
	 */
	public function requestAction(Request $request, $leaderId){
    	//Get entity manager
        $em = $this->getDoctrine()->getManager();
		//Get the leader
		$leader = $em->getRepository('SingzUserBundle:User')->find($leaderId);
		if(!$leader){
			throw $this->createNotFoundException('Utilisateur non trouvé');
		}
		//Get the current user leaders
		$currentUserFollowers = $this->getUser()->getFollowers();
		//If unfollowed then follow else unfollow
		$followed = false;
		foreach($currentUserFollowers as $currentUserFollower){
			if($currentUserFollower->getLeader() == $leader){
				$followed = true;
				break;
			}
		}
		if($followed){
			// Remove follow
			$em->remove($currentUserFollower);
		}else{
			// Add follow
			$follow = new Follow();
			$follow->setFollower($this->getUser());
			$follow->setLeader($leader);
			if($leader->getIsPrivate()){
				$follow->setIsPending(true);
			}else{
				$follow->setIsPending(false);
			}
			$em->persist($follow);
		}
		$em->flush();
		return $this->redirectToRoute('singz_user_bundle_homepage', array(
			'username' => $leader->getUsername()
		));
	}

    public function pendingAction($status, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $follow = $em->getRepository('SingzSocialBundle:Follow')->find($id);
        if($follow == null) {
            throw $this->createNotFoundException('Demande de follow inexistante');
        }
        if($status == 'accept'){
            $follow->setIsPending(false);
        }elseif($status == 'deny'){
            $em->remove($follow);
        }
        $em->flush();
        return $this->redirectToRoute('singz_social_bundle_notification_list');
    }
}
