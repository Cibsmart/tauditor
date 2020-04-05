<?php


namespace Tests\Setup;


use App\Beneficiary;
use function factory;

class BeneficiaryFactory
{
    public function create($override = [])
    {
        $beneficiary = factory(Beneficiary::class)->create($override);
    }
}
