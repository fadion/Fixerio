<?php

use Mockery as m;
use Fadion\Fixerio\Exchange;

class ExchangeTest extends PHPUnit_Framework_TestCase {

    private $url = 'http://api.fixer.io';

    public function tearDown()
    {
        m::close();
    }

    public function testDefaultBase()
    {
        $url = (new Exchange())->getUrl();
        $expected = $this->url.'/latest?base=EUR';

        $this->assertEquals($url, $expected);
    }

    public function testBase()
    {
        $url = (new Exchange())->base('USD')->getUrl();
        $expected = $this->url.'/latest?base=USD';

        $this->assertEquals($url, $expected);
    }

    public function testSymbols()
    {
        $url = (new Exchange())->symbols('USD', 'GBP')->getUrl();
        $expected = $this->url.'/latest?base=EUR&symbols=USD,GBP';

        $this->assertEquals($url, $expected);
    }

    public function testSymbolsAsArray()
    {
        $url = (new Exchange())->symbols(['USD', 'GBP'])->getUrl();
        $expected = $this->url.'/latest?base=EUR&symbols=USD,GBP';

        $this->assertEquals($url, $expected);
    }

    public function testEmptySymbols()
    {
        $url = (new Exchange())->symbols()->getUrl();
        $expected = $this->url.'/latest?base=EUR';

        $this->assertEquals($url, $expected);
    }

    public function testSecure()
    {
        $url = (new Exchange())->secure()->getUrl();
        $expected = str_replace('http', 'https', $this->url).'/latest?base=EUR';

        $this->assertEquals($url, $expected);
    }

    public function testHistorical()
    {
        $date = '2012-12-12';
        $url = (new Exchange())->historical($date)->getUrl();
        $expected = $this->url.'/'.$date.'?base=EUR';

        $this->assertEquals($url, $expected);
    }

    public function testFullExample()
    {
        $url = (new Exchange())->secure()->base('USD')->symbols('EUR', 'GBP')->getUrl();
        $expected = str_replace('http', 'https', $this->url).'/latest?base=USD&symbols=EUR,GBP';

        $this->assertEquals($url, $expected);
    }

    public function testResponse()
    {
        $client = m::mock('GuzzleHttp\Client');
        $client->shouldReceive('request')->once()->andReturn(new ClientResponse);

        $exchange = new Exchange($client);
        $exchange->symbols('GBP', 'USD');

        $rates = $exchange->get();
        $expected = ['GBP', 'USD'];

        $this->assertEquals($rates, $expected);
    }

    /**
     * @expectedException Fadion\Fixerio\Exceptions\ResponseException
     * @expectedExceptionMessage Some error message
     */
    public function testResponseException()
    {
        $client = m::mock('GuzzleHttp\Client');
        $client->shouldReceive('request')->once()->andReturn(new InvalidClientResponse);

        $exchange = new Exchange($client);

        $exchange->get();
    }
    
}

class ClientResponse {

    public function getBody()
    {
        return json_encode(['rates' => ['GBP', 'USD']]);
    }

}

class InvalidClientResponse {

    public function getBody()
    {
        return json_encode(['error' => 'Some error message']);
    }

}