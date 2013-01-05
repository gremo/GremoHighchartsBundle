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

use Symfony\Component\Translation\TranslatorInterface;

class LangOptionsProvider implements OptionsProviderInterface
{
    /**
     * @var string
     */
    const MESSAGES_DOMAIN = 'gremo_highcharts';

    /**
     * @var array
     */
    protected $defaults = array(
        'downloadJPEG'      => 'Download JPEG image',
        'downloadPDF'       => 'Download PDF document',
        'downloadPNG'       => 'Download PNG image',
        'downloadSVG'       => 'Download SVG vector image',
        'exportButtonTitle' => 'Export to raster or vector image',
        'loading'           => 'Loading...',
        'months'            => array(
            'january'   => 'January',
            'february'  => 'February',
            'march'     => 'March',
            'april'     => 'April',
            'may'       => 'May',
            'june'      => 'June',
            'july'      => 'July',
            'august'    => 'August',
            'september' => 'September',
            'october'   => 'October',
            'november'  => 'November',
            'december'  => 'December',
        ),
        'printButtonTitle'  => 'Print the chart',
        'resetZoom'         => 'Reset zoom',
        'resetZoomTitle'    => 'Reset zoom level 1:1',
        'shortMonths'       => array(
            'jan' => 'Jan',
            'feb' => 'Feb',
            'mar' => 'Mar',
            'apr' => 'Apr',
            'may' => 'May',
            'jun' => 'Jun',
            'jul' => 'Jul',
            'aug' => 'Aug',
            'sep' => 'Sep',
            'oct' => 'Oct',
            'nov' => 'Nov',
            'dic' => 'Dic',
        ),
        'weekdays'          => array(
            'sunday'    => 'Sunday',
            'monday'    => 'Monday',
            'tuesday'   => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday'  => 'Thursday',
            'friday'    => 'Friday',
            'saturday'  => 'Saturday',
        )
    );

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    protected $translator;

    /**
     * @var string
     */
    protected $domain;

    public function __construct(TranslatorInterface $translator, $domain)
    {
        $this->translator = $translator;
        $this->domain = $domain;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        $options = array();
        foreach($this->defaults as $optionKey => $defaultValue) {
            if(is_array($defaultValue)) {
                $translation = array();
                foreach($defaultValue as $optionSubKey => $defaultSubValue) {
                    $translation[] = $this->getTranslation("$optionKey.$optionSubKey");
                }

                // Add it only if it differs from the default array value
                if($translation !== array_values($defaultValue)) {
                    $options[$optionKey] = $translation;
                }
            }
            else {
                $translation = $this->getTranslation($optionKey);

                // Add only if it differs from the default value
                if($translation !== $defaultValue) {
                    $options[$optionKey] = $translation;
                }
            }
        }

        // Return the empty array or the options array with "lang" key
        return empty($options) ? $options : array('lang' => $options);
    }

    protected function getTranslation($id)
    {
        $translation = $this->translator->trans($id, array(), $this->domain);

        return $translation !== $id ? $translation
            : $this->translator->trans($id, array(), self::MESSAGES_DOMAIN);
    }
}
