<?php

/*
 * This file is part of the GremoHighchartsBundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\HighchartsBundle\Tests\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Gremo\HighchartsBundle\DependencyInjection\Compiler\AddOptionsProviderPass;
use Gremo\HighchartsBundle\Provider\OptionsProviderInterface;
use Symfony\Component\DependencyInjection\Reference;

class TestOptionsProvider implements OptionsProviderInterface
{
    public function getOptions() {}
}

class AddOptionsProviderPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Gremo\HighchartsBundle\DependencyInjection\Compiler\AddOptionsProviderPass
     */
    public $pass;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    public $chainDefinition;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    public $highchartsDefinition;

    public function setUp()
    {
        $this->pass = new AddOptionsProviderPass();
        $this->chainDefinition = $this->getMockedDefinition();
        $this->highchartsDefinition = $this->getMockedDefinition();
    }

    public function testProcessIfHighchartsDefinitionIsNotDefinedDoesNotProcessTaggedServices()
    {
        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $container->expects($this->once())
            ->method('hasDefinition')
            ->with($this->equalTo('gremo_highcharts'))
            ->will($this->returnValue(false));

        $container->expects($this->never())
            ->method('findTaggedServiceIds');

        $this->pass->process($container);
    }

    public function testProcessIfThereAreNoProvidersChainIsAddedWithNoProviders()
    {
        $container = $this->getContainer();

        $this->chainDefinition->expects($this->never())
            ->method('addMethodCall');

        $this->highchartsDefinition->expects($this->once())
            ->method('addArgument');

        $this->pass->process($container);
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function testProcessIfProviderDoesNotImplementInterfaceThrowsException()
    {
        $myprovider = $this->getMockedDefinition();
        $container = $this->getContainer(array('myprovider' => array()), array('myprovider' => $myprovider));

        $myprovider->expects($this->once())
            ->method('getClass')
            ->will($this->returnValue('stdClass'));

        $this->pass->process($container);
    }

    public function testProcessIfProviderIsValidAddsItToTheChain()
    {
        $provider = $this->getMockedDefinition();

        $providers = array(
            'myprovider' => array(0 => array('priority' => 3)),
        );

        $container = $this->getContainer($providers, array('myprovider' => $provider));

        $provider->expects($this->once())
            ->method('getClass')
            ->will($this->returnValue('Gremo\HighchartsBundle\Tests\DependencyInjection\Compiler\TestOptionsProvider'));

        $this->chainDefinition->expects($this->once())
            ->method('addMethodCall')
            ->with('addOptionsProvider', array(new Reference('myprovider'), 3));

        $this->pass->process($container);
    }

    public function getContainer(array $providers = array(), array $providersDefinitions = array())
    {
        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $container->expects($this->once())
            ->method('hasDefinition')
            ->with($this->equalTo('gremo_highcharts'))
            ->will($this->returnValue(true));

        $container->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->equalTo('gremo_highcharts.options_provider'))
            ->will($this->returnValue($providers));

        $self = $this;
        $container->expects($this->any())
            ->method('getDefinition')
            ->will($this->returnCallback(function($id) use($self, $providersDefinitions) {
            $defs = array_merge(array(
                'gremo_highcharts.options_provider.chain' => $self->chainDefinition,
                'gremo_highcharts' => $self->highchartsDefinition,
            ), $providersDefinitions);

            return $defs[$id];
        }));

        return $container;
    }

    public function getMockedDefinition()
    {
        return $this->getMockBuilder('Symfony\Component\DependencyInjection\Definition')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
