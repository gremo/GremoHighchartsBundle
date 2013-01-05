<?php

/*
 * This file is part of the HighchartsBundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\HighchartsBundle\Tests\Provider;

class LangOptionsProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetOptionIfTranslationExistsUsesUserProviderDomain()
    {
        $translator = $this->getMockedTranslator();

        $provider = $this->getMockBuilder('Gremo\HighchartsBundle\Provider\LangOptionsProvider')
            ->setConstructorArgs(array($translator, 'mydomain'))
            ->setMethods(array('__toString'))
            ->getMock();

        $translator->expects($this->any())
            ->method('trans')
            ->with($this->anything(), array(), 'mydomain')
            ->will($this->returnCallback(function($id) {
                $values = array('loading' => 'Caricamento...');

                return isset($values[$id]) ? $values[$id] : "Translated $id";
            }));

        $options = $provider->getOptions();

        $this->assertArrayHasKey('lang', $options);
        $this->assertArrayHasKey('loading', $options['lang']);
        $this->assertSame('Caricamento...', $options['lang']['loading']);
    }

    public function testGetOptionsIfTranslationDoesNotExistUsesBundleDomain()
    {
        $translator = $this->getMockedTranslator();

        $translator->expects($this->any())
            ->method('trans')
            ->will($this->returnCallback(function($id, $params, $domain) {
                return 'mydomain' === $domain ? $id : "Translated $id";
            }));

        $provider = $this->getMockBuilder('Gremo\HighchartsBundle\Provider\LangOptionsProvider')
            ->setConstructorArgs(array($translator, 'mydomain'))
            ->setMethods(array('__toString'))
            ->getMock();

        $options = $provider->getOptions();

        $this->assertArrayHasKey('lang', $options);
        $this->assertArrayHasKey('loading', $options['lang']);
        $this->assertContains('translated', $options['lang']['loading'], '', true);
    }

    public function testGetOptionsIfTranslationIsDefaultReturnsArrayWithoutIt()
    {
        $translations = array('downloadJPEG' => 'Download JPEG image',);

        $provider = $this->getMockedLangProvider($translations);
        $options = $provider->getOptions();

        $this->assertArrayHasKey('lang', $options);
        $this->assertArrayNotHasKey('downloadJPEG', $options['lang']);
    }

    public function testGetOptionsIfTranslationIsNewReturnsArrayWithIt()
    {
        $translations = array('downloadPDF' => 'DOWNLOAD PDF DOCUMENT');

        $provider = $this->getMockedLangProvider($translations);
        $options = $provider->getOptions();

        $this->assertArrayHasKey('lang', $options);
        $this->assertArrayHasKey('downloadPDF', $options['lang']);
        $this->assertSame($translations['downloadPDF'], $options['lang']['downloadPDF']);
    }

    public function testGetOptionsIfArrayTranslationIsDefaultReturnsArrayWithoutIt()
    {
        $translations = array(
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
        );

        $provider = $this->getMockedLangProvider($translations);
        $options = $provider->getOptions();

        $this->assertArrayHasKey('lang', $options);
        $this->assertArrayNotHasKey('shortmonths', $options['lang']);
    }

    public function testGetOptionsIfArrayTranslationIsNewReturnsArrayWithIt()
    {
        $translations = array(
            'weekdays.sunday'    => 'Domenica',
            'weekdays.monday'    => 'Lunedì',
            'weekdays.tuesday'   => 'Martedì',
            'weekdays.wednesday' => 'Mercoledì',
            'weekdays.thursday'  => 'Giovedì',
            'weekdays.friday'    => 'Venerdì',
            'weekdays.saturday'  => 'Sabato',
        );

        $provider = $this->getMockedLangProvider($translations);
        $options = $provider->getOptions();

        $this->assertArrayHasKey('lang', $options);
        $this->assertArrayHasKey('weekdays', $options['lang']);
        $this->assertSame(array_values($translations), $options['lang']['weekdays']);
    }

    public function getMockedLangProvider(array $translations)
    {
        $provider = $this->getMockBuilder('Gremo\HighchartsBundle\Provider\LangOptionsProvider')
            ->disableOriginalConstructor()
            ->setMethods(array('getTranslation'))
            ->getMock();

        $provider->expects($this->any())
            ->method('getTranslation')
            ->will($this->returnCallback(function($id) use($translations) {
                return isset($translations[$id]) ? $translations[$id] : $id;
        }));

        return $provider;
    }

    public function getMockedTranslator()
    {
        return $this->getMockBuilder('Symfony\Component\Translation\TranslatorInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
