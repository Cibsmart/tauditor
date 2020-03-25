<?php

namespace Tests\Feature;

use AllowanceSeeder;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

        $this->seed(AllowanceSeeder::class);

        $this->assertFalse(false);
    }
}
