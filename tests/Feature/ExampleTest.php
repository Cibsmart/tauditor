<?php

namespace Tests\Feature;

use Carbon\Carbon;
use AllowanceSeeder;
use Tests\TestCase;
use BeneficiarySeeder;
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

        $time = Carbon::now()->months(5);

        dd($time);

        $this->assertFalse(false);
    }
}
