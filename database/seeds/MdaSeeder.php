<?php

use App\Mda;
use App\BeneficiaryType;
use Illuminate\Database\Seeder;

class MdaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = BeneficiaryType::all();

        $sco = [
            'ANCSC' => 'ANAMBRA STATE CIVIL SERVICE COMMISSION',
            'EXCO' => 'EXCO CHAMBERS',
        ];

        $pa = [
            'LGSC' => 'LOCAL GOVERNMENT SERVICE COMMISSION',
            'ANHA' => 'HONORABLE MEMBERS ANHA',
            'PAGH' => 'POLITICAL APPOINTEES GOVERNMENT HOUSE',
            'SSGC' => 'SSG AND COMMISSIONERS',
            'BEB' => 'BASIC EDUCATION BOARD',
            'PPSSC' => 'POST PRIMARY SCHOOL SERVICE COMMISSION'
        ];

        $cv = [
            'AGRIC' => 'AGRICULTURE AND NATURAL RESOURCES',
            'ANUTH' => 'ANAMBRA STATE UNIVERSITY TEACHING HOSPITAL',
            'BIR' => 'BOARD OF INTERNAL REVENUE',
            'CSC' => 'CIVIL SERVICE COMMISSION',
            'CIT' => 'COMMERCE INDUSTRY AND TOURISM',
            'DGOVO' => 'DEPUTY GOVERNOR OFFICE',
            'EPD' => 'ECONOMIC PLANNING AND DEVELOPMENT',
            'EDC' => 'EXAMINATIONS DEVELOPMENT CENTRE',
            'FINBUG' => 'FINANCE AND BUDGET',
            'GOVTP' => 'GOVERNMENT PRESS',
            'GOVO' => 'GOVERNORS OFFICE',
            'HSPS' => 'HEAD OF SERVICE AND PERMANENT SECRETARIES',
            'HA' => 'HOUSE OF ASSEMBLY',
            'HUD' => 'HOUSING AND URBAN DEVELOPMENT',
            'INFOCUL' => 'INFORMATION AND CULTURE',
            'JSC' => 'JUDICIAL SERVICE COMMISSION',
            'LAND' => 'LANDS SURVEY AND TOWN PLANNING',
            'LOA' => 'LIAISON OFFICE ABUJA',
            'LOL' => 'LIAISON OFFICE LAGOS',
            'LGCM' => 'LOCAL GOVERNMENT AND CHIEFTANCY MATTERS',
            'DIASPORA' => 'MINISTRY OF DIASPORA AFFAIRS',
            'EDUCATION' => 'MINISTRY OF EDUCATION',
            'ENVIRONMENT' => 'MINISTRY OF ENVIRONMENT',
            'HEALTH' => 'MINISTRY OF HEALTH',
            'JUSTICE' => 'MINISTRY OF JUSTICE',
            'TRANSPORT' => 'MINISTRY OF TRANSPORT',
            'WORKS' => 'MINISTRY OF WORKS',
            'AGLG' => 'OFFICE OF THE AUDITOR GENERAL FOR LOCAL GOVT',
            'OHOS' => 'OFFICE OF THE HEAD OF SERVICE',
            'OSSG' => 'OFFICE OF THE SSG.',
            'OSAG' => 'OFFICE OF THE STATE AUDITOR GENERAL',
            'PUTIL' => 'PUBLIC UTILITIES',
            'SCITECH' => 'SCIENCE AND TECHNOLOGY',
            'STATS' => 'STATE BUREAU OF STATISTICS',
            'SEC' => 'STATE EDUCATION COMMISSION',
            'SHMB' => 'STATE HOSPITAL MANAGEMENT BOARD',
            'WASD' => 'WOMEN AFFAIRS AND SOCIAL DEVELOPMENT',
            'SPORTS' => 'YOUTH AND SPORTS',
        ];

        $anpen = [
            'ABAGZN' => 'ABAGANA ZONE',
            'ACHAZN' => 'ACHALLA ZONE',
            'AGUAZN' => 'AGUATA ZONE',
            'AJALZN' => 'AJALI ZONE',
            'AWKAZN' => 'AWKA ZONE',
            'FEGEZN' => 'FEGGE ZONE',
            'IHIAZN' => 'IHIALA ZONE',
            'NENIZN' => 'NENI ZONE',
            'NNEWZN' => 'NNEWI ZONE',
            'NTEJZN' => 'NTEJE ZONE',
            'OGBAZN' => 'OGBARU ZONE',
            'OGIDZN' => 'OGIDI ZONE',
            'OJOTZN' => 'OJOTO ZONE',
            'ONITZN' => 'ONITSHA ZONE',
            'OTUOZN' => 'OTUOCHA ZONE',
            'OZUBZN' => 'OZUBULU ZONE',
            'UKPOZN' => 'UKPOR ZONE',
            'UMUNZN' => 'UMUNZE ZONE',
        ];

        $lgea = [
            'AGUAEA' => 'AGUATA LGEA',
            'ANETEA' => 'ANAMBRA EAST LGEA',
            'ANWTEA' => 'ANAMBRA WEST LGEA',
            'ANAOEA' => 'ANAOCHA LGEA',
            'ASUBEBEA' => 'ASUBEB HEADQUARTERS LGEA',
            'AWNTEA' => 'AWKA NORTH LGEA',
            'AWSTEA' => 'AWKA SOUTH LGEA',
            'AYAMEA' => 'AYAMELUM LGEA',
            'DUNUEA' => 'DUNUKOFIA LGEA',
            'EKWUEA' => 'EKWUSIGO LGEA',
            'IDNTEA' => 'IDEMILI NORTH LGEA',
            'IDSTEA' => 'IDEMILI SOUTH LGEA',
            'IHIAEA' => 'IHIALA LGEA',
            'NJIKEA' => 'NJIKOKA LGEA',
            'NWNTEA' => 'NNEWI NORTH LGEA',
            'NWSTEA' => 'NNEWI SOUTH LGEA',
            'OGBAEA' => 'OGBARU LGEA',
            'ONNTEA' => 'ONITSHA NORTH LGEA',
            'ONSTEA' => 'ONITSHA SOUTH LGEA',
            'ORNTEA' => 'ORUMBA NORTH LGEA',
            'ORSTEA' => 'ORUMBA SOUTH LGEA',
            'OYIEA' => 'OYI LGEA',
        ];

        $lgsc = [
            'AGUASC' => 'AGUATA LGSC',
            'ANETSC' => 'ANAMBRA EAST LGSC',
            'ANWTSC' => 'ANAMBRA WEST LGSC',
            'ANAOSC' => 'ANAOCHA LGSC',
            'AWNTSC' => 'AWKA NORTH LGSC',
            'AWSTSC' => 'AWKA SOUTH LGSC',
            'AYAMSC' => 'AYAMELUM LGSC',
            'DUNUSC' => 'DUNUKOFIA LGSC',
            'EKWUSC' => 'EKWUSIGO LGSC',
            'IDNTSC' => 'IDEMILI NORTH LGSC',
            'IDSTSC' => 'IDEMILI SOUTH LGSC',
            'IHIASC' => 'IHIALA LGSC',
            'NJIKSC' => 'NJIKOKA LGSC',
            'NWNTSC' => 'NNEWI NORTH LGSC',
            'NWSTSC' => 'NNEWI SOUTH LGSC',
            'OGBASC' => 'OGBARU LGSC',
            'ONNTSC' => 'ONITSHA NORTH LGSC',
            'ONSTSC' => 'ONITSHA SOUTH LGSC',
            'ORNTSC' => 'ORUMBA NORTH LGSC',
            'ORSTSC' => 'ORUMBA SOUTH LGSC',
            'OYISC' => 'OYI LGSC',
            'PENBSC' => 'PENSION BOARD LGSC',
        ];

        $lgpen = [
            'AGULG' => 'AGUATA',
            'ANESLG' => 'ANAMBRA EAST',
            'ANWSLG' => 'ANAMBRA WEST',
            'ANALG' => 'ANAOCHA',
            'AWNTLG' => 'AWKA NORTH',
            'AWSTLG' => 'AWKA SOUTH',
            'AYAMLG' => 'AYAMELUM',
            'DUNULG' => 'DUNUKOFIA',
            'EKWULG' => 'EKWUSIGO',
            'IDEMNTLG' => 'IDEMILI NORTH',
            'IDEMSTLG' => 'IDEMILI SOUTH',
            'IHIALG' => 'IHIALA',
            'NJILG' => 'NJIKOKA',
            'NNENTLG' => 'NNEWI NORTH',
            'NNESTLG' => 'NNEWI SOUTH',
            'NONINDGLG' => 'NON-INDIGINES',
            'OGBALG' => 'OGBARU',
            'ONITNTLG' => 'ONITSHA NORTH',
            'ONITSTLG' => 'ONITSHA SOUTH',
            'ORMNTLG' => 'ORUMBA NORTH',
            'ORMSTLG' => 'ORUMBA SOUTH',
            'OYILG' => 'OYI',
        ];

        $type_id = $types->firstWhere('code', 'SCO')->id;
        foreach($sco as $code => $name){
            factory(Mda::class)->create(['code' => $code, 'name' => $name, 'beneficiary_type_id' => $type_id]);
        }
        Mda::firstWhere('code', 'EXCO')->update(['has_sub' => 1]);

        $type_id = $types->firstWhere('code', 'PA')->id;
        foreach($pa as $code => $name){
            factory(Mda::class)->create(['code' => $code, 'name' => $name, 'beneficiary_type_id' => $type_id]);
        }

        $type_id = $types->firstWhere('code', 'CV')->id;
        foreach($cv as $code => $name){
            factory(Mda::class)->create(['code' => $code, 'name' => $name, 'beneficiary_type_id' => $type_id]);
        }
        Mda::firstWhere('code', 'SEC')->update(['has_sub' => 1]);

        $type_id = $types->firstWhere('code', 'ANPEN')->id;
        foreach($anpen as $code => $name){
            factory(Mda::class)->create(['code' => $code, 'name' => $name, 'beneficiary_type_id' => $type_id]);
        }

        $type_id = $types->firstWhere('code', 'LGEA')->id;
        foreach($lgea as $code => $name){
            factory(Mda::class)->create(['code' => $code, 'name' => $name, 'beneficiary_type_id' => $type_id, 'has_sub' => 1]);
        }

        $type_id = $types->firstWhere('code', 'LGSC')->id;
        foreach($lgsc as $code => $name){
            factory(Mda::class)->create(['code' => $code, 'name' => $name, 'beneficiary_type_id' => $type_id, 'has_sub' => 1]);
        }

        $type_id = $types->firstWhere('code', 'LGPEN')->id;
        foreach($lgpen as $code => $name){
            factory(Mda::class)->create(['code' => $code, 'name' => $name, 'beneficiary_type_id' => $type_id]);
        }
    }
}
