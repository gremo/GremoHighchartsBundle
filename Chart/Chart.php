<?php

/*
 * This file is part of the GremoHighchartsBundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\HighchartsBundle\Chart;

/**
 * Represents a generic chart.
 *
 * @static Chart create()
 */
class Chart extends ChartProperty
{
    /**
     * @var null|ChartProperty|ChartProperty[]
     */
    protected $xAxis;

    /**
     * @var null|ChartProperty|ChartProperty[]
     */
    protected $yAxis;

    /**
     * @var array|Series[]
     */
    protected $series;

    /**
     * Creates a new chart.
     */
    public function __construct()
    {
        $this->series = array();
    }

    /**
     * Creates and returns a new x axis, adding it to this chart.
     *
     * @return ChartProperty
     */
    public function newXAxis()
    {
        return $this->newAxis($this->xAxis);
    }

    /**
     * Creates and returns a new y axis, adding it to this chart.
     *
     * @return ChartProperty
     */
    public function newYAxis()
    {
        return $this->newAxis($this->yAxis);
    }

    /**
     * Creates a new series.
     *
     * @return Series
     */
    public function newSeries()
    {
        $reflection = new \ReflectionClass('Gremo\HighchartsBundle\Chart\Series');
        $series = $reflection->newInstance();

        // Set the parent protected property
        $parentProperty = $reflection->getProperty('parent');
        $parentProperty->setAccessible(true);
        $parentProperty->setValue($series, $this);
        $parentProperty->setAccessible(false);

        // Add to the collection
        $this->series[] = $series;

        return $series;
    }

    /**
     * {@inheritdoc}
     */
    public function getJsonData()
    {
        // Unset empty properties before returing the array
        foreach(array('xAxis', 'yAxis', 'series') as $property) {
            if(empty($property)) {
                unset($property);
            }
        }

        return parent::getJsonData();
    }

    protected function newAxis(&$current)
    {
        if($current instanceof ChartProperty) {
            $current = array($current, $new = parent::create());
        }
        elseif(is_array($current)) {
            $current[] = $new = parent::create();
        }
        else {
            $new = $current = parent::create();
        }

        // Set the parent property
        $new->parent = $this;

        return $new;
    }
}
