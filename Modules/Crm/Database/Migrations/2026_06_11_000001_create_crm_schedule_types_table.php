<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCrmScheduleTypesTable extends Migration
{
    public function up()
    {
        Schema::create('crm_schedule_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->string('name');
            $table->timestamps();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
        });

        $slugToName = [
            'call'    => 'Call',
            'sms'     => 'SMS',
            'meeting' => 'Meeting',
            'email'   => 'Email',
        ];

        $businesses = DB::table('business')->pluck('id');

        // [business_id => [slug => type_id]]
        $typeIds = [];
        foreach ($businesses as $businessId) {
            $typeIds[$businessId] = [];
            foreach ($slugToName as $slug => $name) {
                $id = DB::table('crm_schedule_types')->insertGetId([
                    'business_id' => $businessId,
                    'name'        => $name,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
                $typeIds[$businessId][$slug] = $id;
            }
        }

        Schema::table('crm_schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('schedule_type_id')->nullable()->after('schedule_type');
        });

        $schedules = DB::table('crm_schedules')
            ->whereNotNull('schedule_type')
            ->get(['id', 'business_id', 'schedule_type']);

        foreach ($schedules as $schedule) {
            $slug       = $schedule->schedule_type;
            $businessId = $schedule->business_id;

            if (isset($typeIds[$businessId][$slug])) {
                DB::table('crm_schedules')
                    ->where('id', $schedule->id)
                    ->update(['schedule_type_id' => $typeIds[$businessId][$slug]]);
            }
        }

        Schema::table('crm_schedules', function (Blueprint $table) {
            $table->foreign('schedule_type_id')
                ->references('id')
                ->on('crm_schedule_types')
                ->nullOnDelete();
        });

        Schema::table('crm_schedules', function (Blueprint $table) {
            $table->dropColumn('schedule_type');
        });
    }

    public function down()
    {
        Schema::table('crm_schedules', function (Blueprint $table) {
            $table->string('schedule_type')->nullable()->after('schedule_type_id');
        });

        $typeNames  = DB::table('crm_schedule_types')->pluck('name', 'id');
        $nameToSlug = ['Call' => 'call', 'SMS' => 'sms', 'Meeting' => 'meeting', 'Email' => 'email'];

        $schedules = DB::table('crm_schedules')
            ->whereNotNull('schedule_type_id')
            ->get(['id', 'schedule_type_id']);

        foreach ($schedules as $schedule) {
            $name = $typeNames[$schedule->schedule_type_id] ?? null;
            $slug = $nameToSlug[$name] ?? null;
            if ($slug) {
                DB::table('crm_schedules')
                    ->where('id', $schedule->id)
                    ->update(['schedule_type' => $slug]);
            }
        }

        Schema::table('crm_schedules', function (Blueprint $table) {
            $table->dropForeign(['schedule_type_id']);
            $table->dropColumn('schedule_type_id');
        });

        Schema::dropIfExists('crm_schedule_types');
    }
}
