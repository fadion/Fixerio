<?php

use Fadion\Fixerio\ExchangeServiceProvider;
use Fadion\Fixerio\Facades\Exchange as Facade;

class LaravelIntegrationTest extends \PHPUnit\Framework\TestCase
{
    public function testServiceProviderProvidesFacadeAccessor()
    {
        $provides = (new ExchangeServiceProvider(null))->provides();

        $app = array_combine($provides, $provides);

        Facade::setFacadeApplication($app);

        $this->assertContains(Facade::getFacadeRoot(), $provides);
    }
}
