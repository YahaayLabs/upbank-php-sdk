<?php

declare(strict_types=1);

namespace  YahaayLabs\UpBank;

use YahaayLabs\UpBank\Exceptions\InvalidResourceException;
use Carbon\Carbon;
use GuzzleHttp\Client as HttpClient;

/**
 * Class Client
 * 
 */
class Client
{
    /**
     * @var HttpClient $httpClient;
     */
    private $httpClient;

    /**
     * @var string $apiKey
     */
    protected $apiKey;

    /**
     * @var string $apiURL
     */
    protected $apiURL;

    /**
     * @var string Extra Guzzle Requests Options
     */
    protected $extraGuzzleRequestsOptions;

    /**
     * API Base Endpoint
     *
     * @var String
     */
    private $base = "https://api.up.com.au/api/v1";

    public function __construct(string $apiKey = "")
    {

        if (isset($apiKey)) {
            $this->setAPIKey($apiKey);
        }
    }

    public function setAPIKey(string $apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    public function getAPIKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Magic method for calling Resources
     *
     * @param  $method
     * @return mixed
     * @throws YahaayLabs\UpBank\Exceptions\InvalidResourceException
     */
    public function __get($method)
    {
        $targetResourceClass = 'YahaayLabs\\UpBank\\Resources\\' . ucfirst($method);

        if (class_exists($targetResourceClass)) {
            // construct a resource object and pass in this client
            $resource = new $targetResourceClass($this);
            
            return $resource;
        }

        $trace = debug_backtrace();
        $message = 'Undefined property via __get(): ' . $method . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'];
        throw new InvalidResourceException($message);
    }

    /**
     * Get the API endpoint for calls
     *
     * @param  String $uri
     * @return String
     */
    public function getAPIEndpoint(String $uri): String
    {
        $endpoint = $this->getBase() . '/' . $uri;

        return $endpoint;
    }

    /**
     *  Get the base URL
     *
     * @return String
     */
    public function getBase(): String
    {
        return $this->base;
    }
}
