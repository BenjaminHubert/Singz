<?php

namespace Singz\AdminBundle\Controller;

use Singz\AdminBundle\Entity\Settings;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * Setting controller.
 *
 */
class SettingsController extends Controller
{
    /**
     * Lists all setting entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $settings = $em->getRepository('SingzAdminBundle:Settings')->findAll();

        return $this->render('SingzAdminBundle:Settings:index.html.twig', array(
            'settings' => $settings,
        ));
    }

    /**
     * Finds and displays a setting entity.
     *
     */
    public function showAction(Settings $setting)
    {

        return $this->render('settings/show.html.twig', array(
            'setting' => $setting,
        ));
    }
}
