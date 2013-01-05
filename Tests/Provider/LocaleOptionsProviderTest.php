<?php

/*
 * This file is part of the HighchartsBundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\HighchartsBundle\Tests\Provider;

class LocaleOptionsProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetOptionsIfSymbolsAreDefaultReturnsEmptyArray()
    {
        $provider = $this->getMockedLocaleProvider(array('getDecimalPoint' => '.', 'getThousandsSep' => ','));

        $this->assertSame(array(), $provider->getOptions());
    }

    public function testGetOptionsIfDecimalPointIsDefaultReturnsOptionsWithoutIt()
    {
        $provider = $this->getMockedLocaleProvider(array('getDecimalPoint' => '.', 'getThousandsSep' => 'foo'));

        $options = $provider->getOptions();

        $this->assertInternalType('array', $options);
        $this->assertArrayHasKey('lang', $options);
        $this->assertArrayNotHasKey('decimalPoint', $options['lang']);
    }

    public function testGetOptionsIfThousandsSepIsDefaultReturnsOptionsWithoutIt()
    {
        $provider = $this->getMockedLocaleProvider(array('getDecimalPoint' => 'foo', 'getThousandsSep' => ','));

        $options = $provider->getOptions();

        $this->assertInternalType('array', $options);
        $this->assertArrayHasKey('lang', $options);
        $this->assertArrayNotHasKey('thousandsSep', $options['lang']);
    }

    public function getMockedLocaleProvider(array $methodValues)
    {
        $provider = $this->getMockBuilder('Gremo\HighchartsBundle\Provider\LocaleOptionsProvider')
            ->disableOriginalConstructor()
            ->setMethods(array_keys($methodValues))
            ->getMock();

        foreach($methodValues as $method => $returnValue) {
            $provider->expects($this->once())
                ->method($method)
                ->will($this->returnValue($returnValue));
        }

        return $provider;
    }
}
