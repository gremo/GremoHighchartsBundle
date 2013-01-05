<?php

/*
 * This file is part of the GremoHighchartsBundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\HighchartsBundle\Tests\Chart;

use Gremo\HighchartsBundle\Chart\Series;

class SeriesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Gremo\HighchartsBundle\Chart\Series
     */
    private $series;

    public function setUp()
    {
        $this->series = new Series();
    }

    public function testNewValueAddsTheValueToTheDataAndReturnsTheSeries()
    {
        $series = $this->series->newValue('foo');

        $this->assertDataAttribute(array('foo'));
        $this->assertSame($this->series, $series);
    }

    public function testNewValuesAddsValuesToTheDataAndReturnsTheSeries()
    {
        $series = $this->series->newValues('foo', 'bar');

        $this->assertDataAttribute(array('foo', 'bar'));
        $this->assertSame($this->series, $series);
    }

    public function testNewPointAddsThePointToTheDataAndReturnsTheSeries()
    {
        $series = $this->series->newPoint('foo', 'bar');

        $this->assertDataAttribute(array(array('foo', 'bar')));
        $this->assertSame($this->series, $series);
    }

    public function testNewComplexPointAddsTheComplexPointToTheDataAndReturnsItself()
    {
        $point = $this->series->newComplexPoint();

        $this->assertInstanceOf('Gremo\HighchartsBundle\Chart\ChartProperty', $point);
        $this->assertAttributeSame($this->series, 'parent', $point);
        $this->assertDataAttribute(array($point));
    }

    public function assertDataAttribute($values)
    {
        $this->assertObjectHasAttribute('data', $this->series);
        $this->assertAttributeInternalType('array', 'data', $this->series);
        $this->assertAttributeCount(count($values), 'data', $this->series);
        $this->assertAttributeSame($values, 'data', $this->series);
    }
}
