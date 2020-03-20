<?php

use App\Relationship;
use Illuminate\Database\Seeder;

class RelationshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $relationships = ['SON', 'DAUGHTER', 'WIFE', 'HUSBAND', 'FRIEND', 'UNCLE', 'BROTHER', 'SISTER', 'AUNT', 'KINDRED', 'OTHERS'];

        foreach ($relationships as $relationship) {
            factory(Relationship::class)->create([
                'name' => $relationship,
            ]);
        }
    }
}
