<?php

namespace MakG\UserBundle\DependencyInjection;


use MakG\UserBundle\Controller\RegistrationController;
use MakG\UserBundle\Controller\ResettingController;
use MakG\UserBundle\Controller\SecurityController;
use MakG\UserBundle\UserBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class UserExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $container->setParameter('makg_user.user_class', $config['user_class']);

        $definition = $container->getDefinition('makg_user.login_manager');
        $definition->replaceArgument(1, $config['firewall_name']);

        $definition = $container->getDefinition(RegistrationController::class);
        $definition->replaceArgument(2, $config['form_types']['registration']);

        $definition = $container->getDefinition(ResettingController::class);
        $definition->replaceArgument(3, $config['resetting']['retry_ttl']);

        $definition = $container->getDefinition(ResettingController::class);
        $definition->replaceArgument(4, $config['resetting']['token_ttl']);

        $definition = $container->getDefinition(ResettingController::class);
        $definition->replaceArgument(5, $config['form_types']['resetting_request']);

        $definition = $container->getDefinition(ResettingController::class);
        $definition->replaceArgument(6, $config['form_types']['reset_password']);

        $definition = $container->getDefinition(SecurityController::class);
        $definition->replaceArgument(0, $config['form_types']['login']);

        $definition = $container->getDefinition('makg_user.twig_swift_mailer');
        $definition->replaceArgument(2, $config['email_sender']);


        if ($config['use_flash_messages']) {
            $loader->load('flash_messages.yaml');
        }
    }

    public function getAlias()
    {
        return UserBundle::EXTENSION_ALIAS;
    }
}
