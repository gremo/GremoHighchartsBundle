<?php

/*
 * This file is part of the HighchartsBundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\HighchartsBundle\Tests;

use Gremo\HighchartsBundle\Highcharts;

class HighchartsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Gremo\HighchartsBundle\Highcharts
     */
    private $highcharts;

    private $provider;

    public function setUp()
    {
        $this->provider = $this->getMock('Gremo\HighchartsBundle\Provider\OptionsProviderInterface');
        $this->highcharts = new Highcharts($this->provider);
    }

    public function testNewAreaChart()
    {
        $this->assertIsChartWithTypeProperty('area', $this->highcharts->newAreaChart());
    }

    public function testNewAreaRangeChart()
    {
        $this->assertIsChartWithTypeProperty('arearange', $this->highcharts->newAreaRangeChart());
    }

    public function testNewAreaSplineChart()
    {
        $this->assertIsChartWithTypeProperty('areaspline', $this->highcharts->newAreaSplineChart());
    }

    public function testNewAreaSplineRangeChart()
    {
        $this->assertIsChartWithTypeProperty('areasplinerange', $this->highcharts->newAreaSplineRangeChart());
    }

    public function testNewBarChart()
    {
        $this->assertIsChartWithTypeProperty('bar', $this->highcharts->newBarChart());
    }

    public function testNewColumnChart()
    {
        $this->assertIsChartWithTypeProperty('column', $this->highcharts->newColumnChart());
    }

    public function testNewColumnRangeChart()
    {
        $this->assertIsChartWithTypeProperty('columnrange', $this->highcharts->newColumnRangeChart());
    }

    public function testNewLineChart()
    {
        $this->assertIsChartWithTypeProperty('line', $this->highcharts->newLineChart());
    }

    public function testNewPieChart()
    {
        $this->assertIsChartWithTypeProperty('pie', $this->highcharts->newePieChart());
    }

    public function testNewScatterChart()
    {
        $this->assertIsChartWithTypeProperty('scatter', $this->highcharts->newScatterChart());
    }

    public function testNewSplineChart()
    {
        $this->assertIsChartWithTypeProperty('spline', $this->highcharts->newSplineChart());
    }

    public function testGetOptionsIfProviderIsNullReturnsResponseWithEmptyJsonObect()
    {
        $this->highcharts = new Highcharts();

        $this->assertJsonResponse('{ }', $this->highcharts->getOptions());
    }

    public function testGetOptionsIfProviderReturnsSomeDataReturnsResponseWithJsonObject()
    {
        $this->provider->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue(array('foo' => 'bar')));

        $this->assertJsonResponse('{"foo":"bar"}', $this->highcharts->getOptions());
    }

    public function assertIsChartWithTypeProperty($type, $object)
    {
        $this->assertInstanceOf('Gremo\HighchartsBundle\Chart\Chart', $object);
        $this->assertObjectHasAttribute('chart', $object);
        $this->assertInstanceOf('Gremo\HighchartsBundle\Chart\ChartProperty', $object->getChart());
        $this->assertSame($object, $object->getChart()->getParent());
        $this->assertObjectHasAttribute('type', $object->getChart());
        $this->assertSame($type, $object->getChart()->getType());
    }

    public function assertJsonResponse($content, $response)
    {
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-type'));
        $this->assertSame($content, $response->getContent());
    }
}
