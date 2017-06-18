<?php

namespace Singz\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ContributionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
    		->add('amount', NumberType::class, array(
    			'label' => 'Montant',
    			'scale' => 2,
    			'grouping' => true
    		))
        	->add('project', EntityType::class, array( 
    			'class' => 'Singz\CoreBundle\Entity\Project',
        		'choice_label' => 'name'
    		))
        	->add('contributer', EntityType::class, array( 
    			'class' => 'Singz\UserBundle\Entity\User',
        		'choice_label' => 'username'
    		))
      		->add('save', SubmitType::class, array(
      			'label' => 'Participer'
      		))
      	;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Singz\CoreBundle\Entity\Contribution'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'singz_core_bundle_contribution';
    }


}
