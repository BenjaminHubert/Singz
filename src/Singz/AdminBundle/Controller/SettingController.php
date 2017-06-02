<?php

namespace Singz\AdminBundle\Controller;

use Singz\AdminBundle\Entity\Setting;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Setting controller.
 *
 */
class SettingController extends Controller
{
    /**
     * Lists all setting entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $settings = $em->getRepository('SingzAdminBundle:Setting')->findAll();

        return $this->render('SingzAdminBundle:Setting:index.html.twig', array(
            'settings' => $settings,
        ));
    }

    /**
     * Finds and displays a setting entity.
     *
     */
    public function showAction(Setting $setting)
    {

        return $this->render('SingzAdminBundle:Setting:show.html.twig', array(
            'setting' => $setting,
        ));
    }

    /**
     * Edits a setting entity
     *
     */
    public function editAction(Request $request, $idSetting, $value)
    {
        // Check if AJAX request
        if(!$request->isXmlHttpRequest()) {
            return new Response('Must be an XML HTTP request', Response::HTTP_BAD_REQUEST);
        }

        // Get setting
        $em = $this->getDoctrine()->getManager();
        $setting = $em->getRepository('SingzAdminBundle:Setting')->getSettingById($idSetting);
        if($setting == null) {
            throw $this->createNotFoundException('Paramètre inexistant');
        }

        $setting->setValue($value);

        $em->persist($setting);
        $em->flush();

        return new Response(null, Response::HTTP_OK);
    }
}
