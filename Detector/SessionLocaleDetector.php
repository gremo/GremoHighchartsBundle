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

use Symfony\Component\DependencyInjection\Container;

/**
 * Represents a locale detector for Symfony 2.0.x framework.
 */
class SessionLocaleDetector implements LocaleDetectorInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        return $this->container->get('session')->getLocale();
    }
}
