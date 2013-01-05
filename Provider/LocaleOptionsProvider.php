<?php

/*
 * This file is part of the GremoHighchartsBundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\HighchartsBundle\Provider;

use Gremo\HighchartsBundle\Detector\LocaleDetectorInterface;
use Gremo\HighchartsBundle\Common\Exception\HighchartsException;

class LocaleOptionsProvider implements OptionsProviderInterface
{
    /**
     * @var \NumberFormatter
     */
    private $formatter;

    /**
     * @var array
     */
    private $defaults = array(
        'decimalPoint' => '.',
        'thousandsSep' => ',',
    );

    public function __construct(LocaleDetectorInterface $detector)
    {
        $formatter = $this->formatter = new \NumberFormatter($detector->getLocale(), \NumberFormatter::DECIMAL);

        if(intl_is_failure($formatter->getErrorCode())) {
            throw new HighchartsException($formatter->getErrorMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        $options = array();
        foreach($this->defaults as $optionKey => $defaultValue) {
            $method = sprintf("get%s", ucfirst($optionKey));

            $optionValue = $this->$method();
            if($defaultValue !== $optionValue) {
                $options[$optionKey] = $optionValue;
            }
        }

        // Return the empty array or the options array with "lang" key
        return empty($options) ? $options : array('lang' => $options);
    }

    protected function getDecimalPoint()
    {
        return $this->formatter->getSymbol(\NumberFormatter::DECIMAL_SEPARATOR_SYMBOL);
    }

    protected function getThousandsSep()
    {
        return $this->formatter->getSymbol(\NumberFormatter::GROUPING_SEPARATOR_SYMBOL);
    }
}
