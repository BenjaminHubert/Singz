<?php

namespace Singz\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CommentController extends Controller
{
    public function indexAction()
    {
        return $this->redirectToRoute('singz_admin_comment_list');
    }

    public function listAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	$allComments = $em->getRepository('SingzSocialBundle:Comment')->findAllCommentsInfo();
		$reported = $em->getRepository('SingzSocialBundle:Report')->findAll();
		$nbReports = [];
		foreach($allComments as $comment){
			$nbReports[$comment->getId()] = 0;
			foreach($reported as $report){
				if($report->getComment() == $comment){
					$nbReports[$comment->getId()]++;
				}
			}
		}
    	return $this->render('SingzAdminBundle:Comment:list.html.twig', array(
            'comments' => $allComments,
    		'nbReports' => $nbReports
        ));
    }
}
