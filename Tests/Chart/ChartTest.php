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

use Gremo\HighchartsBundle\Chart\Chart;

class ChartTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Gremo\HighchartsBundle\Chart\Chart
     */
    private $chart;

    public function setUp()
    {
        $this->chart = new Chart();
    }

    public function testNewXAxisIfZeroXAxesArePresentCreatesTheAxis()
    {
        $axis = $this->chart->newXAxis();

        $this->assertAttributeSame($axis, 'xAxis', $this->chart);
        $this->assertIsChartProperty($axis, $this->chart);
    }

    public function testNewXAxisIfOneXAxisIsPresentCreatesTheAxisArray()
    {
        $axis1 = $this->chart->newXAxis();
        $axis2 = $this->chart->newXAxis();

        $this->assertAttributeInternalType('array', 'xAxis', $this->chart);
        $this->assertAttributeCount(2, 'xAxis', $this->chart);

        $this->assertIsAxisContainedIn($axis1, 'xAxis', $this->chart);
        $this->assertIsAxisContainedIn($axis2, 'xAxis', $this->chart);
    }

    public function testNewYAxisIfZeroYAxesArePresentCreatesTheAxis()
    {
        $axis = $this->chart->newYAxis();

        $this->assertAttributeSame($axis, 'yAxis', $this->chart);
        $this->assertIsChartProperty($axis, $this->chart);
    }

    public function testNewYAxisIfOneYAxisIsPresentCreatesTheAxisArray()
    {
        $axis1 = $this->chart->newYAxis();
        $axis2 = $this->chart->newYAxis();

        $this->assertAttributeInternalType('array', 'yAxis', $this->chart);
        $this->assertAttributeCount(2, 'yAxis', $this->chart);

        $this->assertIsAxisContainedIn($axis1, 'yAxis', $this->chart);
        $this->assertIsAxisContainedIn($axis2, 'yAxis', $this->chart);
    }

    public function testAddSeriesCreatesTheSeriesArray()
    {
        $series = $this->chart->newSeries();

        $this->assertAttributeInternalType('array', 'series', $this->chart);
        $this->assertAttributeCount(1, 'series', $this->chart);

        $this->assertAttributeContains($series, 'series', $this->chart);
        $this->assertIsChartProperty($series, $this->chart);
    }

    protected function assertIsAxisContainedIn($axis, $attribute, $chart)
    {
        $this->assertAttributeContains($axis, $attribute, $chart);
        $this->assertIsChartProperty($axis, $chart);
    }

    protected function assertIsChartProperty($objet, $chart)
    {
        $this->assertInstanceOf('Gremo\HighchartsBundle\Chart\ChartProperty', $objet);
        $this->assertAttributeSame($chart, 'parent', $objet);
    }
}
