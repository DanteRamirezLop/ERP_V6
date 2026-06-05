<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .report-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .report-header h2 {
            margin: 0 0 4px 0;
            font-size: 18px;
        }
        .report-header p {
            margin: 2px 0;
            font-size: 11px;
            color: #555;
        }
        /* Summary table */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .summary-table th {
            background-color: #34495e;
            color: #fff;
            padding: 7px 10px;
            text-align: left;
            font-size: 12px;
        }
        .summary-table th.text-right,
        .summary-table td.text-right {
            text-align: right;
        }
        .summary-table td {
            padding: 6px 10px;
            border-bottom: 1px solid #ddd;
        }
        .summary-table tr:nth-child(even) td {
            background-color: #f5f5f5;
        }
        .summary-table tfoot td {
            font-weight: bold;
            background-color: #ecf0f1;
            border-top: 2px solid #333;
        }
        /* Detail sections */
        .status-section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .status-title {
            background-color: #2c3e50;
            color: #fff;
            padding: 6px 10px;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 0;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
        }
        .detail-table th {
            background-color: #7f8c8d;
            color: #fff;
            padding: 5px 10px;
            font-size: 11px;
            text-align: left;
        }
        .detail-table td {
            padding: 5px 10px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 11px;
        }
        .detail-table tr:nth-child(even) td {
            background-color: #f9f9f9;
        }
        .section-divider {
            font-size: 13px;
            font-weight: bold;
            margin: 20px 0 8px 0;
            border-bottom: 1px solid #aaa;
            padding-bottom: 4px;
            color: #2c3e50;
        }
    </style>
</head>
<body>

    <div class="report-header">
        <h2>@lang('project::lang.projects_by_status_report')</h2>
        @if(!empty($start_date) && !empty($end_date))
            <p>@lang('report.date_range'): {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}</p>
        @endif
        <p>@lang('messages.generated_on'): {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    {{-- Summary table --}}
    <p class="section-divider">@lang('project::lang.total_projects')</p>
    <table class="summary-table">
        <thead>
            <tr>
                <th>@lang('project::lang.status')</th>
                <th class="text-right">@lang('project::lang.qty')</th>
            </tr>
        </thead>
        <tbody>
            @php $grand_total = 0; @endphp
            @forelse($projects_grouped as $status => $group)
                @php $grand_total += $group->count(); @endphp
                <tr>
                    <td>{{ $status_labels[$status] ?? $status }}</td>
                    <td class="text-right">{{ $group->count() }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" style="text-align:center;">@lang('project::lang.no_record_found')</td>
                </tr>
            @endforelse
        </tbody>
        @if($projects_grouped->isNotEmpty())
        <tfoot>
            <tr>
                <td><strong>@lang('project::lang.total_projects')</strong></td>
                <td class="text-right"><strong>{{ $grand_total }}</strong></td>
            </tr>
        </tfoot>
        @endif
    </table>

    {{-- Detail per status --}}
    @if($projects_grouped->isNotEmpty())
        <p class="section-divider">@lang('project::lang.projects') @lang('project::lang.by_status')</p>

        @foreach($projects_grouped as $status => $group)
            <div class="status-section">
                <div class="status-title">
                    {{ $status_labels[$status] ?? $status }} ({{ $group->count() }})
                </div>
                <table class="detail-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>@lang('project::lang.project')</th>
                            <th>@lang('messages.date')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($group as $i => $project)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $project->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($project->created_at)->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    @endif

</body>
</html>
