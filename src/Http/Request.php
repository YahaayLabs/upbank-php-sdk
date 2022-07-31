<?php

namespace YahaayLabs\UpBank\Http;

use YahaayLabs\UpBank\Client;
use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;

use YahaayLabs\UpBank\Exceptions\InvalidContentTypeException;
use YahaayLabs\UpBank\Exceptions\InvalidRequestMethodException;
use YahaayLabs\UpBank\Exceptions\InvalidResourceException;

class Request
{
    private $httpClient;

    private $method;

    private $url;

    private $headers = [];

    private $body = false;

    private $params = [];

    private $response;

    /**
     * Request constructor.
     *
     * @param  bool $client
     * @return $this
     */
    public function __construct($client = false)
    {
        $this->httpClient = $client ? $client : new HttpClient();

        return $this;
    }

    /**
     * @param  string $method the request method for the call
     * @return $this
     * @throws InvalidRequestMethodException
     * @throws InvalidRequestMethodException
     * @throws InvalidRequestMethodException
     */
    public function setMethod($method)
    {
        $method = strtoupper(trim($method));

        if (!in_array($method, ['GET','POST','PUT','PATCH','DELETE'])) {
            throw new InvalidRequestMethodException();
        }

        $this->method = $method;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod() : String
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getHeaders() : array
    {
        return $this->headers;
    }

    /**
     *  Get a specific header
     *
     * @param  string $name the header name
     * @return false|string false if the header is not set, otherwise the string value
     */
    private function getHeader($name)
    {
        if (isset($this->headers[$name])) {
            return $this->headers[$name];
        }
        return false;
    }

    /**
     *  Clear request headers
     *
     * @return $this
     */
    public function clearHeaders()
    {
        $this->headers = [];
        return $this;
    }
    
    /**
     *  Add headers to the request
     *
     * @param  array $headers an array of $name => $value headers to set
     * @return $this
     */
    public function addHeaders($headers)
    {
        foreach ($headers as $name => $value) {
            $this->addHeader($name, $value);
        }
        return $this;
    }

    /**
     *  Add (over overwrite) a single header to the request
     *
     * @param  string $name  the header name
     * @param  string $value the header value
     * @return $this
     */
    public function addHeader($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     *  Set the default request headers
     *
     * @return $this
     */
    private function setDefaultHeaders()
    {
        $defaultHeaders = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'User-Agent' =>  Client::UA, 
            'X-STATS-SDK-LANGUAGE' => 'php',
            'X-STATS-SDK-VERSION' => 'v1-dev'
        ];
        foreach ($defaultHeaders as $name => $value) {
            if (!$this->getHeader($name)) {
                $this->addHeader($name, $value);
            }
        }
        return $this;
    }

    /**
     *  Set the body of the request
     *
     * @param  array $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     *  Get the request body
     *
     * @return bool
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     *  Get the request URL
     *
     */
    public function getURL(): string
    {
        return $this->url;
    }

    /**
     *  Set the request URL
     *
     * @param  string $url the URL this request should hit
     * @return $this
     */
    public function setURL($url)
    {
        $this->url = trim($url);
        return $this;
    }

    /**
     * @return array
     * @throws InvalidContentTypeException
     */
    public function getPayload() : array
    {
        $payload = [];
        $body = $this->getBody();
        if (!empty($body)) {
            $payload[$this->getBodyKey()] = $body;
        }

        $payload['headers'] = $this->getHeaders();
        $params = $this->getQueryStringParams();
        if (!empty($params)) {
            $payload['query'] = $params;
        }

        return $payload;
    }

    /**
     *  Set the query string params
     *
     * @param  array $params
     * @return $this
     */
    public function setQueryStringParams($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     *  Get the query string params
     *
     * @return array
     */
    public function getQueryStringParams()
    {
        return $this->params;
    }

    /**
     *  Get the payload key for the body depending on whether the call is JSON/multipart
     *
     * @return string
     * @throws InvalidContentTypeException
     * @throws InvalidContentTypeException
     * @throws InvalidContentTypeException
     */
    public function getBodyKey()
    {
        switch ($this->getHeader('Content-Type')) {
        case 'application/json':
            return'json';
                break;
        default:
            throw new InvalidContentTypeException;
        }
    }

    /**
     * @return JustSteveKing\Stats\Http\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     *  Make a request
     *
     * @return $this
     * @throws Exception
     * @throws Exception
     */
    public function make()
    {
        $startTime = microtime(true);

        try {
            $result = $this->httpClient->request($this->getMethod(), $this->getURL(), $this->getPayload());
        } catch (GuzzleException $e) {
            throw new Exception($e->getMessage());
        }

        $endTime = microtime(true);
        $this->response = new Response();
        $this->response
            ->setExecutionTime(round(($endTime - $startTime), 5))
            ->setStatusCode($result->getStatusCode());

        // set the request ID for remote debugging if it is present
        if (!empty(($requestID = $result->getHeader('X-Stats-Request-Id')))) {
            $this->response->setRequestID($requestID[0]);
        }
        $body = json_decode($result->getBody());
        $this->response->setRaw($body)->parse();
        return $this;
    }
}