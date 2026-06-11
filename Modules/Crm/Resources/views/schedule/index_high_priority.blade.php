@extends('layouts.app')
@section('title', __('crm::lang.follow_ups'))
@section('content')
	@include('crm::layouts.nav')
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">
			@lang('crm::lang.follow_ups')
			<small class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold">
				@lang('crm::lang.crm') &mdash;
				@if(!empty($high_priority))
					<span class="label label-danger">{{ $high_priority->name }}</span>
				@endif
			</small>
		</h1>
	</section>

	<section class="content no-print">
		<div class="row">
			<div class="col-md-12">
				@component('components.widget', ['class' => 'box box-solid', 'title' => __('crm::lang.all_schedules')])
					@slot('tool')
			            <div class="box-tools">
			                <button type="button" class="tw-m-2 tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right btn-add-schedule">
			                <i class="fa fa-plus"></i> @lang('messages.add')</button>
			            </div>
			            <input type="hidden" name="schedule_create_url" id="schedule_create_url" value="{{action([\Modules\Crm\Http\Controllers\ScheduleController::class, 'create'])}}">
		        	@endslot
			        <div class="col-sm-12">
			        	<div class="table-responsive">
					    	<table class="table table-bordered table-striped" id="high_priority_follow_up_table" style="width: 100%">
							    <thead>
							        <tr>
							        	<th>@lang('messages.action')</th>
							        	<th>@lang('contact.contact')</th>
							        	<th>@lang('crm::lang.start_datetime')</th>
							            <th>@lang('crm::lang.end_datetime')</th>
							            <th>@lang('sale.status')</th>
							            <th>@lang('crm::lang.schedule_type')</th>
							            <th>@lang('crm::lang.followup_category')</th>
							            <th>@lang('crm::lang.priority')</th>
							            <th>@lang('lang_v1.assigned_to')</th>
							            <th>@lang('crm::lang.description')</th>
							            <th>@lang('crm::lang.additional_info')</th>
							            <th>@lang('crm::lang.title')</th>
							            <th>@lang('lang_v1.added_by')</th>
							            <th>@lang('lang_v1.added_on')</th>
							        </tr>
							    </thead>
							    <tbody></tbody>
							    <tfoot>
	                                <tr class="bg-gray font-17 footer-total text-center">
	                                    <td colspan="5">
	                                        <strong>@lang('sale.total'):</strong>
	                                    </td>
	                                    <td class="footer_hp_status_count"></td>
	                                    <td class="footer_hp_type_count"></td>
	                                    <td colspan="6"></td>
	                                </tr>
	                            </tfoot>
							</table>
					    </div>
			        </div>
		    	@endcomponent
			</div>
		</div>
	</section>

	<div class="modal fade schedule" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
    <div class="modal fade edit_schedule" tabindex="-1" role="dialog"></div>
	<div class="modal fade schedule_log_modal" tabindex="-1" role="dialog"></div>

@endsection
@section('javascript')
	<script src="{{ asset('modules/crm/js/crm.js?v=' . $asset_v) }}"></script>
	<script type="text/javascript">
		$(function () {
		    high_priority_datatable = $("#high_priority_follow_up_table").DataTable({
				processing: true,
		        serverSide: true,
		        scrollY: "80vh",
				scrollX: true,
				scrollCollapse: true,
		        ajax: {
		            url: "/crm/high-priority-follow-ups",
		            data: function(d) {
		            	d.priority_id = "{{ $high_priority_id }}";
		            }
		        },
		        columnDefs: [
		            {
		                targets: [0, 8, 10],
		                orderable: false,
		                searchable: false,
		            },
		        ],
		        aaSorting: [[2, 'desc']],
		        columns: [
		        	{ data: 'action', name: 'action' },
		        	{ data: 'contact', name: 'contacts.name' },
		        	{ data: 'start_datetime', name: 'start_datetime' },
		            { data: 'end_datetime', name: 'end_datetime' },
		            { data: 'status', name: 'crm_schedules.status' },
		            { data: 'schedule_type', name: 'schedule_type' },
		            { data: 'followup_category', name: 'C.name' },
		            { data: 'priority_name', name: 'P.name' },
		            { data: 'users', name: 'users' },
		            { data: 'description', name: 'description' },
		            { data: 'additional_info', name: 'additional_info' },
		            { data: 'title', name: 'title' },
		            { data: 'added_by', name: 'added_by' },
		            { data: 'added_on', name: 'crm_schedules.created_at' },
		        ],
		        "fnDrawCallback": function( oSettings ) {
		        	__show_date_diff_for_human($("#high_priority_follow_up_table"));

		        	$('a.view_schedule_log').click(function(){
		        		getScheduleLog($(this).data('schedule_id'), true);
		        	})
			    },
		        "footerCallback": function ( row, data, start, end, display ) {
		        	$('.footer_hp_status_count').html(__count_status(data, 'status'));
		            $('.footer_hp_type_count').html(__count_status(data, 'schedule_type'));
		        }
			});
		});
	</script>
@endsection
