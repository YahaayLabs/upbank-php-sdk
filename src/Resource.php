<?php

namespace YahaayLabs\UpBank;

use YahaayLabs\UpBank\Client;
use YahaayLabs\UpBank\Http\Request;
use YahaayLabs\UpBank\Http\Response;
use GuzzleHttp\Exception\GuzzleException;

class Resource
{

    /**
     * @var Client
     */
    private $client;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Response
     */
    private $response;

    /**
     * Create and return a new Resource
     *
     * @param Client $client
     * @param bool $request
     */
    public function __construct(Client $client, $request = false)
    {
        $this->client = $client;
        $this->request = $request ? $request : new Request;

        return $this;
    }

    /**
     *
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    public function setAuthenticationHeaders()
    {
        $this->request->addHeaders(
            [
                'Authorization' => "Bearer {$this->client->getAPIKey()}"
            ]
        );

        return $this;
    }

    /**
     *  Make a call to the API
     *
     * @param  String $method    request method to use GET|POST|PUT|PATCH|DELETE
     * @param  bool   $body      any body data to send with the request
     * @param  bool   $uriAppend any additional URI componenents as a string (eg 'relationships/categories')
     * @param  array  $headers   any specific headers for the request
     * @return Response
     * @throws InvalidRequestMethodException
     * @throws GuzzleException
     */
    public function call(String $method, $body = false, $uriAppend = false, $headers = [])
    {        
        $this->setAuthenticationHeaders();
        $url = $this->client->getAPIEndpoint($this->uri);

        if ($uriAppend) {
            $url = $url . '/' . $uriAppend;
        }

        $request = clone $this->request;

        $request->setURL($url)
            ->setMethod($method)
            ->addHeaders($headers)
            ->setBody($body);

        return $request->make()->getResponse();
    }

    /**
     *  Adds specific request headers to an array to be passed to the request
     *
     */
    public function addRequestHeaders(array $headers): array
    {
        $this->request->addHeaders($headers);
 
        return $headers;
    }

    /**
     * Getting all records of the resourse
     */
    public function all()
    {
        return $this->call('get', false);
    }

    /**
     * Getting specicic row of a resource
     */
    public function get(String $id)
    {
        return $this->call('get', false, $id);
    }
}