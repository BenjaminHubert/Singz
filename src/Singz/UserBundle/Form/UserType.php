<?php 

namespace Singz\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Singz\UserBundle\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$roles = User::getAllRoles();
		unset($roles['ROLE_DEFAULT']);

		$builder
			->add('roles', ChoiceType::class, array(
				'attr'  =>  array(
					'class' => 'selectpicker',
				),
				'choices' => $roles,
				'multiple' => true,
				'required' => true,
			))
			->add('submit', SubmitType::class,array(
				'label' => 'Edit'
			))
		;
		$builder->remove('plainPassword');
	}

	public function getParent()
	{
		return 'FOS\UserBundle\Form\Type\RegistrationFormType';
	}

	public function getBlockPrefix()
	{
		return 'singz_user_registration';
	}
}