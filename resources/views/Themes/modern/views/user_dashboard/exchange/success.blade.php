@extends('user_dashboard.layouts.app')

@section('content')
<section class="min-vh-100">
    <div class="my-30">
        <div class="container-fluid">
            <!-- Page title start -->
            <div>
                <h3 class="page-title">{{ __('Exchange Currency') }}</h3>
            </div>
            <!-- Page title end-->

            <div class="row mt-4">
                <div class="col-lg-4">
                    <!-- Sub title start -->
                    <div class="mt-5">
                        <h3 class="sub-title">{{ __('Success') }}</h3>
                        <p class="text-gray-500 text-16">{{ __('Currency exchange has been completed successfully') }}.</p>
                    </div>
                    <!-- Sub title end-->
                </div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-xl-10">
                            <div class="d-flex w-100 mt-4">
                                <ol class="breadcrumb w-100">
                                    <li class="breadcrumb-active text-white">{{ __('Create') }}</li>
                                    <li class="breadcrumb-first text-white">{{ __('Confirmation') }}</li>
                                    <li class="breadcrumb-success text-white">{{ __('Success') }}</li>
                                </ol>
                            </div>

                            <div class="bg-secondary rounded mt-5 shadow p-35">
                                <div>
                                    <div class="d-flex justify-content-center">
                                        <div class="confirm-check"><i class="fa fa-check"></i></div>
                                    </div>

									<div class="text-center mt-4 mb-2">
                                        <p class="sub-title"> @lang('message.dashboard.deposit.success')!</p>
                                    </div>
                                    <p class="text-center">
										{{ __('Exchange amount ') }}  <strong>{{ moneyFormat($transInfo['currSymbol'], isset($transInfo['finalAmount']) ? formatNumber($transInfo['finalAmount'], $transInfo['currency_id']) : 0.00) }}</strong>
									</p>
                                    <p class="text-center mt-1">
										{{ __('Rate') }}:
										&nbsp; 1 {{$fromWallet->currency->code}} = {{ ($transInfo['dCurrencyRate']) }} {{$transInfo['currCode']}}
									</p>

									<div class="mt-4">
										<div class="text-center">
											<a href="{{url('exchange-money/print')}}/{{$transInfo['trans_ref_id']}}" target="_blank" class="btn btn-grad mr-2 mt-4"><i class="fa fa-print"></i> &nbsp;
												@lang('message.dashboard.vouchers.success.print')
											</a>
											<a href="{{url('exchange')}}" class="btn btn-primary px-4 py-2 ml-2 mt-4">@lang('message.dashboard.exchange.confirm.exchange-money-again')</a>
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
