<?php

/*
 * This file is part of the HighchartsBundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\HighchartsBundle\Tests\Detector;

use Gremo\HighchartsBundle\Detector\SessionLocaleDetector;
use Symfony\Component\HttpKernel\Kernel;

class SessionLocaleDetectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Gremo\HighchartsBundle\Detector\SessionLocaleDetector
     */
    private $detector;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $container;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $session;

    public function setUp()
    {
        if(version_compare(Kernel::VERSION, '2.1.0', '>=')) {
            $this->markTestSkipped('These tests are only for Symfony < 2.1.0.');
        }

        $this->container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->session =$this->getMockBuilder('Symfony\Component\HttpFoundation\Session')
            ->disableOriginalConstructor()
            ->getMock();

        $this->detector = new SessionLocaleDetector($this->container);
    }

    public function testGetLocaleReturnsLocaleFromTheSession()
    {
        $this->container->expects($this->once())
            ->method('get')
            ->with('session')
            ->will($this->returnValue($this->session));

        $this->session->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue('it'));

        $this->assertEquals('it', $this->detector->getLocale());
    }
}
