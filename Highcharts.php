<?php

/*
 * This file is part of the GremoHighchartsBundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\HighchartsBundle;

use Gremo\HighchartsBundle\Chart\Chart;
use Gremo\HighchartsBundle\Provider\OptionsProviderInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * The Highcharts facade class.
 */
class Highcharts
{
    /**
     * @var null|\Gremo\HighchartsBundle\Provider\OptionsProviderInterface
     */
    private $optionsProvider;

    public function __construct(OptionsProviderInterface $optionsProvider = null)
    {
        $this->optionsProvider = $optionsProvider;
    }

    /**
     * Returns a new area chart
     *
     * @return Chart
     */
    public function newAreaChart()
    {
        return $this->newChart('area');
    }

    /**
     * Returns a new area range chart
     *
     * @return Chart
     */
    public function newAreaRangeChart()
    {
        return $this->newChart('arearange');
    }

    /**
     * Returns a new area spline chart
     *
     * @return Chart
     */
    public function newAreaSplineChart()
    {
        return $this->newChart('areaspline');
    }

    /**
     * Returns a new area spline range chart
     *
     * @return Chart
     */
    public function newAreaSplineRangeChart()
    {
        return $this->newChart('areasplinerange');
    }

    /**
     * Returns a new bar chart
     *
     * @return Chart
     */
    public function newBarChart()
    {
        return $this->newChart('bar');
    }

    /**
     * Returns a new column chart
     *
     * @return Chart
     */
    public function newColumnChart()
    {
        return $this->newChart('column');
    }

    /**
     * Returns a new column range chart
     *
     * @return Chart
     */
    public function newColumnRangeChart()
    {
        return $this->newChart('columnrange');
    }

    /**
     * Returns a new line chart
     *
     * @return Chart
     */
    public function newLineChart()
    {
        return $this->newChart('line');
    }

    /**
     * Returns a new pie chart
     *
     * @return Chart
     */
    public function newePieChart()
    {
        return $this->newChart('pie');
    }

    /**
     * Returns a new scatter chart
     *
     * @return Chart
     */
    public function newScatterChart()
    {
        return $this->newChart('scatter');
    }

    /**
     * Returns a new spline chart
     *
     * @return Chart
     */
    public function newSplineChart()
    {
        return $this->newChart('spline');
    }

    /**
     * Returns a new chart, optionally setting the type property.
     *
     * @param null|string $type The chart type
     * @return Chart
     */
    public function newChart($type = null)
    {
        if(null === $type) {
            return Chart::create();
        }

        return Chart::create()
            ->newChart()
                ->setType($type)
            ->getParent();
    }

    /**
     * Returns a new response of type application/json with the merged option defined using
     * option providers services.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws Common\Exception\UnexpectedTypeException
     */
    public function getOptions()
    {
        $response = new Response();
        $options = null === $this->optionsProvider ? array() : $this->optionsProvider->getOptions();

        $response->setStatusCode(200);
        $response->setContent(empty($options) ? '{ }' : json_encode($options));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
