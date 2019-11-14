<?php

namespace MakG\UserBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginForm extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
            ->add(
                '_username',
                EmailType::class,
                [
                    'label' => 'E-mail',
                ]
            )
            ->add('_password', PasswordType::class)
            ->add(
                '_remember_me',
                CheckboxType::class,
                [
                    'label' => 'Remember me',
                    'attr' => [
                        'checked' => true,
                    ],
                ]
            )
			->add('submit', SubmitType::class, [
				'label' => 'Sign in',
			]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
            'data_class'         => null,
            'csrf_token_id'      => 'authenticate',
            'csrf_field_name'    => '_csrf_token',
            'translation_domain' => 'MakGUserBundle',
		]);
	}

	public function getBlockPrefix()
	{
		return null;
	}
}
