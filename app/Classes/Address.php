<?php

namespace App\Classes;

class Address
{
    public $address_line_1;

    public $address_line_2;

    public $address_city;

    public $address_state;

    public $address_country;

    /**
     * Address constructor.
     */
    public function __construct($address_line_1, $address_line_2, $address_city, $address_state, $address_country)
    {
        $this->address_line_1 = $address_line_1;
        $this->address_line_2 = $address_line_2;
        $this->address_city = $address_city;
        $this->address_state = $address_state;
        $this->address_country = $address_country;
    }
}
