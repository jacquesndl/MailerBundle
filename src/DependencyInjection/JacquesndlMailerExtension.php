<?php

namespace Jacquesndl\MailerBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * @author Jacques de Lamballerie <jndl@protonmail.com>
 */
final class JacquesndlMailerExtension extends Extension
{
    /**
     * @param array            $config
     * @param ContainerBuilder $container
     *
     * @throws Exception
     */
    public function load(array $config, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        $container->setParameter('jacquesndl.mailer.sender_name', $config['sender']['name']);
        $container->setParameter('jacquesndl.mailer.sender_address', $config['sender']['address']);
    }
}
