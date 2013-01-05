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
 * Represents a series.
 *
 * @static Series create()
 */
class Series extends ChartProperty
{
    /**
     * @var array
     */
    protected $data;

    /**
     * Creates a new series.
     */
    public function __construct()
    {
        $this->data = array();
    }

    /**
     * Creates a new numerical value.
     *
     * The numerical value will be interpreted and y value, and x value will be automatically
     * calculated.
     *
     * @param mixed $value The numerical value
     * @return Series
     */
    public function newValue($value)
    {
        $this->data[] = $value;

        return $this;
    }

    /**
     * Creates a variable number of points
     *
     * @param mixed $value The numerical value
     * @param mixed $value,...
     * @return Series
     */
    public function newValues($value)
    {
        $this->newValue($value);

        if(func_num_args() > 1) {
            for($i = 1; $i < func_num_args(); $i++) {
                $this->newValue(func_get_arg($i));
            }
        }

        return $this;
    }

    /**
     * Creates a new point.
     *
     * The first value is the x value and the second is the y value.
     *
     * @param mixed $x The x value
     * @param mixed $y The y value
     * @return Series
     */
    public function newPoint($x, $y)
    {
        $this->data[] = array($x, $y);

        return $this;
    }

    /**
     * Creates a new complex point.
     *
     * The object returned can be configured using magic setters, getters and creators.
     *
     * @return ChartProperty
     */
    public function newComplexPoint()
    {
        $this->data[] = $new = parent::create();

        // Set the parent property
        $new->parent = $this;

        return $new;
    }

    /**
     * Set an array of points for this series.
     *
     * @param array $data The data
     * @return Series
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getJsonData()
    {
        if(empty($this->data)) {
            unset($this->data);
        }

        return parent::getJsonData();
    }
}
