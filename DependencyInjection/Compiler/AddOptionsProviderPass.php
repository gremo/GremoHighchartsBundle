<?php

/*
 * This file is part of the GremoHighchartsBundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\HighchartsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;

class AddOptionsProviderPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if(!$container->hasDefinition('gremo_highcharts')) {
            return;
        }

        $chainDefinition = $container->getDefinition('gremo_highcharts.options_provider.chain');

        foreach($container->findTaggedServiceIds('gremo_highcharts.options_provider') as $id => $tagsAttributes) {
            $class = $container->getDefinition($id)->getClass();

            $refection = new \ReflectionClass($class);
            $interface = 'Gremo\HighchartsBundle\Provider\OptionsProviderInterface';

            // Check if implements the given interface
            if(!$refection->isSubclassOf($interface)) {
                throw new InvalidArgumentException(sprintf('Service "%s" must implement interface "%s".', $id, $interface));
            }

            // Get the priority if defined
            $attributes = call_user_func_array('array_merge', $tagsAttributes);
            $priority = isset($attributes['priority']) ? $attributes['priority'] : 0;

            $chainDefinition->addMethodCall('addOptionsProvider', array(new Reference($id), $priority));
        }

        $highchartsDefinition = $container->getDefinition('gremo_highcharts');
        $highchartsDefinition->addArgument(new Reference('gremo_highcharts.options_provider.chain'));
    }
}
