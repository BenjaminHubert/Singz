<?php

namespace Singz\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CoreController extends Controller
{
    public function indexAction()
    {
        return $this->render('SingzCoreBundle:Core:index.html.twig', array(
            // ...
        ));
    }

}
