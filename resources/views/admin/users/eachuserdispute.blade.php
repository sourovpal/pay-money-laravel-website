@extends('admin.layouts.master')

@section('title', __('Disputes'))

@section('head_style')
    <!-- dataTables -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/DataTables-1.10.18/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/Responsive-2.2.2/css/responsive.dataTables.min.css') }}">
@endsection

@section('page_content')
    <div class="box">
       <div class="panel-body ml-20">
            <ul class="nav nav-tabs f-14 cus" role="tablist">
                @include('admin.users.user_tabs')
           </ul>
          <div class="clearfix"></div>
       </div>
    </div>

    @if ($users->status == 'Inactive')
        <h3>{{ $users->first_name.' '.$users->last_name }}&nbsp;<span class="label label-danger">{{ __('Inactive') }}</span></h3>
    @elseif ($users->status == 'Suspended')
        <h3>{{ $users->first_name.' '.$users->last_name }}&nbsp;<span class="label label-warning">{{ __('Suspended') }}</span></h3>
    @elseif ($users->status == 'Active')
        <h3>{{ $users->first_name.' '.$users->last_name }}&nbsp;<span class="label label-success">{{ __('Active') }}</span></h3>
    @endif

    <div class="box">
      <div class="box-body">
        <div class="row">
            <div class="col-md-12 f-14">
                <div class="panel panel-info">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-hover pt-3" id="eachuserdispute">
                                <thead>
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Dispute ID') }}</th>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Claimant') }}</th>
                                    <th>{{ __('Transaction ID') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($disputes)
                                    @foreach($disputes as $dispute)
                                        <tr>
                                            <td>{{ dateFormat($dispute->created_at) }}</td>

                                            <td><a href="{{ url(\Config::get('adminPrefix').'/dispute/discussion/'.$dispute->id) }}">{{ $dispute->code }}</a></td>

                                            <td><a href="{{ url(\Config::get('adminPrefix').'/dispute/discussion/'.$dispute->id) }}">{{ $dispute->title }}</a></td>

                                            <td><a href="{{ url(\Config::get('adminPrefix').'/users/edit/'. $dispute->claimant->id) }}">{{ isset($dispute->claimant) ? $dispute->claimant->first_name.' '.$dispute->claimant->last_name :"-" }}</a></td>

                                            <td>
                                                @if (isset($dispute->transaction))
                                                    <a href="{{ url(\Config::get('adminPrefix').'/transactions/edit/'.$dispute->transaction->id) }}" target="_blank">{{ $dispute->transaction->uuid }}</a>
                                                @else
                                                    {{ 'Not Found' }}
                                                @endif
                                            </td>

                                            @if($dispute->status=='Open')
                                            <td><span class="label label-primary">{{ __('Open') }}</span></td>
                                            @else
                                            <td><span class="label label-danger">{{ __('Closed') }}</span></td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @else
                                    {{ __('No Dispute Found!') }}
                                @endif
                            </tbody>
                        </table>
                    </div>
                    </div>
                </div>
        </div>
    </div>
    @include('admin.layouts.partials.message_boxes')
@endsection

@push('extra_body_scripts')

<!-- jquery.dataTables js -->
<script src="{{ asset('public/backend/DataTables_latest/DataTables-1.10.18/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/backend/DataTables_latest/Responsive-2.2.2/js/dataTables.responsive.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    $(function () {
      $("#eachuserdispute").DataTable({
            "order": [],
            "language": '{{Session::get('dflt_lang')}}',
            "pageLength": '{{Session::get('row_per_page')}}'
        });
    });
</script>
@endpush
