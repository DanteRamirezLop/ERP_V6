<?php

namespace Modules\Crm\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduleType extends Model
{
    use SoftDeletes;

    protected $table = 'crm_schedule_types';

    protected $guarded = ['id'];

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'schedule_type_id');
    }

    public static function forDropdown($business_id)
    {
        return self::where('business_id', $business_id)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    public static function forDropdownByName($business_id)
    {
        return self::where('business_id', $business_id)
            ->orderBy('name')
            ->pluck('name', 'name')
            ->toArray();
    }
}
