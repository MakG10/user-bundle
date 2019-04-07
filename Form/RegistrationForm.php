<?php

namespace MakG\UserBundle\Form;


use MakG\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationForm extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('email', EmailType::class)
			->add('displayName', TextType::class)
			->add('plainPassword', RepeatedType::class, [
				'type' => PasswordType::class,
				'first_options' => ['label' => 'Password'],
				'second_options' => ['label' => 'Repeat password'],
			])
			->add('submit', SubmitType::class, [
				'label' => 'Sign up',
			]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
            'data_class'         => User::class,
            'translation_domain' => 'MakGUserBundle',
		]);
	}
}
