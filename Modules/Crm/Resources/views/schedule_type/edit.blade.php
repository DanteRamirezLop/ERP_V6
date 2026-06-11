<div class="modal-dialog" role="document">
    <div class="modal-content">
        {!! Form::open([
            'url'    => action([\Modules\Crm\Http\Controllers\ScheduleTypeController::class, 'update'], $schedule_type->id),
            'method' => 'put',
            'id'     => 'edit_schedule_type_form',
        ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            <h4 class="modal-title">@lang('messages.edit') @lang('crm::lang.schedule_type')</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('name', __('crm::lang.name') . ':*') !!}
                {!! Form::text('name', $schedule_type->name, ['class' => 'form-control', 'required']) !!}
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang('messages.close')</button>
            <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang('messages.update')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>
