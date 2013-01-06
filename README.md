# GremoHighchartsBundle [![Build Status](https://secure.travis-ci.org/gremo/GremoHighchartsBundle.png)](http://travis-ci.org/gremo/GremoHighchartsBundle)

Symfony2 Bundle for creating Highcharts charts, fluently and with as little as possible of JavaScript.

- [Installation](#installation)
- [Configuration](#configuration)
- [Defining charts](#defining-charts)
    - [Creating charts objects](#creating-charts-objects)
    - [Setting and getting properties](#setting-and-getting-properties)
    - [Creating axes, series and points](#creating-axes-series-and-points)
    - [Options providers](#options-providers)
        - [Built-in options providers](#built-in-options-providers)
- [Rendering Charts](#rendering-charts)
- [Limitations](#limitations)
- [Planned features](#panned-features)

## Installation

Add the following to your `deps` file (for Symfony 2.0.*):

```
[GremoHighchartsBundle]
    git=https://github.com/gremo/GremoHighchartsBundle.git
    target=bundles/Gremo/HighchartsBundle
```

Then register the namespaces with the autoloader (`app/autoload.php`):

```php
$loader->registerNamespaces(array(
    // ...
    'Gremo' => __DIR__.'/../vendor/bundles',
    // ...
));
```

Or, if you are using Composer and Symfony 2.1.*, add to `composer.json` file:

```javascript
{
    "require": {
        "gremo/highcharts-bundle": "*"
    }
}
```

Finally register the bundle with your kernel in `app/appKernel.php`:
```php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Gremo\HighchartsBundle\GremoHighchartsBundle(),
        // ...
    );

    // ...
}
```

## Configuration
See [Options providers](#options-providers).

## Defining charts
First get `gremo_highcharts` service from the service container:

```php
/** @var $highcharts \Gremo\HighchartsBundle\Highcharts */
$highcharts = $this->get('gremo_highcharts');
```

### Creating charts objects
There is one method for each chart type in `gremo_highcharts` service:

```php
$highcharts->newAreaChart();
$highcharts->newAreaSplineChart();
$highcharts->newBarChart();
$highcharts->newColumnChart();
$highcharts->newLineChart();
$highcharts->newPieChart();
$highcharts->newScatterChart();
$highcharts->newSplineChart();

$highcharts->newAreaRangeChart();
$highcharts->newAreaSplineRangeChart();
$highcharts->newColumnRangeChart();
```

Last three chart types requires `highcharts-more.js`. For "special" charts (like combining more than one chart) you can
use the generic `newChart()` method.

### Setting and getting properties
Magic methods `setXxx` (set simple property), `newXxx` (create nested property), `getXxx` (get property) are available
for charts, axes, series and complex points. String part `Xxx` will be lcfirst-ed to `xxx` before setting the property.

Setters `setXxx` are fluent and returns the instance, while `newXxx` methods return the nested property itself. Use
`getParent()` to get the parent object:

 ```php
 $chart = $highcharts->newLineChart()
    ->newChart()
        ->setRenderTo('chart-container')
    ->getParent()
    ->newTitle()
        ->setText('My chart')
        ->newStlye()
            ->setColor('#FF00FF')
            ->setFontWeight('bold')
        ->getParent()
    ->getParent();

echo $chart;
 ```

Will result in:

 ```
 {
    "chart" : {
        "renderTo": "chart-container"
    },
    "title" : {
        "text": "My Chart",
        "style": {
            "color": "#FF00FF",
            "fontWeight": "bold"
        }
    }
 }
```

Refer to to [Highcharts API Reference](http://api.highcharts.com/highcharts) and to [Highcharts Demo Page](http://www.highcharts.com/demo/)
to control the behaviour of your chart.

### Creating axes, series and points
The chart object has `newXAxis()`, `newYAxis()` and `newSeries()` methods for creating and adding axes and series to the
chart. These methods return nested properties themselves, and work exactly the same way:

```php
$chart = $highcharts->newBarChart()
    ->newXAxis()
        ->setCategories(array('Africa', 'America', 'Asia', 'Europe', 'Oceania'))
        ->newTitle()
            ->setText(null)
        ->getParent()
    ->getParent()
    ->newYAxis()
        ->setMin(0)
        ->newTitle()
            ->setText('Population (millions)')
            ->setAlign('high')
        ->getParent()
        ->newLabels()
            ->setOverflow('justify')
        ->getParent()
    ->getParent();
```

For actually addding your data to the chart, you can use `newValue($value)`, `newPoint($x, $y)` and `newComplexPoint()`:

```php
$chart->newSeries()
    ->newComplexPoint()
        ->setName('Point 1')
        ->setColor('#00FF00')
        ->setY(0)
    ->getParent()
    ->newComplexPoint()
        ->setName('Point 2')
        ->setColor('#FF00FF')
        ->setY(5)
    ->getParent();
```

Alternatively you can set the data directly using `setData(array $data)` method.

Methods `newValue($value)`, `newPoint($x, $y)` and `setData(array $data)` returns the series while `newComplexPoint()` returns
the point itself, for chaining subsequent calls. Values, points and complex points are explained
[here](http://api.highcharts.com/highcharts#series.data).

### Options providers
Properties defined using options providers applies for all charts. Define a service, add `gremo_highcharts.options_provider`
tag and implement `Gremo\HighchartsBundle\Provider\OptionsProviderInterface` interface, returing the default options as
an `array` in `getOptions()` method:

```php
use Gremo\HighchartsBundle\Provider\OptionsProviderInterface;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("my_options_provider")
 * @DI\Tag("gremo_highcharts.options_provider", attributes={"priority"=10})
 */
class MyOptionsProvider implements ActivationDateProviderInterface
{
    /**
     * @return array
     */
    public function getOptions()
    {
        return array(
            'colors' => array(
                '#058DC7',
                '#50B432',
                '#ED561B',
                '#DDDF00',
                '#24CBE5',
                '#64E572',
                '#FF9655',
                '#FFF263',
                '#6AF9C4',
            )
        );
    }
}
```

Failing in returing an `array` type will throw an exception.

Providers with an higher priority will (nicely and recursively) override options from providers with a lower one. See
[Rendering Charts](#rendering-charts) for actually using default options. Priority attribute is not mandatory.

#### Built-in options providers
For setting common options, this bundle provides some built-in options providers. If you are fine with default options
you can use the short form (works for every provider):

```
gremo_highcharts:
    options_providers:
        credits_disabler: ~
        lang: ~
        locale: ~
        # ...
```

**credit_disabler**: sets Highcharts credits to off.

```
gremo_highcharts:
    options_providers:
        # ...
        credits_disabler:
            enabled: true
```

**lang**: provides translation for [`lang` strings](http://api.highcharts.com/highcharts#lang) using
[Symfony 2 translation system](http://symfony.com/doc/current/book/translation.html).

```
gremo_highcharts:
    options_providers:
        # ...
        lang:
            enabled: true
            messages_domain: mydomain # default to gremo_highcharts
```

Key reference along with default values:

```
downloadJPEG: Download JPEG image
downloadPDF: Download PDF document
downloadPNG: Download PNG image
downloadSVG: Download SVG vector image
exportButtonTitle: Export to raster or vector image
loading: Loading...
months:
    january: January
    february: February
    march: March
    april: April
    may: May
    june: June
    july: July
    august: August
    september: September
    october: October
    november: November
    december: December
printButtonTitle: Print the chart
resetZoom: Reset zoom
resetZoomTitle: Reset zoom level 1:1
shortMonths:
    jan: Jan
    feb: Feb
    mar: Mar
    apr: Apr
    may: May
    jun: Jun
    jul: Jul
    aug: Aug
    sep: Sep
    oct: Oct
    nov: Nov
    dic: Dic
weekdays:
    sunday: Sunday
    monday: Monday
    tuesday: Tuesday
    wednesday: Wednesday
    thursday: Thursday
    friday: Friday
    saturday: Saturday
```

**locale**: provides decimal and thousands separators based on the current locale, using [PHP intl extension](http://php.net/manual/en/book.intl.php).

```
gremo_highcharts:
    options_providers:
        # ...
        locale:
            enabled: true
```

## Rendering charts
First, pass the chart to your template:

```php
public function showAction()
{
    // Chart building...

    return $this->render(
        'AcmeHelloBundle:Hello:chart.html.twig',
        array('chart' => $chart)
    );
}
```

Then in your `AcmeHelloBundle:Hello:chart.html.twig` template import jQuery along with Highcharts JavaScript file:

```
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Highcharts Example</title>
        {% javascripts
            '@AcmeHelloBundle/Resources/public/js/jquery.js'
            '@AcmeHelloBundle/Resources/public/js/highcharts.js' %}
            <script type="text/javascript" src="{{ asset_url }}"></script>
        {% endjavascripts %}
    </head>
    <body>
        <!-- Chart options and initialization -->
        <div id="chart-container"></div>
    </body>
</html>
```

Note that jQuery library is only needed for creating the chart after the DOM is ready.

Finally initialize the chart:

```
<!-- Chart options and initialization -->
<script type="text/javascript">
    Highcharts.setOptions({% render 'gremo_highcharts:getOptions' %});

    $(function() {
        $(document).ready(function() {
            new Highcharts.Chart({{ chart|raw }});
        });
    })(jQuery);;
</script>
```

You can omit `Highcharts.setOptions()` if you didn't used any options provider.

## Limitations
Since JavaScript closures cannot be serialized, it's not possible to define properties as callbacks directly using this
library (e.g. when you need to customize [tooltips formatters](http://api.highcharts.com/highcharts#tooltip.formatter)).

This has to be done directly in JavaScript:

```
<script type="text/javascript">
    // ...

    $(function() {
        $(document).ready(function() {
            var options = {{ chart|raw }};

            options.tooltip = {
                formatter: function() {
                    return 'The value for <b>'+ this.x + '</b> is <b>'+ this.y +'</b>';
                }
            };

            new Highcharts.Chart(options);
        });
    })(jQuery);
</script>
```

## Planned features
- Add an options provider for loading options from a JSON file (i.e. Highcharts themes, JSON file)
- Find out a better way for printing options and charts (a Twig extension maybe)
- Add helper methods for multiple axes and combined charts
- Add a building system for defining reusable chart templates
