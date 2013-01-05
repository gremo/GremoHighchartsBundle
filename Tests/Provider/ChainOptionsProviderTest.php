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

use Gremo\HighchartsBundle\Provider\ChainOptionsProvider;

class ChainOptionsProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Gremo\HighchartsBundle\Provider\ChainOptionsProvider
     */
    private $chain;

    public function setUp()
    {
        $this->chain = new ChainOptionsProvider();
    }

    public function testGetOptionsIfThereAreNoProvidersReturnsEmptyArray()
    {
        $merged = $this->chain->getOptions();

        $this->assertInternalType('array', $merged);
        $this->assertEquals(array(), $merged);
    }

    public function testGetOptionsIfProviderReturnsEmptyArrayReturnsEmptyArray()
    {
        $provider = $this->getMockedProvider();

        $provider->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue(array()));

        $this->chain->addOptionsProvider($provider, 13);

        $merged = $this->chain->getOptions();

        $this->assertInternalType('array', $merged);
        $this->assertEquals(array(), $merged);
    }

    public function testGetOptionsIfProviderReturnsDataReturnsCorrectArray()
    {
        $provider = $this->getMockedProvider();
        $options = array('foo' => 'bar');

        $provider->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue($options));

        $this->chain->addOptionsProvider($provider, 0);

        $merged = $this->chain->getOptions();

        $this->assertInternalType('array', $merged);
        $this->assertEquals($options, $merged);
    }

    /**
     * @dataProvider getInvalidProviderOptions
     * @expectedException \Gremo\HighchartsBundle\Common\Exception\UnexpectedTypeException
     */
    public function testGetOptionsIfProviderReturnsInvalidDataThrowsException($invalidData)
    {
        $provider = $this->getMockedProvider();

        $provider->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue($invalidData));

        $this->chain->addOptionsProvider($provider, 0);

        $this->chain->getOptions();
    }

    public function testGetOptionsIfThereAreMoreProvidersReturnsMergedArray()
    {
        $provider1 = $this->getMockedProvider();
        $provider2 = $this->getMockedProvider();
        $provider3 = $this->getMockedProvider();

        $this->chain->addOptionsProvider($provider1, 0);
        $this->chain->addOptionsProvider($provider2, 1);
        $this->chain->addOptionsProvider($provider3, 1);

        $provider1->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue(array(
                'a' => 'a',
                'b' => array('b1'),
            )));

        $provider2->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue(array(
                'a' => array('a2' => 'a2'),
                'b' => 'b2',
            )));

        $provider3->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue(array(
                'a' => array('a3' => 'a3'),
                'c' => 'c3'
        )));

        $merged = $this->chain->getOptions();

        $this->assertInternalType('array', $merged);
        $this->assertEquals(array('a' => array('a2' => 'a2', 'a3' => 'a3'), 'b' => 'b2', 'c' => 'c3'), $merged);
    }

    public function getMockedProvider()
    {
        return $this->getMockBuilder('Gremo\HighchartsBundle\Provider\OptionsProviderInterface')
            ->getMock();
    }

    public function getInvalidProviderOptions()
    {
        return array(
            array(null),
            array(''),
            array(true),
            array(new \stdClass()),
        );
    }
}
