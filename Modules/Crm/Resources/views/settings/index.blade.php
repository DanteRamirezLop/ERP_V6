@extends('layouts.app')
@section('title', __('messages.settings'))
@section('content')
@include('crm::layouts.nav')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        @lang('messages.settings')
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['url' => action([\Modules\Crm\Http\Controllers\CrmSettingsController::class, 'updateSettings']), 'method' => 'post']) !!}
            @component('components.widget', ['class' => 'box-solid'])
                <div class="col-md-4">
                    <div class="checkbox">
                        <label>
                        {!! Form::checkbox('enable_order_request', 1, !empty($crm_settings['enable_order_request']), ['class' => 'input-icheck']); !!} @lang('crm::lang.enable_order_request')
                        </label> @show_tooltip(__('crm::lang.enable_order_request_help'))
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('order_request_prefix', __('crm::lang.order_request_prefix') . ':') !!}
                        {!! Form::text('order_request_prefix', $crm_settings['order_request_prefix'] ?? null, ['class' => 'form-control','placeholder' => __( 'crm::lang.order_request_prefix' )]); !!}
                    </div>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary pull-right">@lang( 'messages.update' )</button>
                </div>
            @endcomponent
            {!! Form::close() !!}
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-solid', 'title' => __('crm::lang.manage_priority')])
                @slot('tool')
                    <div class="box-tools">
                        <button type="button" class="btn btn-primary btn-sm" id="btn_add_priority">
                            <i class="fa fa-plus"></i> @lang('messages.add')
                        </button>
                    </div>
                @endslot

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="priority_table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>@lang('crm::lang.priority')</th>
                                <th>@lang('lang_v1.description')</th>
                                <th>@lang('messages.action')</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            @endcomponent
        </div>
    </div>
</section>

{{-- Modal para crear/editar prioridades --}}
<div class="modal fade" id="priority_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title" id="priority_modal_title">@lang('messages.add')</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="priority_edit_id" value="">
                <div class="form-group">
                    {!! Form::label('priority_name_input', __('crm::lang.priority') . ':*') !!}
                    {!! Form::text('priority_name_input', null, ['class' => 'form-control', 'id' => 'priority_name_input', 'required', 'placeholder' => __('crm::lang.priority')]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('priority_description_input', __('lang_v1.description') . ':') !!}
                    {!! Form::textarea('priority_description_input', null, ['class' => 'form-control', 'id' => 'priority_description_input', 'rows' => 3, 'placeholder' => __('lang_v1.description')]) !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
                <button type="button" class="btn btn-primary" id="btn_save_priority">@lang('messages.save')</button>
            </div>
        </div>
    </div>
</div>

@stop
@section('javascript')
<script type="text/javascript">
$(function () {
    var priority_table = $('#priority_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ action([\App\Http\Controllers\TaxonomyController::class, 'index']) }}?type=schedule_priority',
        columnDefs: [{ targets: [2], orderable: false, searchable: false }],
        columns: [
            { data: 'name', name: 'name' },
            { data: 'description', name: 'description' },
            { data: 'action', name: 'action' },
        ],
    });

    // Abrir modal para crear
    $('#btn_add_priority').click(function () {
        $('#priority_edit_id').val('');
        $('#priority_name_input').val('');
        $('#priority_description_input').val('');
        $('#priority_modal_title').text('{{ __("messages.add") }}');
        $('#btn_save_priority').text('{{ __("messages.save") }}');
        $('#priority_modal').modal('show');
    });

    // Abrir modal para editar (delegado al botón que genera DataTables)
    $(document).on('click', 'button.edit_category_button', function () {
        var href = $(this).data('href');
        $.get(href, function (html) {
            var name = $(html).find('input[name="name"]').val();
            var description = $(html).find('textarea[name="description"]').val();
            var id = href.match(/\/taxonomies\/(\d+)\/edit/)[1];

            $('#priority_edit_id').val(id);
            $('#priority_name_input').val(name);
            $('#priority_description_input').val(description || '');
            $('#priority_modal_title').text('{{ __("messages.edit") }}');
            $('#btn_save_priority').text('{{ __("messages.update") }}');
            $('#priority_modal').modal('show');
        });
    });

    // Guardar (crear o actualizar)
    $('#btn_save_priority').click(function () {
        var id = $('#priority_edit_id').val();
        var name = $('#priority_name_input').val().trim();
        var description = $('#priority_description_input').val().trim();

        if (!name) {
            toastr.error('{{ __("messages.required") }}');
            return;
        }

        var url, method;
        var data = { name: name, description: description };

        if (id) {
            url = '{{ action([\App\Http\Controllers\TaxonomyController::class, "update"], ["taxonomy" => "__ID__"]) }}'.replace('__ID__', id);
            method = 'PUT';
        } else {
            url = '{{ action([\App\Http\Controllers\TaxonomyController::class, "store"]) }}';
            method = 'POST';
            data.category_type = 'schedule_priority';
        }

        $.ajax({
            url: url,
            method: method,
            data: data,
            dataType: 'json',
            success: function (result) {
                if (result.success) {
                    $('#priority_modal').modal('hide');
                    toastr.success(result.msg);
                    priority_table.ajax.reload();
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });

    // Eliminar
    $(document).on('click', 'button.delete_category_button', function () {
        var href = $(this).data('href');
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(function (willDelete) {
            if (willDelete) {
                $.ajax({
                    url: href,
                    method: 'DELETE',
                    dataType: 'json',
                    success: function (result) {
                        if (result.success) {
                            toastr.success(result.msg);
                            priority_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
});
</script>
@endsection