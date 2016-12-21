<?php

namespace Singz\UserBundle\Controller;

use Singz\SocialBundle\Entity\Follow;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('SingzUserBundle:User')->find($id);

        $publications = $em->getRepository('SingzSocialBundle:Publication')->findBy(array('user' => $user));

        // Get publications' threads, comments
        $threads = array();
        $allComments = array();

        foreach($publications as $pub){
            $id = $pub->getId();
            $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
            if (null === $thread) {
                $thread = $this->container->get('fos_comment.manager.thread')->createThread();
                $thread->setId($id);
                $thread->setPermalink($request->getUri());

                // Add the thread
                $this->container->get('fos_comment.manager.thread')->saveThread($thread);
            }

            $comments = $this->container->get('fos_comment.manager.comment')->findCommentTreeByThread($thread);

            $threads[$id] = $thread;
            $allComments[$id] = $comments;
        }

        $followers = $em->getRepository('SingzSocialBundle:Follow')->findBy(array('follower' => $user));
        $leader   = $em->getRepository('SingzSocialBundle:Follow')->findBy(array('leader' => $user));

        return $this->render('SingzUserBundle:Default:index.html.twig', array(
                "publications" => $publications,
                "threads" => $threads,
                "comments" => $allComments,
                "user" => $user,
                "followers" => $followers,
                "leader" => $leader
            )
        );
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
