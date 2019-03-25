<?php

namespace MakG\UserBundle\DependencyInjection;


use MakG\UserBundle\Controller\RegistrationController;
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

        $definition = $container->getDefinition('makg_user.user_manager');
        $definition->replaceArgument(0, $config['user_class']);

        $definition = $container->getDefinition(RegistrationController::class);
        $definition->replaceArgument(2, $config['form_types']['registration']);
    }
}