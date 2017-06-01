<?php 


namespace Singz\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EditMyProfileType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('isPrivate', CheckboxType::class, array(
				'label' => 'Privé',
				'required' => false
			))
			->add('biography', TextareaType::class, array(
				'label' => 'Biographie',
				'required' => false
			))
			->add('image', ImageType::class, array(
				'label' => 'Photo de profil',
				'required' => false
			))
		;
	}

	public function getParent()
	{
		return 'FOS\UserBundle\Form\Type\ProfileFormType';
	}

	public function getBlockPrefix()
	{
		return 'singz_user_profile';
	}
}