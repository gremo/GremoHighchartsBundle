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
 * Represents a generic chart (nested) property.
 */
class ChartProperty
{
    /**
     * @var null|ChartProperty
     */
    protected $parent;

    /**
     * Creates a new chart property.
     *
     * @return ChartProperty
     */
    public static function create()
    {
        return new static();
    }

    public function __call($name, $args)
    {
        if(strlen($name) > 3) {
            $type = substr($name, 0, 3);
            $prop = lcfirst(substr($name, 3));

            if('set' === $type) {
                $this->$prop = isset($args[0]) ? $args[0] : null;
                return $this;
            }

            if('get' === $type) {
                return isset($this->$prop) ? $this->$prop : null;
            }

            if('new' === $type) {
                $reflection = new \ReflectionClass('Gremo\HighchartsBundle\Chart\ChartProperty');

                // Create a new property
                $instance = $this->$prop = $reflection->newInstance();

                // Set the parent property
                $parentProperty = $reflection->getProperty('parent');
                $parentProperty->setAccessible(true);
                $parentProperty->setValue($instance, $this);
                $parentProperty->setAccessible(false);

                return $instance;
            }
        }
    }

    /**
     * Returns the parent object, if any.
     *
     * @return ChartProperty
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Returns the array representation of this object.
     *
     * @return array
     */
    public function getJsonData()
    {
        $properties = get_object_vars($this);

        // Do not serialize parent property
        unset($properties['parent']);

        // Go down in the three and get the data
        array_walk_recursive($properties, function(&$value, $key) {
            if(is_object($value) && is_callable(array($value, $call = 'getJsonData'))) {
                $value = $value->$call();
            }
        });

        return $properties;
    }

    /**
     * Returns the JSON string representation of this object.
     *
     * @return string
     */
    public function getJson()
    {
        return json_encode($this->getJsonData());
    }

    /**
     * Returns the string representation of this object.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getJson();
    }
}
