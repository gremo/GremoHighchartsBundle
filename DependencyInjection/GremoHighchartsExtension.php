<?php

/*
 * This file is part of the GremoHighchartsBundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\HighchartsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\HttpKernel\Kernel;

class GremoHighchartsExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('services.xml');

        // Set the correct locale detector alias based on Symfony version
        if(version_compare(Kernel::VERSION, '2.1.0', '<')) {
            $container->setAlias('gremo_highcharts.detector.default', 'gremo_highcharts.detector.session');
        }

        // Add the messages_domain argument to the lang provider
        $container->getDefinition('gremo_highcharts.options_provider.lang')
            ->addArgument($config['options_providers']['lang']['messages_domain']);

        // Add options_provider tag if lang provider is enabled
        if($config['options_providers']['lang']['enabled']) {
            $container->getDefinition('gremo_highcharts.options_provider.lang')
                ->addTag('gremo_highcharts.options_provider');
        }

        // Add options_provider tag if locale provider is enabled
        if($config['options_providers']['locale']['enabled']) {
            $container->getDefinition('gremo_highcharts.options_provider.locale')
                ->addTag('gremo_highcharts.options_provider');
        }

        // Add options_provider tag if credits disabler provider is enabled
        if($config['options_providers']['credits_disabler']['enabled']) {
            $container->getDefinition('gremo_highcharts.options_provider.credits_disabler')
                ->addTag('gremo_highcharts.options_provider', array('priority' => -10));
        }
    }
}
