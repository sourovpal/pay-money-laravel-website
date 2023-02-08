@extends('user_dashboard.layouts.app')
@section('content')

<section class="min-vh-100">
    <div class="my-30">
        <div class="container-fluid">
            <!-- Page title start -->
            <div>
                <h3 class="page-title">{{ __('Withdrawals') }}</h3>
            </div>
            <!-- Page title end-->

            <div class="mt-4 border-bottom">
                <div class="d-flex flex-wrap">
                    <a href="{{ url('/payouts') }}">
                        <div class="mr-4 pb-3">
                            <p class="text-16 font-weight-400 text-gray-500">{{ __('Payout list') }}</p>
                        </div>
                    </a>

                    <a href="{{ url('/payout/setting') }}">
                        <div class="mr-4">
                            <p class="text-16 font-weight-400 text-gray-500">{{ __('Payout settings') }}  </p>
                        </div>
                    </a>

                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mt-4">
                    <div class="row">
                        <div class="col-lg-4">
                            <!-- Sub title start -->
                            <div class="mt-5">
                                <h3 class="sub-title">{{ __('Success') }}</h3>
                                <p class="text-gray-500 text-16">{{ __('Your payout process successfully done.') }}</p>
                            </div>
                            <!-- Sub title end-->
                        </div>

                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-10 p-0 mt-4">
                                    <div class="d-flex w-100 ">
                                        <ol class="breadcrumb w-100">
                                            <li class="breadcrumb-active text-white">{{ __('Create') }}</li>
                                            <li class="breadcrumb-first text-white">{{ __('Confirmation') }}</li>
                                            <li class="breadcrumb-success text-white">{{ __('Success') }}</li>
                                        </ol>
                                    </div>
    
                                    <div class="bg-secondary rounded p-35 mt-5 shadow">
                                        @include('user_dashboard.layouts.common.alert')
                                        <div>
                                            <div class="d-flex justify-content-center">
                                                <div class="confirm-check"><i class="fa fa-check"></i></div>
                                            </div>
    
                                            <div class="text-center mt-4">
                                                <p class="sub-title">@lang('message.dashboard.payout.new-payout.success')!</p>
                                            </div>
    
                                            <div class="text-center">
                                                <p class="mt-2">
                                                    @lang('message.dashboard.payout.new-payout.payout-success')
                                                </p>
                                            </div>
                                            <p class="text-center font-weight-600 mt-2">@lang('message.dashboard.payout.new-payout.amount') : {{  moneyFormat($currencySymbol, formatNumber($amount, $currency_id)) }}</p>
    
                                            <div class="mt-4">
                                                <div class="text-center">
                                                    <a href="{{ url('withdrawal-money/print') }}/{{ $transactionId }}" target="_blank" class="btn btn-grad mr-2 mt-4"><strong>@lang('message.dashboard.vouchers.success.print')</strong></a>
                                                    <a href="{{ url('payout') }}" class="btn btn-primary px-4 py-2 ml-2 mt-4"><strong>@lang('message.dashboard.payout.new-payout.payout-again')</strong></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('js')
    <script type="text/javascript">

        function printFunc(){
            window.print();
        }
        $(document).ready(function() {
            window.history.pushState(null, "", window.location.href);
            window.onpopstate = function() {
                window.history.pushState(null, "", window.location.href);
            };
        });

        //disabling F5
        function disable_f5(e)
        {
          if ((e.which || e.keyCode) == 116)
          {
              e.preventDefault();
          }
        }
        $(document).ready(function(){
            $(document).bind("keydown", disable_f5);
        });

        //disabling ctrl+r
        function disable_ctrl_r(e)
        {
          if(e.keyCode == 82 && e.ctrlKey)
          {
            e.preventDefault();
          }
        }
        $(document).ready(function(){
            $(document).bind("keydown", disable_ctrl_r);
        });

        //Clear withdrawal local storage values on click - payout again
        $(document).on('click', '.payout-again', function()
        {
            window.localStorage.removeItem('payoutConfirmPreviousUrl');
            window.localStorage.removeItem('payoutPaymentMethodId');
            window.localStorage.removeItem('currency_id');
            window.localStorage.removeItem('pFees');
            window.localStorage.removeItem('fFees');
            window.localStorage.removeItem('total_fees_html');
        });
    </script>
@endsection
