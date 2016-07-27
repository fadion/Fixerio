<?php

use Fadion\Fixerio\Currency;
use Fadion\Fixerio\Result;

class ResultTest extends PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        $rates = [
            Currency::USD => 1.23,
            Currency::GBP => 1.01,
        ];
        $base = 'EUR';
        $date = new DateTime('2016-01-02');

        $result = new Result($base, $date, $rates);

        $this->assertEquals($rates, $result->getRates());
        $this->assertEquals($base, $result->getBase());
        $this->assertEquals($date, $result->getDate());

        $this->assertEquals(1.23, $result->getRate(Currency::USD));
        $this->assertEquals(1.01, $result->getRate(Currency::GBP));

        // Check that asking for the Base will return 1
        $this->assertEquals(1, $result->getRate(Currency::EUR));

        // Null if currency not in result
        $this->assertNull($result->getRate(Currency::HKD));
        $this->assertNull($result->getRate('invalid'));
    }
}
