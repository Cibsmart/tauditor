<?php

namespace Tests\Feature;

use StateSeeder;
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

        $this->seed(StateSeeder::class);

        $this->assertFalse(false);
    }
}
