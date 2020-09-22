<?php

use App\Models\ValueType;
use App\Models\MaritalStatus;
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
            'blank' => 'BLANK VALUE'
        ];

        foreach ($types as $key => $value) {
            factory(ValueType::class)->create([
                'id' => $key,
                'name' => $value,
            ]);
        }
    }
}
