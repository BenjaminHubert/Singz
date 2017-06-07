<?php

namespace Singz\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ProjectType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
    		->add('name', TextType::class, array(
    			'label' => 'Nom'
    		))
        	->add('description', TextareaType::class, array( 
    			'required' => false
    		))
        	->add('requester', EntityType::class, array( 
    			'class' => 'Singz\UserBundle\Entity\User',
        		'choice_label' => 'username'
    		))
      		->add('save', SubmitType::class, array(
      			'label' => 'Enregistrer'
      		))
      	;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Singz\CoreBundle\Entity\Project'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'singz_core_bundle_project';
    }


}
