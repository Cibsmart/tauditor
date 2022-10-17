<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateMdaScheduleView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE OR REPLACE VIEW mda_schedule_views AS
            SELECT `category`.*,
                `mda_schedule`.`id` AS `mda_schedule`,
                `mda`.`id` AS `mda`,
                `mda`.`code` AS `mda_code`,
                `mda`.`name` AS `mda_name`,
                `mda`.`active` AS `mda_active`,
                `mda`.`beneficiary_type_id` AS `beneficiary_type`,
                `sub_mda_schedule`.`uploaded` AS `uploaded`,
                `sub_mda_schedule`.`autopay_generated` AS `generated`,
                `sub_mda_schedule`.`id` AS `sub_mda_id`
            FROM `audit_payroll_categories` AS `category`
            INNER JOIN `audit_mda_schedules` AS `mda_schedule` ON `category`.`id` = `mda_schedule`.`audit_payroll_category_id`
            INNER JOIN `audit_sub_mda_schedules` AS `sub_mda_schedule` ON `mda_schedule`.`id` = `sub_mda_schedule`.`audit_mda_schedule_id`
            INNER JOIN `mdas` AS `mda` ON `mda_schedule`.`mda_id` = `mda`.`id`
            WHERE `mda`.`active` = 1;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS mda_schedule_views;');
    }
}
