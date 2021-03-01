<?php

namespace InfluxDB2;

use InvalidArgumentException;

abstract class DefaultApi
{
    const DEFAULT_TIMEOUT = 10;
    public $options;
    public $http;
    /**
     * Holds GuzzleHttp timeout.
     *
     * @var int
     */
    private $timeout;
    /**
     * DefaultApi constructor.
     * @param $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
        $this->timeout = $this->options['timeout'] ?? self::DEFAULT_TIMEOUT;
    }

    /**
     * @param $payload
     * @param $uriPath
     * @param $queryParams
     * @param int $timeout - Float describing the timeout of the request in seconds. Use 0 to wait indefinitely (the default behavior).
     * @param bool $stream - use streaming
     * @return string response body
     */
    public function post($payload, $uriPath, $queryParams, $timeout = null, bool $stream = false): string
    {
        return $this->request($payload, $uriPath, $queryParams, 'POST', $timeout, $stream);
    }

    public function get($payload, $uriPath, $queryParams, $timeout = null): string
    {
        return $this->request($payload, $uriPath, $queryParams, 'GET', $timeout, false);
    }

    protected abstract function setUpClient();

    protected abstract function request($payload, $uriPath, $queryParams, $method, $timeout = null, bool $stream = false): string;

    protected function check($key, $value)
    {
        if ((!isset($value) || trim($value) === '')) {
            $options = implode(', ', array_map(
                function ($v, $k) {
                    if (is_array($v)) {
                        return $k.'[]='.implode('&'.$k.'[]=', $v);
                    } else {
                        return $k.'='.$v;
                    }
                },
                $this->options,
                array_keys($this->options)
            ));
            throw new InvalidArgumentException("The '${key}' should be defined as argument or default option: {$options}");
        }
    }
}
