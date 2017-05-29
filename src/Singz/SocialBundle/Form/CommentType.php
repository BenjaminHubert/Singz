<?php 

namespace Singz\SocialBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Singz\SocialBundle\Entity\Comment;

class CommentType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
	    	->add('author', EntityType::class, array(
	    		'class' => 'SingzUserBundle:User',
	    		'choice_label' => 'username',
	    		'attr' => array(
	    			'hidden' => ''
	    		)
	    	))
	    	->add('thread', EntityType::class, array(
	    		'class' => 'SingzSocialBundle:Thread',
	    		'choice_label' => 'id',
	    		'attr' => array(
	    			'hidden' => ''
	    		)
	    	))
	    	->add('body', TextareaType::class)
	    	->add('parent', EntityType::class, array(
	    		'class' => 'SingzSocialBundle:Comment',
	    		'choice_label' => 'id',
	    		'required' => false,
	    		'attr' => array(
	    			'hidden' => ''
	    		)
	    	))
		;
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => Comment::class,
		));
	}
}