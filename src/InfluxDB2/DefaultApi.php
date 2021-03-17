<?php

namespace InfluxDB2;

use InvalidArgumentException;

abstract class DefaultApi
{
    const DEFAULT_TIMEOUT = 10;
    public $options;

    /**
     * DefaultApi constructor.
     * @param $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;

        $this->setUpClient();
    }

    /**
     * @param string|null $payload
     * @param string $uriPath
     * @param array $queryParams
     * @param int $timeout - Float describing the timeout of the request in seconds. Use 0 to wait indefinitely (the default behavior).
     * @param bool $stream - use streaming
     * @return string response body
     */
    public function post($payload, $uriPath, $queryParams, $timeout = null, bool $stream = false): string
    {
        return $this->request($payload, $uriPath, $queryParams, 'POST', $timeout, $stream);
    }

    /**
     * @param string|null $payload
     * @param string    $uriPath
     * @param array     $queryParams
     * @param int $timeout
     * @return string
     */
    public function get($payload, $uriPath, $queryParams, $timeout = null): string
    {
        return $this->request($payload, $uriPath, $queryParams, 'GET', $timeout, false);
    }

    abstract protected function setUpClient();

    /**
     * @param string|null $payload
     * @param string $uriPath
     * @param array $queryParams
     * @param string $method
     * @param int  $timeout
     * @param bool $stream
     * @return string
     */
    abstract protected function request($payload, $uriPath, $queryParams, $method, $timeout = null, bool $stream = false): string;

    public function check($key, $value)
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
