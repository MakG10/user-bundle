<?php

namespace MakG\UserBundle\DependencyInjection;


use MakG\UserBundle\Form\LoginForm;
use MakG\UserBundle\Form\RegistrationForm;
use MakG\UserBundle\Form\ResetPasswordForm;
use MakG\UserBundle\Form\ResettingRequestForm;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('user_bundle');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->scalarNode('user_class')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('firewall_name')->cannotBeEmpty()->defaultValue('main')->end()
            ->scalarNode('email_sender')->defaultNull()->end()
            ->scalarNode('use_flash_messages')->defaultFalse()->end()
            ->arrayNode('resetting')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('retry_ttl')->cannotBeEmpty()->defaultValue(3600)->end()
            ->scalarNode('token_ttl')->cannotBeEmpty()->defaultValue(86400)->end()
            ->end()
            ->end()
            ->arrayNode('form_types')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('registration')->cannotBeEmpty()->defaultValue(RegistrationForm::class)->end()
            ->scalarNode('login')->cannotBeEmpty()->defaultValue(LoginForm::class)->end()
            ->scalarNode('reset_password')->cannotBeEmpty()->defaultValue(ResetPasswordForm::class)->end()
            ->scalarNode('resetting_request')->cannotBeEmpty()->defaultValue(ResettingRequestForm::class)->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
