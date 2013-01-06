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

use Gremo\HighchartsBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\Config\Definition\Processor
     */
    private $processor;

    /**
     * @var \Gremo\HighchartsBundle\DependencyInjection\Configuration
     */
    private $configuration;

    public function setUp()
    {
        $this->configuration = new Configuration();
        $this->processor = new Processor();
    }
    public function testProcessIfConfigIsEmpty()
    {
        $config = $this->processor->processConfiguration($this->configuration, array());

        $this->assertArrayHasKey('options_providers', $config);

        $this->assertArrayHasKey('lang', $config['options_providers']);
        $this->assertArrayHasKey('enabled', $config['options_providers']['lang']);
        $this->assertFalse($config['options_providers']['lang']['enabled']);
        $this->assertArrayHasKey('messages_domain', $config['options_providers']['lang']);
        $this->assertEquals('gremo_highcharts', $config['options_providers']['lang']['messages_domain']);

        $this->assertArrayHasKey('locale', $config['options_providers']);
        $this->assertArrayHasKey('enabled', $config['options_providers']['locale']);
        $this->assertFalse($config['options_providers']['locale']['enabled']);

        $this->assertArrayHasKey('credits_disabler', $config['options_providers']);
        $this->assertArrayHasKey('enabled', $config['options_providers']['credits_disabler']);
        $this->assertFalse($config['options_providers']['credits_disabler']['enabled']);
    }

    public function testProcessIfLangProviderIsNullEnablesLangProvider()
    {
        $configs = $this->getFullConfigs();
        $configs[0]['options_providers']['lang'] = null;

        $config = $this->processor->processConfiguration($this->configuration, $configs);

        $this->assertTrue($config['options_providers']['lang']['enabled']);
    }

    public function testProcessIfLangProviderIsEnabledEnablesLangProvider()
    {
        $configs = $this->getFullConfigs();
        $configs[0]['options_providers']['lang']['enabled'] = true;

        $config = $this->processor->processConfiguration($this->configuration, $configs);

        $this->assertTrue($config['options_providers']['lang']['enabled']);
    }

    public function testProcessIfLangProviderDomainIsSpecifiedSetsLangProviderDomain()
    {
        $configs = $this->getFullConfigs();
        $configs[0]['options_providers']['lang']['messages_domain'] = 'mydomain';

        $config = $this->processor->processConfiguration($this->configuration, $configs);

        $this->assertEquals('mydomain', $config['options_providers']['lang']['messages_domain']);
    }

    public function testProcessIfLocaleProviderIsNullEnablesLocaleProvider()
    {
        if(!extension_loaded('intl')) {
            $this->setExpectedException('Symfony\Component\Config\Definition\Exception\InvalidConfigurationException');
        }

        $configs = $this->getFullConfigs();
        $configs[0]['options_providers']['locale'] = null;

        $config = $this->processor->processConfiguration($this->configuration, $configs);

        $this->assertTrue($config['options_providers']['locale']['enabled']);
    }

    public function testProcessIfLocaleProviderIsEnabledEnablesLocaleProvider()
    {
        if(!extension_loaded('intl')) {
            $this->setExpectedException('Symfony\Component\Config\Definition\Exception\InvalidConfigurationException');
        }

        $configs = $this->getFullConfigs();
        $configs[0]['options_providers']['locale']['enabled'] = true;

        $config = $this->processor->processConfiguration($this->configuration, $configs);

        $this->assertTrue($config['options_providers']['locale']['enabled']);
    }

    public function testProcessIfCreditsDisablerProviderIsNullEnablesLangProvider()
    {
        $configs = $this->getFullConfigs();
        $configs[0]['options_providers']['credits_disabler'] = null;

        $config = $this->processor->processConfiguration($this->configuration, $configs);

        $this->assertTrue($config['options_providers']['credits_disabler']['enabled']);
    }

    public function testProcessIfCreditsDisablerProviderIsEnabledEnablesLangProvider()
    {
        $configs = $this->getFullConfigs();
        $configs[0]['options_providers']['credits_disabler']['enabled'] = true;

        $config = $this->processor->processConfiguration($this->configuration, $configs);

        $this->assertTrue($config['options_providers']['credits_disabler']['enabled']);
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
                    ),
                    'credits_disabler' => array(
                        'enabled' => false
                    )
                )
            )
        );
    }
}
