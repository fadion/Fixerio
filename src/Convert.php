<?php

namespace Fadion\Fixerio;

use DateTime;
use Fadion\Fixerio\Exceptions\ConnectionException;
use Fadion\Fixerio\Exceptions\ResponseException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\TransferException;

class Convert
{
    /**
     * Guzzle client
     * @var GuzzleHttp\Client
     */
    private $guzzle;

    /**
     * URL of fixer.io
     * @var string
     */
    private $url = "data.fixer.io/api";

    /**
     * Http or Https
     * @var string
     */
    private $protocol = 'http';

    /**
     * Holds whether the response should be
     * an object or not
     * @var array
     */
    private $asObject = false;

    /**
     * Holds the Fixer.io API key
     *
     * @var null|string
     */
    private $key = null;

    /**
     * @param $guzzle Guzzle client
     */
    public function __construct($from, $to, $amount, $guzzle = null)
    {
        if (isset($guzzle)) {
            $this->guzzle = $guzzle;
        } else {
            $this->guzzle = new GuzzleClient();
        }
        
        $this->from = $from;
        $this->to = $to;
        $this->amount = $amount;
        
    }

    /**
     * Sets the protocol to https
     *
     * @return Exchange
     */
    public function secure()
    {
        $this->protocol = 'https';

        return $this;
    }

    /**
     * Sets the API key
     *
     * @param  string $key
     * @return Exchange
     */
    public function key($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Returns the correctly formatted url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->buildUrl($this->url);
    }

    /**
     * Makes the request and returns the response
     * with the rates.
     *
     * @throws ConnectionException if the request is incorrect or times out
     * @throws ResponseException if the response is malformed
     * @return array
     */
    public function get()
    {
        $url = $this->buildUrl($this->url);

        try {
            $response = $this->makeRequest($url);

            return $this->prepareResponse($response);
        }
        // The client needs to know only one exception, no
        // matter what exception is thrown by Guzzle
         catch (TransferException $e) {
            throw new ConnectionException($e->getMessage());
        }
    }

    /**
     * Makes the request and returns the response
     * with the rates, as a Result object
     *
     * @throws ConnectionException if the request is incorrect or times out
     * @throws ResponseException if the response is malformed
     * @return Result
     */
    public function getResult()
    {
        $url = $this->buildUrl($this->url);

        try {
            $response = $this->makeRequest($url);

            return $this->prepareResponseResult($response);
        }
        // The client needs to know only one exception, no
        // matter what exception is thrown by Guzzle
         catch (TransferException $e) {
            throw new ConnectionException($e->getMessage());
        }
    }

    /**
     * Alias of get() but returns an object
     * response.
     *
     * @throws ConnectionException if the request is incorrect or times out
     * @throws ResponseException if the response is malformed
     * @return object
     */
    public function getAsObject()
    {
        $this->asObject = true;

        return $this->get();
    }

    /**
     * Forms the correct url from the different parts
     *
     * @param  string $url
     * @return string
     */
    private function buildUrl($url)
    {
        $url = $this->protocol . '://' . $url . '/convert';
        
        if ($this->key) {
            $url .= '?access_key=' . $this->key;
        }
        
        if ($this->from) {
            $url .= '&from=' . $this->from;
        }
            
        if ($this->to) {
            $url .= '&to=' . $this->to;
        }
        
        if ($this->amount) {
            $url .= '&amount=' . $this->amount;
        }

        return $url;
    }

    /**
     * Makes the http request
     *
     * @param  string $url
     * @return string
     */
    private function makeRequest($url)
    {
        $response = $this->guzzle->request('GET', $url);

        return $response->getBody();
    }

    /**
     * @param  string $body
     * @throws ResponseException if the response is malformed
     * @return array
     */
    private function prepareResponse($body)
    {
        $response = json_decode($body, true);

        if (isset($response['result'])) {
            return ($this->asObject) ? (object) $response['result'] : $response['result'];
        } else if (isset($response['error'])) {
            throw new ResponseException($response['error']);
        } else {
            throw new ResponseException('Response body is malformed.');
        }
    }

    /**
     * @param  string $body
     * @throws ResponseException if the response is malformed
     * @return Result
     */
    private function prepareResponseResult($body)
    {
        $response = json_decode($body, true);

        if (isset($response['query']) and isset($response['result'])) {
            return new Result(
                $response['base'],
                new DateTime($response['date']),
                $response['rates']
            );
        } else if (isset($response['error'])) {
            throw new ResponseException($response['error']);
        } else {
            throw new ResponseException('Response body is malformed.');
        }
    }

}
