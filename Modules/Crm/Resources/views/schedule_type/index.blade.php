@extends('layouts.app')
@section('title', __('crm::lang.schedule_type'))
@section('content')
    @include('crm::layouts.nav')
    <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">
            @lang('crm::lang.schedule_type')
            <small class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold">{{ __('crm::lang.crm') }}</small>
        </h1>
    </section>

    <section class="content">
        @component('components.widget', ['class' => 'box box-solid'])
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white pull-right" data-toggle="modal" data-target="#add_schedule_type_modal">
                        <i class="fa fa-plus"></i> @lang('messages.add')
                    </button>
                </div>
            @endslot

            @if(session('status'))
                @if(session('status.success'))
                    <div class="alert alert-success">{{ session('status.msg') }}</div>
                @else
                    <div class="alert alert-danger">{{ session('status.msg') }}</div>
                @endif
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>@lang('crm::lang.name')</th>
                            <th>@lang('messages.action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedule_types as $type)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $type->name }}</td>
                                <td>
                                    <button type="button"
                                        class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-info btn-edit-type"
                                        data-href="{{ action([\Modules\Crm\Http\Controllers\ScheduleTypeController::class, 'edit'], $type->id) }}">
                                        <i class="fa fa-edit"></i> @lang('messages.edit')
                                    </button>
                                    <button type="button"
                                        class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-error btn-delete-type"
                                        data-href="{{ action([\Modules\Crm\Http\Controllers\ScheduleTypeController::class, 'destroy'], $type->id) }}">
                                        <i class="fas fa-trash"></i> @lang('messages.delete')
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">@lang('messages.no_data_available')</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endcomponent
    </section>

    {{-- Add Modal --}}
    <div class="modal fade" id="add_schedule_type_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['url' => action([\Modules\Crm\Http\Controllers\ScheduleTypeController::class, 'store']), 'method' => 'post', 'id' => 'add_schedule_type_form']) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    <h4 class="modal-title">@lang('messages.add') @lang('crm::lang.schedule_type')</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        {!! Form::label('name', __('crm::lang.name') . ':*') !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __('crm::lang.name')]) !!}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang('messages.close')</button>
                    <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang('messages.save')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div class="modal fade" id="edit_schedule_type_modal" tabindex="-1" role="dialog"></div>
@endsection
@section('javascript')
<script>
$(function () {
    $(document).on('click', '.btn-edit-type', function () {
        var url = $(this).data('href');
        $.get(url, function (html) {
            $('#edit_schedule_type_modal').html(html).modal('show');
        });
    });

    $(document).on('click', '.btn-delete-type', function () {
        var url = $(this).data('href');
        if (confirm('{{ __("messages.confirm_delete") }}')) {
            $.ajax({
                url: url,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function (res) {
                    if (res.success) {
                        toastr.success(res.msg);
                        location.reload();
                    } else {
                        toastr.error(res.msg);
                    }
                }
            });
        }
    });

    $(document).on('submit', '#add_schedule_type_form', function (e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function (res) {
                if (res.success) {
                    toastr.success(res.msg);
                    location.reload();
                } else {
                    toastr.error(res.msg);
                }
            }
        });
    });

    $(document).on('submit', '#edit_schedule_type_form', function (e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function (res) {
                if (res.success) {
                    toastr.success(res.msg);
                    location.reload();
                } else {
                    toastr.error(res.msg);
                }
            }
        });
    });
});
</script>
@endsection
