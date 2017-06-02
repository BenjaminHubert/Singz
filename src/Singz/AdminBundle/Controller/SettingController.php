<?php

namespace Singz\AdminBundle\Controller;

use Singz\AdminBundle\Entity\Setting;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Singz\AdminBundle\Form\SettingType;
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
    public function editAction(Request $request, $id)
    {
        // Get publication
        $em = $this->getDoctrine()->getManager();
        $setting = $em->getRepository('SingzAdminBundle:Setting')->getSettingById($id);
        if($setting == null) {
            throw $this->createNotFoundException('Paramètre inexistant');
        }

        // on récupère le formulaire
        $form = $this->createForm(SettingType::class, $setting);
        $form->remove('name');

        // si le formulaire est soumis
        if($request->isMethod('POST')){
            //on met dans notre objet $publication les valeurs du formulaire
            $form->handleRequest($request);

            // on vérifie la validation du formulaire
            if($form->isValid()){
                // update
                $em->flush();

                //on affiche un message
                $this->addFlash('success', 'Paramètre bien enregistré.');

                // On redirige vers la page de visualisation de la publication nouvellement créée
                return $this->redirectToRoute('singz_admin_setting_show', array('id' => $setting->getId()));
            }
        }


        return $this->render('SingzAdminBundle:Setting:edit.html.twig', array(
            'form' => $form->createView(),
            'setting' => $setting
        ));
    }
}
