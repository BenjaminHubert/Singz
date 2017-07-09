<?php

namespace Singz\AdminBundle\Controller;

use Faker\Provider\ka_GE\DateTime;
use Singz\CoreBundle\Entity\Project;
use Singz\SocialBundle\Entity\Publication;
use Singz\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    public function dashboardAction()
    {
        $em = $this->getDoctrine()->getManager();
        $userService = $this->get('singz.user.service.role');

        // Get publications
        $publications = $em->getRepository('SingzSocialBundle:Publication')->findBy(array('state' => Publication::STATE_VISIBLE));
        if(!empty($publications))
            $publications = count($publications);
        // Get resingz
        $resingz = $em->getRepository('SingzSocialBundle:Publication')->findBy(array('state' => Publication::STATE_VISIBLE, 'isResingz' => true));
        $resingz = count($resingz);
        // Get comments
        $comments = $em->getRepository('SingzSocialBundle:Comment')->findAll();
        $comments = count($comments);
        // Get loves
        $loves = $em->getRepository('SingzSocialBundle:Love')->findAll();
        $loves = count($loves);
        // Get projects
        $projects = $em->getRepository('SingzCoreBundle:Project')->findAll();
        $projects = count($projects);
        // Get current projects
        $currentProjects = $em->getRepository('SingzCoreBundle:Project')->findBy(array('state' => Project::STATE_VISIBLE));
        $currentProjects = count($currentProjects);
        // Get done projects
        $doneProjects = $em->getRepository('SingzCoreBundle:Project')->findBy(array('state' => Project::STATE_DONE));
        $doneProjects = count($doneProjects);
        // Get users
        $starz = 0;
        $singzers = 0;
        $users = $em->getRepository('SingzUserBundle:User')->findAll();
        foreach ($users as $user) {
            if($userService->isGranted(User::ROLE_STARZ, $user)){
                $starz++;
            }
            elseif($userService->isGranted(User::ROLE_SINGZER, $user)) {
                $singzers++;
            }
        }
        $users = count($users);

        // Get last week's publications, comments and loves
        $lastweek = new \DateTime('-1 week');
        $graphPub = $em->getRepository('SingzSocialBundle:Publication')->getLastWeekPublications($lastweek);
        $graphCom = $em->getRepository('SingzSocialBundle:Comment')->getLastWeekComments($lastweek);
        $graphLov = $em->getRepository('SingzSocialBundle:Love')->getLastWeekLoves($lastweek);

        // Get last week's days
        $dates = [];
        $now = new \DateTime( 'now' );
        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($lastweek, $interval, $now);
        foreach ($period as $dt)
            $dates[] = $dt->format("Y-m-d");

        return $this->render('SingzAdminBundle:Admin:dashboard.html.twig', array(
            'publications' => $publications,
            'resingz' => $resingz,
            'comments' => $comments,
            'loves' => $loves,
            'projects' => $projects,
            'currentProjects' => $currentProjects,
            'doneProjects' => $doneProjects,
            'users' => $users,
            'starz' => $starz,
            'singzers' =>$singzers,
            'graphPub' => $graphPub,
            'graphCom' => $graphCom,
            'graphLov' => $graphLov,
            'dates' => $dates
        ));
    }

    public function indexAction()
    {
    	return $this->redirectToRoute('singz_admin_dashboard');
    }
}
