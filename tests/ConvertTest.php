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
        $converter = new Convert();
        $converter->from('USD');
        $converter->to('CAD');
        $converter->amount('22.50');
        $url = $converter->getUrl();
        $expected = $this->url . '/convert&from=USD&to=CAD&amount=22.50';
        
        $this->assertEquals($url, $expected);
    }

    public function testSecure()
    {
        $converter = new Convert();
        $converter->from('USD');
        $converter->to('CAD');
        $converter->amount('22.50');
        $converter->secure();
        $url = $converter->getUrl();
        $expected = str_replace('http', 'https', $this->url).'/convert&from=USD&to=CAD&amount=22.50';

        $this->assertEquals($url, $expected);
    }

    public function testResponse()
    {
        $response = m::mock('StdClass');
        $response->shouldReceive('getBody')->once()->andReturn(json_encode(['result' => 47.3685]));

        $client = m::mock('GuzzleHttp\Client');
        $client->shouldReceive('request')->once()->andReturn($response);

        $converter = new Convert($client);
        $converter->from('USD');
        $converter->to('CAD');
        $converter->amount('22.50');
        $actual = $converter->get();
        $expected = 47.3685;
        
        $this->assertEquals($actual, $expected);
    }
    
    public function testResponseAsObject()
    {
        $response = m::mock('StdClass');
        $response->shouldReceive('getBody')->once()->andReturn(json_encode(['result' => 47.3685]));

        $client = m::mock('GuzzleHttp\Client');
        $client->shouldReceive('request')->once()->andReturn($response);

        $converter = new Convert($client);
        $converter->from('USD');
        $converter->to('CAD');
        $converter->amount('22.50');
        $actual = $converter->getAsObject();
        $expected = (object) 47.3685;

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

        $converter = new Convert($client);
        $converter->get();
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

        $converter = new Convert($client);

        $converter->getResult();
    }
}
