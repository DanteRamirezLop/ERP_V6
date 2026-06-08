<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDocumentToCrmSchedulesTable extends Migration
{
    public function up()
    {
        Schema::table('crm_schedules', function (Blueprint $table) {
            $table->string('document')->nullable()->after('description');
        });
    }

    public function down()
    {
        Schema::table('crm_schedules', function (Blueprint $table) {
            $table->dropColumn('document');
        });
    }
}
