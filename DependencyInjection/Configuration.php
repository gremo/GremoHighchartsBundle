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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('gremo_highcharts');

        $rootNode
            ->children()
                ->arrayNode('options_providers')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('locale')
                            ->addDefaultsIfNotSet()
                            ->beforeNormalization()
                                ->ifNull()
                                ->then(function() { return array('enabled' => true); })
                            ->end()
                            ->validate()
                                ->ifTrue(function($v) {
                                    return $v['enabled'] && !function_exists('intl_get_error_code');
                                })
                                ->thenInvalid('You must enable the intl extension to use the "locale" provider.')
                            ->end()
                            ->children()
                                ->booleanNode('enabled')->defaultFalse()->end()
                            ->end()
                        ->end()
                        ->arrayNode('lang')
                            ->addDefaultsIfNotSet()
                            ->beforeNormalization()
                                ->ifNull()
                                ->then(function() { return array('enabled' => true); })
                            ->end()
                            ->children()
                                ->booleanNode('enabled')->defaultFalse()->end()
                                ->scalarNode('messages_domain')->defaultValue('gremo_highcharts')->end()
                            ->end()
                        ->end()
                        ->arrayNode('credits_disabler')
                            ->addDefaultsIfNotSet()
                            ->beforeNormalization()
                                ->ifNull()
                                ->then(function() { return array('enabled' => true); })
                            ->end()
                            ->children()
                                ->booleanNode('enabled')->defaultFalse()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
