<?php

namespace Tests\Feature;

use MdaSeeder;
use StateSeeder;
use MicroFinanceBankSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {

        $this->seed();

        $this->assertFalse(false);
    }
}
