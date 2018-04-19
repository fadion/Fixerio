<?php

use Fadion\Fixerio\Currency;
use Mockery as m;
use Fadion\Fixerio\Convert;

class ConvertTest extends PHPUnit_Framework_TestCase
{
    private $url = 'http://data.fixer.io/api';

    public function tearDown()
    {
        m::close();
    }

    public function testParams()
    {
        $url = (new Convert('USD', 'CAD', '22.50'))->getUrl();
        $expected = $this->url . '/convert&from=USD&to=CAD&amount=22.50';

        $this->assertEquals($url, $expected);
    }

    public function testSecure()
    {
        $url = (new Convert('USD', 'CAD', '22.50'))->secure()->getUrl();
        $expected = str_replace('http', 'https', $this->url).'/convert&from=USD&to=CAD&amount=22.50';

        $this->assertEquals($url, $expected);
    }

    public function testResponse()
    {
        $response = m::mock('StdClass');
        $response->shouldReceive('getBody')->once()->andReturn(json_encode(['result' => 47.3685]));

        $client = m::mock('GuzzleHttp\Client');
        $client->shouldReceive('request')->once()->andReturn($response);

        $convert = new Convert(Currency::USD, Currency::CAD, '37.50', $client);
        $expected = 47.3685;
        $actual = $convert->get();

        $this->assertEquals($actual, $expected);
    }
    
    public function testResponseAsObject()
    {
        $response = m::mock('StdClass');
        $response->shouldReceive('getBody')->once()->andReturn(json_encode(['result' => 47.3685]));

        $client = m::mock('GuzzleHttp\Client');
        $client->shouldReceive('request')->once()->andReturn($response);

        $convert = new Convert(Currency::USD, Currency::CAD, '37.50', $client);
        $expected = (object) 47.3685;
        $actual = $convert->getAsObject();

        $this->assertEquals($actual, $expected);
    }

    /**
     * @expectedException Fadion\Fixerio\Exceptions\ResponseException
     * @expectedExceptionMessage Some error message
     */
    public function testResponseException()
    {
        $response = m::mock('StdClass');
        $response->shouldReceive('getBody')->once()->andReturn(json_encode(['error' => 'Some error message']));

        $client = m::mock('GuzzleHttp\Client');
        $client->shouldReceive('request')->once()->andReturn($response);

        $convert = new Convert(Currency::USD, Currency::CAD, '37.50', $client);

        $convert->get();
    }

    /**
     * @expectedException Fadion\Fixerio\Exceptions\ResponseException
     * @expectedExceptionMessage Some error message
     */
    public function testResponseResultException()
    {
        $response = m::mock('StdClass');
        $response->shouldReceive('getBody')->once()->andReturn(json_encode(['error' => 'Some error message']));

        $client = m::mock('GuzzleHttp\Client');
        $client->shouldReceive('request')->once()->andReturn($response);

        $convert = new Convert(Currency::USD, Currency::CAD, '37.50', $client);

        $convert->getResult();
    }
}
