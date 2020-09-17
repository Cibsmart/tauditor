<?php

use App\Models\QualificationType;
use Illuminate\Database\Seeder;

class QualificationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            'FSCL' => 'FIRST SCHOOL LEAVING CERTIFICATE',
            'SSCE' => 'SENIOR SECONDARY CERTIFICATE EXAMINATION',
            'ND' => 'NATIONAL DIPLOMA',
            'HND' => 'HIGHER NATIONAL DIPLOMA',
            'PGD' => 'POST GRADUATE DIPLOMA',
            'BACHELOR' => 'BACHELORS DEGREE',
            'MASTER' => 'MASTERS DEGREE',
            'PHD' => 'DOCTORATE DEGREE',
        ];

        foreach ($types as $code => $name) {
            factory(QualificationType::class)->create([
                'id' => $code,
                'name' => $name,
            ]);
        }
    }
}
