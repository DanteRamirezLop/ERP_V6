<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>@lang('project::lang.status')</th>
                <th class="text-right">@lang('project::lang.qty')</th>
            </tr>
        </thead>
        <tbody>
            @forelse($projects_by_status as $row)
                <tr>
                    <td>{{ $status_labels[$row->status] ?? $row->status }}</td>
                    <td class="text-right">{{ $row->total }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-center">@lang('project::lang.no_record_found')</td>
                </tr>
            @endforelse
        </tbody>
        @if($projects_by_status->isNotEmpty())
        <tfoot>
            <tr class="bg-gray">
                <th><strong>@lang('project::lang.total_projects')</strong></th>
                <th class="text-right"><strong>{{ $total }}</strong></th>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
