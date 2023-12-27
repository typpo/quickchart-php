<?php

$USER_AGENT = 'quickchart-php (1.0.0)';

class QuickChart {
  public $protocol;
  public $host;
  public $port;

  public $config;
  public $width;
  public $height;
  public $devicePixelRatio;
  public $format;
  public $backgroundColor;
  public $apiKey;
  public $version;

  function __construct($options = array()) {
    $this->protocol = isset($options['protocol']) ? $options['protocol'] : 'https';
    $this->host = isset($options['host']) ? $options['host'] : 'quickchart.io';
    $this->port = isset($options['port']) ? $options['port'] : 443;
    $this->width = isset($options['width']) ? $options['width'] : 500;
    $this->height = isset($options['height']) ? $options['height'] : 300;
    $this->devicePixelRatio = isset($options['devicePixelRatio']) ? $options['devicePixelRatio'] : 1.0;
    $this->format = isset($options['format']) ? $options['format'] : 'png';
    $this->backgroundColor = isset($options['backgroundColor']) ? $options['backgroundColor'] : 'transparent';
    $this->apiKey = isset($options['apiKey']) ? $options['apiKey'] : null;
    $this->version = isset($options['version']) ? $options['version'] : null;
  }

  function setConfig($chartjsConfig) {
    $this->config = $chartjsConfig;
  }

  function setWidth($width) {
    $this->width = $width;
  }

  function setHeight($height) {
    $this->height = $height;
  }

  function setDevicePixelRatio($devicePixelRatio) {
    $this->devicePixelRatio = $devicePixelRatio;
  }

  function setFormat($format) {
    $this->format = $format;
  }

  function setBackgroundColor($backgroundColor) {
    $this->backgroundColor = $backgroundColor;
  }

  function setApiKey($apiKey) {
    $this->apiKey = $apiKey;
  }

  function setVersion($version) {
    $this->version = $version;
  }

  function getConfigStr() {
    if (is_array($this->config)) {
      return json_encode($this->config);
    }
    return $this->config;
  }

  function getUrl() {
    $configStr = urlencode($this->getConfigStr());
    $width = $this->width;
    $height = $this->height;
    $devicePixelRatio = number_format($this->devicePixelRatio, 1);
    $format = $this->format;
    $backgroundColor = $this->backgroundColor;

    $url = sprintf($this->getRootEndpoint() . '/chart?ref=qc-php&c=%s&w=%d&h=%d&devicePixelRatio=%f&format=%s&bkg=%s', $configStr, $width, $height, $devicePixelRatio, $format, $backgroundColor);

    if ($this->apiKey) {
      $url .= '&key=' . $this->apiKey;
    }

    if ($this->version) {
      $url .= '&v=' . $this->version;
    }

    return $url;
  }

  function getShortUrl() {
    if ($this->host != 'quickchart.io') {
      throw new Exception('Short URLs must use quickchart.io host');
    }
    $ch = curl_init($this->getRootEndpoint() . '/chart/create');
    $postData = array(
      'backgroundColor' => $this->backgroundColor,
      'width' => $this->width,
      'height' => $this->height,
      'devicePixelRatio' => number_format($this->devicePixelRatio, 1),
      'format' => $this->format,
      'chart' => $this->getConfigStr(),
    );
    if ($this->apiKey) {
      $postData['key'] = $this->apiKey;
    }
    if ($this->version) {
      $postData['version'] = $this->version;
    }
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type:application/json',
      "User-Agent:$USER_AGENT",
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);

    if ($result === false) {
      throw new Exception(curl_error($ch), curl_errno($ch));
    }

    curl_close($ch);
    // Note: do not dereference json_decode directly for 5.3 compatibility.
    $ret = json_decode($result, true);
    return $ret['url'];
  }

  function toBinary() {
    $ch = curl_init($this->getRootEndpoint() . '/chart');
    $postData = array(
      'backgroundColor' => $this->backgroundColor,
      'devicePixelRatio' => $this->devicePixelRatio,
      'width' => $this->width,
      'height' => $this->height,
      'format' => $this->format,
      'chart' => $this->getConfigStr(),
    );
    if ($this->apiKey) {
      $postData['key'] = $this->apiKey;
    }
    if ($this->version) {
      $postData['version'] = $this->version;
    }
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($result === false) {
      $error = curl_error($ch);
      curl_close($ch);
      throw new Exception("Curl error: $error");
    }

    curl_close($ch);

    if ($httpStatusCode >= 200 && $httpStatusCode < 300) {
      return $result;
    }

    // Parse response headers
    $responseHeaders = [];
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headerStr = substr($result, 0, $headerSize);
    foreach (explode("\r\n", $headerStr) as $i => $line) {
      if ($i === 0) {
        $responseHeaders['http_code'] = $line;
      } else {
        list($key, $value) = explode(': ', $line);
        $responseHeaders[$key] = $value;
      }
    }

    $errorHeader = isset($responseHeaders['X-quickchart-error']) ? $responseHeaders['X-quickchart-error'] : null;
    if ($errorHeader) {
      throw new Exception("QuickChart API returned an error with status code: $httpStatusCode. Error: $errorHeader");
    }

    throw new Exception("QuickChart API returned an error with status code: $httpStatusCode. Response: $result");
  }

  function toFile($path) {
    $data = $this->toBinary();
    file_put_contents($path, $data);
  }

  protected function getRootEndpoint() {
    return $this->protocol . '://' . $this->host . ':' . $this->port;
  }
}


