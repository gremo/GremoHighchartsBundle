<?php

/*
 * This file is part of the GremoHighchartsBundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\HighchartsBundle\Common\Exception;

class UnexpectedTypeException extends HighchartsException
{
    public function __construct($expectedType, $value)
    {
        parent::__construct(sprintf('Expected value of type "%s", "%s" given.', $expectedType, gettype($value)));
    }
}
