<?php


namespace Tests\Setup;


use App\Deduction;
use Faker\Generator;
use function factory;

class DeductionFactory
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
        \Facades\DeductionFactory::clearResolvedInstance('DeductionFactory');

        $allowance = factory(Deduction::class)->create();

    }
}
