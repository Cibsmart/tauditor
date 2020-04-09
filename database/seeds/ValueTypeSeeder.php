<?php

use App\ValueType;
use App\MaritalStatus;
use Illuminate\Database\Seeder;

class ValueTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            'computed' => 'COMPUTED VALUE',
            'fixed' => 'FIXED VALUE',
            'percentage' => 'PERCENTAGE VALUE',
        ];

        foreach ($types as $key => $value) {
            factory(ValueType::class)->create([
                'code' => $key,
                'name' => $value,
            ]);
        }
    }
}
