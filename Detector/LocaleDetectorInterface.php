<?php

/*
 * This file is part of the GremoHighchartsBundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\HighchartsBundle\Detector;

interface LocaleDetectorInterface
{
    /**
     * Returns the current or fallback locale.
     *
     * @return string
     */
    public function getLocale();
}
