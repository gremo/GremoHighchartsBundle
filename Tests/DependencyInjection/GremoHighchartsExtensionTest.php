<?php

/*
 * This file is part of the GremoHighchartsBundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\HighchartsBundle\Tests\DependencyInjection;

use Gremo\HighchartsBundle\DependencyInjection\GremoHighchartsExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class GremoHighchartsExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Gremo\HighchartsBundle\DependencyInjection\GremoHighchartsExtension
     */
    private $extension;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private $container;

    public function setUp()
    {
        $this->extension = new GremoHighchartsExtension();
        $this->container = new ContainerBuilder();
    }

    public function testLoadIfConfigIsEmptyHasDefinitionsAndDoesNotAssignProviderTags()
    {
        $this->extension->load(array(), $this->container);

        $this->assertDicDefinition('gremo_highcharts.options_provider.lang');
        $this->assertDicDefinitionNotHasTag('gremo_highcharts.options_provider', 'gremo_highcharts.options_provider.lang');

        $this->assertDicDefinition('gremo_highcharts.options_provider.locale');
        $this->assertDicDefinitionNotHasTag('gremo_highcharts.options_provider', 'gremo_highcharts.options_provider.locale');
    }

    public function testLoadIfConfigIsEmptyAddsDomainArgumentToLangProvider()
    {
        $this->extension->load(array(), $this->container);

        $this->assertDicDefinitionArgument(1, 'string', 'gremo_highcharts', 'gremo_highcharts.options_provider.lang');
    }

    public function testLoadIfConfigIsEmptySetsLocaleDetectorAlias()
    {
        $this->extension->load(array(), $this->container);

        $this->assertDicAlias('gremo_highcharts.detector.default', version_compare(Kernel::VERSION, '2.1.0', '<') ?
            'gremo_highcharts.detector.session' : 'gremo_highcharts.detector.request');
    }

    public function testLoadIfLangProviderIsEnabledAddsOptionsProviderTagToIt()
    {
        $configs = $this->getFullConfigs();
        $configs[0]['options_providers']['lang']['enabled'] = true;

        $this->extension->load($configs, $this->container);

        $this->assertDicDefinitionHasTag('gremo_highcharts.options_provider', 'gremo_highcharts.options_provider.lang');
    }

    public function testLoadIfLangProviderDomainIsSpecifiedAddsDomainArgumentToLangProvider()
    {
        $configs = $this->getFullConfigs();
        $configs[0]['options_providers']['lang']['messages_domain'] = 'mydomain';

        $this->extension->load($configs, $this->container);

        $this->assertDicDefinitionArgument(1, 'string', 'mydomain', 'gremo_highcharts.options_provider.lang');
    }

    public function testLoadIfLocaleProviderIsEnabledAddsOptionsProviderTagToIt()
    {
        if(!extension_loaded('intl')) {
            $this->setExpectedException('Symfony\Component\Config\Definition\Exception\InvalidConfigurationException');
        }

        $configs = $this->getFullConfigs();
        $configs[0]['options_providers']['locale']['enabled'] = true;

        $this->extension->load($configs, $this->container);

        $this->assertDicDefinitionHasTag('gremo_highcharts.options_provider', 'gremo_highcharts.options_provider.locale');
    }

    public function assertDicAlias($aliasId, $definitionId)
    {
        $this->assertTrue($this->container->hasAlias($aliasId), "Container has $aliasId alias.");

        $alias = $this->container->getAlias($aliasId);
        $this->assertEquals($definitionId, $alias, "Alias $aliasId has the correct value $definitionId.");
    }

    public function assertDicDefinition($definitionId)
    {
        $this->assertTrue($this->container->hasDefinition($definitionId), "Container has $definitionId definition.");
    }

    public function assertDicDefinitionHasTag($tag, $definitionId)
    {
        $this->assertTrue($this->container->getDefinition($definitionId)->hasTag($tag), "Defininition $definitionId has tag $tag.");
    }

    public function assertDicDefinitionNotHasTag($tag, $id)
    {
        $this->assertFalse($this->container->getDefinition($id)->hasTag($tag), "Defininition $id doesn't have tag $tag.");
    }

    public function assertDicDefinitionArgument($index, $internalType, $expectedValue, $definitionId)
    {
        $definition = $this->container->getDefinition($definitionId);
        $this->assertArrayHasKey($index, $definition->getArguments(), "Definition $definitionId has argument at $index.");

        $argument = $definition->getArgument($index);
        $this->assertInternalType($internalType, $argument, "Argument $index has the correct $internalType type.");
        $this->assertEquals($expectedValue, $argument, "Argument $index has the correct value $expectedValue.");
    }

    public function getFullConfigs()
    {
        return array(
            array(
                'options_providers' => array(
                    'lang' => array(
                        'enabled' => false,
                        'messages_domain' => 'gremo_highcharts'
                    ),
                    'locale' => array(
                        'enabled' => false
                    )
                )
            )
        );
    }
}
