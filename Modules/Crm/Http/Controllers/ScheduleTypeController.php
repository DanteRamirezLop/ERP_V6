<?php

namespace Modules\Crm\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Modules\Crm\Entities\ScheduleType;

class ScheduleTypeController extends Controller
{
    protected $moduleUtil;

    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        if (!$this->canAccess($business_id)) {
            abort(403, 'Unauthorized action.');
        }

        $schedule_types = ScheduleType::where('business_id', $business_id)
            ->orderBy('name')
            ->get();

        return view('crm::schedule_type.index', compact('schedule_types'));
    }

    public function store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!$this->canAccess($business_id)) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $request->validate(['name' => 'required|string|max:191']);

            ScheduleType::create([
                'business_id' => $business_id,
                'name'        => $request->name,
            ]);

            $output = ['success' => true, 'msg' => __('lang_v1.success')];
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());
            $output = ['success' => false, 'msg' => __('messages.something_went_wrong')];
        }

        if ($request->ajax()) {
            return $output;
        }

        return redirect()->action([ScheduleTypeController::class, 'index'])
            ->with(['status' => $output]);
    }

    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!$this->canAccess($business_id)) {
            abort(403, 'Unauthorized action.');
        }

        $schedule_type = ScheduleType::where('business_id', $business_id)->findOrFail($id);

        if (request()->ajax()) {
            return view('crm::schedule_type.edit', compact('schedule_type'));
        }

        return redirect()->action([ScheduleTypeController::class, 'index']);
    }

    public function update(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!$this->canAccess($business_id)) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $request->validate(['name' => 'required|string|max:191']);

            ScheduleType::where('business_id', $business_id)
                ->findOrFail($id)
                ->update(['name' => $request->name]);

            $output = ['success' => true, 'msg' => __('lang_v1.success')];
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());
            $output = ['success' => false, 'msg' => __('messages.something_went_wrong')];
        }

        if ($request->ajax()) {
            return $output;
        }

        return redirect()->action([ScheduleTypeController::class, 'index'])
            ->with(['status' => $output]);
    }

    public function destroy($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!$this->canAccess($business_id)) {
            abort(403, 'Unauthorized action.');
        }

        try {
            ScheduleType::where('business_id', $business_id)->findOrFail($id)->delete();
            $output = ['success' => true, 'msg' => __('lang_v1.success')];
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());
            $output = ['success' => false, 'msg' => __('messages.something_went_wrong')];
        }

        return $output;
    }

    private function canAccess($business_id)
    {
        return auth()->user()->can('superadmin')
            || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module');
    }
}
