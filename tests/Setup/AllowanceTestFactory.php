<?php


namespace Tests\Setup;


use App\Allowance;
use Faker\Generator;
use function factory;

class AllowanceTestFactory
{
    /**
     * @var Generator
     */
    private Generator $faker;

    public function __construct(Generator $faker)
    {
        $this->faker = $faker;
    }

    public function create($attributes = [])
    {
        \Facades\AllowanceFactory::clearResolvedInstance('AllowanceTestFactory');

        $allowance = factory(Allowance::class)->create();
    }
}
