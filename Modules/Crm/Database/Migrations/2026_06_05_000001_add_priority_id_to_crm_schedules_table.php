<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriorityIdToCrmSchedulesTable extends Migration
{
    public function up()
    {
        Schema::table('crm_schedules', function (Blueprint $table) {
            $table->integer('priority_id')
                ->nullable()
                ->after('followup_category_id');
        });
    }

    public function down()
    {
        Schema::table('crm_schedules', function (Blueprint $table) {
            $table->dropColumn('priority_id');
        });
    }
}
