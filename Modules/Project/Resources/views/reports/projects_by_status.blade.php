@extends('layouts.app')
@section('title', __('project::lang.project_report'))

@section('content')
@include('project::layouts.nav')

<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black tw-flex tw-gap-2">
        <a href="{{action([\Modules\Project\Http\Controllers\ReportController::class, 'index'])}}" class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-sm tw-text-white "> <span class="fa fa-arrow-left"></span></a>
        <span>
        @lang('report.reports')
            <small class="tw-text-sm md:tw-text-base tw-text-gray-700">
                @lang('project::lang.projects_by_status_report')
            </small> 
        </span>
    </h1>
</section>

<section class="content">
    @component('components.filters', ['title' => __('report.filters')])

    <div class="tw-px-4">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('projects_status_report_daterange', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', null, [
                        'placeholder' => __('lang_v1.select_a_date_range'),
                        'class'       => 'form-control',
                        'id'          => 'projects_status_report_daterange',
                        'readonly',
                    ]) !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('projects_status_report_status', __('project::lang.status') . ':') !!}
                    {!! Form::select('status[]', $statuses, null, [
                        'class'    => 'form-control select2',
                        'id'       => 'projects_status_report_status',
                        'multiple' => true,
                        'style'    => 'width: 100%;',
                    ]) !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>&nbsp;</label><br>
                    <!-- <button type="button" id="download_pdf_btn" class="btn btn-success">
                        <i class="fas fa-download"></i>
                        @lang('project::lang.download_pdf') 
                    </button> -->
                </div>
            </div>
        </div>
    </div>
    @endcomponent

    <div class="box box-solid">
        <div class="box-header">
            <div class="box-tools">
                <button type="button" id="download_pdf_btn" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm">
                    <i class="fas fa-download"></i>
                    @lang('project::lang.download_pdf') 
                </button>
            </div>
        </div>
        <div class="box-body projects_by_status_report">
            
        </div>

       
    </div>

</section>
@endsection

@section('javascript')
<script type="text/javascript">
$(document).ready(function () {

    $('#projects_status_report_daterange').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#projects_status_report_daterange').val(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );
        }
    );

    $('#projects_status_report_daterange').on('cancel.daterangepicker', function () {
        $('#projects_status_report_daterange').val('');
    });

    $(document).on('change', '#projects_status_report_daterange, #projects_status_report_status', function () {
        loadProjectsByStatusReport();
    });

    $('#download_pdf_btn').on('click', function () {
        downloadPdf();
    });

    loadProjectsByStatusReport();
});

function getReportFilters() {
    var picker = $('input#projects_status_report_daterange').data('daterangepicker');
    return {
        start_date: picker ? picker.startDate.format('YYYY-MM-DD') : '',
        end_date:   picker ? picker.endDate.format('YYYY-MM-DD')   : '',
        status:     $('#projects_status_report_status').val(),
    };
}

function loadProjectsByStatusReport() {
    $.ajax({
        method:   'GET',
        dataType: 'json',
        url:      '/project/project-by-status-report',
        data:     getReportFilters(),
        success: function (result) {
            if (result.success) {
                $('div.projects_by_status_report').html(result.report);
            } else {
                toastr.error(result.msg);
            }
        }
    });
}

function downloadPdf() {
    var filters = getReportFilters();
    var params  = $.param(filters);
    window.open('/project/project-by-status-report/pdf?' + params, '_blank');
}
</script>
@endsection
