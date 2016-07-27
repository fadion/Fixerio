<?php

namespace Fadion\Fixerio;

use DateTime;

class Result
{
    /**
     * The Base currency the result was returned in
     * @var string
     */
    private $base;

    /**
     * The date the result was generated
     * @var DateTime
     */
    private $date;

    /**
     * All of the rates returned
     * @var array
     */
    private $rates;

    /**
     * Result constructor.
     *
     * @param string $base
     * @param DateTime $date
     * @param array $rates
     */
    public function __construct($base, DateTime $date, $rates)
    {
        $this->base = $base;
        $this->date = $date;
        $this->rates = $rates;
    }

    /**
     * @return string
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return array
     */
    public function getRates()
    {
        return $this->rates;
    }

    /**
     * Get an individual rate by Currency code
     * Will return null if currency is not found in the result
     *
     * @param string $code
     * @return float|null
     */
    public function getRate($code)
    {
        // the result won't have the base code in it,
        // because that would always be 1. But to make
        // dynamic code easier this prevents null if
        // the base code is asked for
        if ($code == $this->getBase()) {
            return 1.0;
        }

        if (isset($this->rates[$code])) {
            return $this->rates[$code];
        }
        return null;
    }
}