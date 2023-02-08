@extends('admin.layouts.master')
@section('title', __('Revenues'))

@section('head_style')
  <!-- Bootstrap daterangepicker -->
  <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/bootstrap/dist/css/daterangepicker.min.css')}}">

  <!-- dataTables -->
  <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/DataTables-1.10.18/css/jquery.dataTables.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/DataTables_latest/Responsive-2.2.2/css/responsive.dataTables.min.css') }}">
@endsection

@section('page_content')
  <div class="box">
      <div class="box-body pb-20">
          <form class="form-horizontal" action="{{ url(\Config::get('adminPrefix').'/revenues') }}" method="GET">

              <input id="startfrom" type="hidden" name="from" value="{{ isset($from) ? $from : '' }}">
              <input id="endto" type="hidden" name="to" value="{{ isset($to) ? $to : '' }}">
              <input id="user_id" type="hidden" name="user_id" value="{{ isset($user) ? $user : '' }}">

              <div class="row">
                  <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div class="d-flex flex-wrap">
                            <!-- Date and time range -->
                            <div class="pr-25">
                                <label class="f-14 fw-bold mb-1">{{ __('Date Range') }}</label><br>
                                <button type="button" class="btn btn-default f-14" id="daterange-btn">
                                    <span id="drp">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    <i class="fa fa-caret-down"></i>
                                </button>
                            </div>

                            <!-- Currency -->
                            <div class="pr-25">
                                <label class="f-14 fw-bold mb-1" for="currency">{{ __('Currency') }}</label><br>
                                <select class="form-control select2" name="currency" id="currency">
                                    <option value="all" {{ ($currency =='all') ? 'selected' : '' }} >{{ __('All') }}</option>
                                    @foreach($revenues_currency as $revenue)
                                        <option value="{{ $revenue->currency_id }}" {{ ($revenue->currency_id == $currency) ? 'selected' : '' }}>
                                            {{ $revenue->currency->code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="pr-25">
                                <label class="f-14 fw-bold mb-1" for="status">{{ __('Transaction Type') }}</label><br>
                                <select class="form-control select2" name="type" id="type">
                                    <option value="all" {{ ($type =='all') ? 'selected' : '' }} >{{ __('All') }}</option>
                                    @foreach($revenues_type as $revenue)
                                    <option value="{{ $revenue->transaction_type_id }}" {{ ($revenue->transaction_type_id == $type) ? 'selected' : '' }}>
                                        {{ ($revenue->transaction_type->name == "Withdrawal") ? "Payout" : str_replace('_', ' ', $revenue->transaction_type->name) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                          <div>
                              <div class="input-group mt-3">
                                 <button type="submit" name="btn" class="btn btn-theme f-14" id="btn">{{ __('Filter') }}</button>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </form>
      </div>
  </div>

  <!-- Total Charge Boxes -->
  @if($currencyInfo)
    <div class="box">
      <div class="box-body">
          <div class="row">
            @forelse ($currencyInfo as $currencyCode => $currency)
                @if ($currency['revenue'] > 0)
                  <div class="col-md-3">
                     <div class="panel panel-primary">
                          <div class="panel-body text-center p-1 mb-0">
                            <span class="f-14">Total {{ $currencyCode }} {{ __('Revenue') }}</span>
                            <p class="mb-0 f-18">{{ moneyFormat($currencyCode , formatNumber($currency['revenue'], $currency['currency_id'])) }}</p>
                          </div>
                     </div>
                  </div>
                @endif

            @empty
              <h3 class="panel-title text-center">{{ __('No Revenue Found!') }}</h3>
            @endforelse
          </div>
      </div>
    </div>
  @endif

  <div class="row">
      <div class="col-md-8">
          <p class="panel-title text-bold ml-5 f-14">{{ __('All Revenues') }}</p>
      </div>
      <div class="col-md-4">
          <div class="btn-group pull-right">
              <a href="" class="btn btn-sm btn-default btn-flat f-14" id="csv">{{ __('CSV') }}</a>
              <a href="" class="btn btn-sm btn-default btn-flat f-14" id="pdf">{{ __('PDF') }}</a>
          </div>
      </div>
  </div>

  <div class="box mt-20">
    <div class="box-body">
      <div class="row">
        <div class="col-md-12 f-14">
          <div class="panel panel-info">
            <div class="panel-body">
              <div class="table-responsive">
                {!! $dataTable->table(['class' => 'table table-striped table-hover dt-responsive', 'width' => '100%', 'cellspacing' => '0']) !!}
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
@endsection

@push('extra_body_scripts')

<!-- Bootstrap daterangepicker -->
<script src="{{ asset('public/backend/bootstrap-daterangepicker/daterangepicker.js') }}" type="text/javascript"></script>

<!-- jquery.dataTables js -->
<script src="{{ asset('public/backend/DataTables_latest/DataTables-1.10.18/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/backend/DataTables_latest/Responsive-2.2.2/js/dataTables.responsive.min.js') }}" type="text/javascript"></script>

{!! $dataTable->scripts() !!}

<script type="text/javascript">
    $(".select2").select2({});

    var sDate;
    var eDate;

    //Date range as a button
    $('#daterange-btn').daterangepicker(
      {
        ranges   : {
          'Today'       : [moment(), moment()],
          'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month'  : [moment().startOf('month'), moment().endOf('month')],
          'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'days'),
        endDate  : moment()
      },
      function (start, end)
      {
        var sessionDate      = '{{Session::get('date_format_type')}}';
        var sessionDateFinal = sessionDate.toUpperCase();

        sDate = moment(start, 'MMMM D, YYYY').format(sessionDateFinal);
        $('#startfrom').val(sDate);

        eDate = moment(end, 'MMMM D, YYYY').format(sessionDateFinal);
        $('#endto').val(eDate);

        $('#daterange-btn span').html('&nbsp;' + sDate + ' - ' + eDate + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
      }
    )

    $(document).ready(function()
    {
        $("#daterange-btn").mouseover(function() {
            $(this).css('background-color', 'white');
            $(this).css('border-color', 'grey !important');
        });

        var startDate = "{!! $from !!}";
        var endDate   = "{!! $to !!}";
        // alert(startDate);
        if (startDate == '') {
            $('#daterange-btn span').html('<i class="fa fa-calendar"></i> &nbsp;&nbsp; Pick a date range &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
        } else {
            $('#daterange-btn span').html(startDate + ' - ' +endDate + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
        }
    });

    // csv
    $(document).ready(function()
    {
        $('#csv').on('click', function(event)
        {
          event.preventDefault();
          var startfrom = $('#startfrom').val();
          var endto = $('#endto').val();
          var currency = $('#currency').val();
          var type = $('#type').val();
          var user_id = $('#user_id').val();
          window.location = SITE_URL+"/"+ADMIN_PREFIX+"/revenues/csv?startfrom="+startfrom
          +"&endto="+endto
          +"&currency="+currency
          +"&type="+type
          +"&user_id="+user_id;
        });
    });

    // pdf
    $(document).ready(function()
    {
        $('#pdf').on('click', function(event)
        {
          event.preventDefault();
          var startfrom = $('#startfrom').val();
          var endto = $('#endto').val();
          var currency = $('#currency').val();
          var type = $('#type').val();
          var user_id = $('#user_id').val();
          window.location = SITE_URL+"/"+ADMIN_PREFIX+"/revenues/pdf?startfrom="+startfrom
          +"&endto="+endto
          +"&currency="+currency
          +"&type="+type
          +"&user_id="+user_id;
        });
    });
</script>

@endpush
