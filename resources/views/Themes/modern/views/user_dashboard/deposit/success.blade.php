@extends('user_dashboard.layouts.app')
@section('content')
<section class="min-vh-100">
    <div class="my-30">
        <div class="container-fluid">
            <!-- Page title start -->
            <div>
                <h3 class="page-title">{{ __('Deposit Fund') }}</h3>
            </div>
            <!-- Page title end-->
            <div class="row mt-4">
                <div class="col-lg-4">
                    <!-- Sub title start -->
                    <div class="mt-5">
                        <h3 class="sub-title">{{ __('Success') }}</h3>
                        <p class="text-gray-500 text-16">{{ __('Money has been successfully deposited to your wallet. You can see the details under the transacton details.') }}</p>
                    </div>
                    <!-- Sub title end-->
                </div>
                
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-xl-10">
                            <div class="d-flex w-100 mt-5">
                                <ol class="breadcrumb w-100">
                                    <li class="breadcrumb-active text-white">{{ __('Create') }}</li>
                                    <li class="breadcrumb-first text-white">{{ __('Confirmation') }}</li>
                                    <li class="breadcrumb-success text-white">{{ __('Success') }}</li>
                                </ol>
                            </div>


                            <div class="bg-secondary mt-5 shadow p-35">
                                <div>
                                    <div class="d-flex justify-content-center">
                                        <div class="confirm-check"><i class="fa fa-check"></i></div>
                                    </div>

                                    <div class="text-center mt-4">
                                        <p class="sub-title"> @lang('message.dashboard.deposit.success')!</p>
                                    </div>

                                    <div class="text-center">
                                        <p class="mt-2">
                                            @lang('message.dashboard.deposit.completed-success')
                                        </p>
                                    </div>
                                    <p class="text-center font-weight-600 mt-2">@lang('message.dashboard.deposit.deposit-amount') : {{ moneyFormat(optional($transaction->currency)->symbol, formatNumber($transaction->subtotal)) }}</p>

                                    <div class="mt-4">
                                        <div class="text-center">
                                            <a href="{{url('deposit-money/print')}}/{{$transaction->id}}" target="_blank" class="btn btn-grad mr-2 mt-4"><strong>@lang('message.dashboard.vouchers.success.print')</strong></a>
                                            <a href="{{url('deposit')}}" class="btn btn-primary ml-2 mt-4"><strong>@lang('message.dashboard.deposit.deposit-again')</strong></a>
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
</script>
@endsection
