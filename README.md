# quickchart-php
[![Packagist VERSION](http://img.shields.io/packagist/v/ianw/quickchart.svg?style=flat)](https://packagist.org/packages/ianw/quickchart)

A PHP client for the [quickchart.io](https://quickchart.io/) chart image API.

# Installation

Use the `QuickChart.php` library in this project, or [install from packagist](https://packagist.org/packages/ianw/quickchart).

```
composer require ianw/quickchart
```

# Usage

This library provides a `QuickChart` class.  Import and instantiate it.  Then set properties on it and specify a [Chart.js](https://chartjs.org) config:

```php
$chart = new QuickChart(array(
  'width' => 500,
  'height' => 300
));

$chart->setConfig('{
  type: "bar",
  data: {
    labels: ["Hello world", "Test"],
    datasets: [{
      label: "Foo",
      data: [1, 2]
    }]
  }
}');
```

Use `getUrl()` on your QuickChart object to get the encoded URL that renders your chart:

```php
echo $chart->getUrl();
// https://quickchart.io/chart?c=%7B%22type%22%3A%22bar%22%2C%22data%22%3A%7B%22labels%22%3A%5B%22Hello+world%22%2C%22Test%22%5D%2C%22datasets%22%3A%5B%7B%22label%22%3A%22Foo%22%2C%22data%22%3A%5B1%2C2%5D%7D%5D%7D%7D&w=500&h=300
```

If you have a long or complicated chart, use `getShortUrl()` to get a fixed-length URL using the quickchart.io web service (note that these URLs only persist for a short time unless you have a subscription):

```php
echo $chart->getShortUrl();
// https://quickchart.io/chart/render/f-a1d3e804-dfea-442c-88b0-9801b9808401
```

The URLs will render an image of a chart:

<img src="https://quickchart.io/chart?c=%7B%22type%22%3A+%22bar%22%2C+%22data%22%3A+%7B%22labels%22%3A+%5B%22Hello+world%22%2C+%22Test%22%5D%2C+%22datasets%22%3A+%5B%7B%22label%22%3A+%22Foo%22%2C+%22data%22%3A+%5B1%2C+2%5D%7D%5D%7D%7D&w=600&h=300&bkg=%23ffffff&devicePixelRatio=2.0&f=png" width="500" />

## Creating the chart object

The `QuickChart` class constructor accepts an array containing the following keys.  All are optional and can be set after object creation:

### config: array or string
The actual Chart.js chart configuration.

### width: int
Width of the chart image in pixels.  Defaults to 500

### height: int
Height of the chart image  in pixels.  Defaults to 300

### format: string
Format of the chart. Defaults to png.

### backgroundColor: string
The background color of the chart. Any valid HTML color works. Defaults to #ffffff (white). Also takes rgb, rgba, and hsl values.

### devicePixelRatio: float
The device pixel ratio of the chart. This will multiply the number of pixels by the value. This is usually used for retina displays. Defaults to 1.0.

### apiKey: string
Your QuickChart API key, if you have one.

## Setting properties

Each option above has an associated function call that you can invoke on your `QuickChart` object:

 - `setConfig($config)`
 - `setWidth($width)`
 - `setHeight($height)`
 - `setFormat($format)`
 - `setBackgroundColor($backgroundColor)`
 - `setDevicePixelRatio($devicePixelRatio)`
 - `setApiKey($apiKey)`

## Getting URLs

There are two ways to get a URL for your chart object.

### getUrl: string

Returns a URL that will display the chart image when loaded.

### getShortUrl: string

Uses the quickchart.io web service to create a fixed-length chart URL that displays the chart image.  Returns a URL such as `https://quickchart.io/chart/render/f-a1d3e804-dfea-442c-88b0-9801b9808401`.

Note that short URLs expire after a few days for users of the free service.  You can [subscribe](https://quickchart.io/pricing/) to keep them around longer.

## Other outputs

### toBinary: binary string

Returns a binary string representing the chart image

### toFile($path: string)

Write the image to a file

For example:
```php
$chart->toFile('/tmp/myfile.png')
```

## More examples

Checkout the `examples` directory to see other usage.

## Troubleshooting

**PHP5 users**: This package requires curl and json modules.

**sslv3 handshake alert failure**: You are using an outdated version a `curl`.  Please upgrade curl on your machine.
