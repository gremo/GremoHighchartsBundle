<?php

/*
 * This file is part of the GremoHighchartsBundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\HighchartsBundle\Tests\Model;

use Gremo\HighchartsBundle\Chart\ChartProperty;

class ChartPropertyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Gremo\HighchartsBundle\Chart\ChartProperty
     */
    private $prop;

    public function setUp()
    {
        $this->prop = new ChartProperty();
    }

    public function testMagicSetterIfThereIsNoArgumentSetsThePropertyToNull()
    {
        $prop = $this->prop->setFoo();

        $this->assertAttributeSame(null, 'foo', $this->prop);
        $this->assertSame($prop, $this->prop);
    }

    public function testMagicSettersIfThereIsArgumentSetsTheValue()
    {
        $prop = $this->prop->setVeryLongProperty('foo');

        $this->assertAttributeSame('foo', 'veryLongProperty', $this->prop);
        $this->assertSame($prop, $this->prop);
    }

    public function testMagicGetterIfPropertyIsNotSettedReturnsNull()
    {
        $this->assertSame(null, $this->prop->getSomething());
    }

    /**
     * @depends testMagicSettersIfThereIsArgumentSetsTheValue
     */
    public function testMagicGetterIfPropertyIsSettedReturnsTheValue()
    {
        $this->assertSame('bar', $this->prop->setFoo('bar')->getFoo());
    }

    public function testMagicCreatorCreatesAndReturnsTheChartProperty()
    {
        $foo = $this->prop->newFoo();

        $this->assertObjectHasAttribute('foo', $this->prop);
        $this->assertAttributeInstanceOf('Gremo\HighchartsBundle\Chart\ChartProperty', 'foo', $this->prop);
        $this->assertAttributeSame($foo, 'foo', $this->prop);
    }

    /**
     * @depends testMagicCreatorCreatesAndReturnsTheChartProperty
     * @depends testMagicGetterIfPropertyIsSettedReturnsTheValue
     */
    public function testMagicCreatorCreatesTheParentProperty()
    {
        $this->prop->newFoo();

        $this->assertObjectHasAttribute('parent', $this->prop);
        $this->assertAttributeSame($this->prop, 'parent', $this->prop->getFoo());
    }

    /**
     * @depends testMagicCreatorCreatesAndReturnsTheChartProperty
     * @depends testMagicCreatorCreatesTheParentProperty
     * @depends testMagicSettersIfThereIsArgumentSetsTheValue
     */
    public function testToStringIgnoresParentProperty()
    {
        $this->prop->newFoo()->setBar('foo');

        $this->assertNotContains('parent', (string) $this->prop);
    }
}
