@extends('user_dashboard.layouts.app')
@section('content')
<section class="min-vh-100">
  <div class="my-30">
      <div class="container-fluid">
          <!-- Page title start -->
          <div>
              <h3 class="page-title">{{ __('Send Money') }}</h3>
          </div>
          <!-- Page title end-->
          <div class="row mt-4">
              <div class="col-lg-4">
                  <!-- Sub title start -->
                  <div class="mt-5">
                      <h3 class="sub-title">{{ __('Success') }}</h3>
                      <p class="text-gray-500 text-16 text-justify">{{ __('The recipient will be notified via an email or phone number after money has been successfully transferred to their account.') }}</p>
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

                          <div class="bg-secondary rounded mt-5 mb-30 shadow p-35">
                                <div>
                                  <div class="d-flex justify-content-center">
                                    <div class="confirm-check"><i class="fa fa-check"></i></div>
                                </div>

                                <div class="text-center mt-4">
                                    <p class="sub-title">@lang('message.dashboard.send-request.request.confirmation.success')!</p>
                                </div>

                                <!-- check mail error message-->
                                @if (!empty($errorMessage))
                                  <div class="text-center">
                                    <p class="font-weight-600">@lang('message.dashboard.send-request.send.confirmation.money-send') @lang('message.dashboard.mail-not-sent').</p>
                                  </div>
                                @else
                                  <div class="text-center">
                                    <p class="font-weight-600">@lang('message.dashboard.send-request.send.confirmation.money-send').</p>
                                  </div>
                                @endif

                                <div class="text-center mt-4">
                                  @if(!empty($userPic))
                                    <img src="{{ url('public/user_dashboard/profile') }}/{{ $userPic }}" class="success-p-img rounded">
                                  @else
                                    <img src="{{ theme_asset('public/images/profile/profile.png') }}" class="success-p-img rounded">
                                  @endif
                                </div>

                                <p class="text-center font-weight-600 mt-4">{{ !empty($receiverName)?$receiverName:$transInfo['receiver'] }}</p>
                                <p class="text-center">@lang('message.dashboard.send-request.send.confirmation.transfer-amount') : {{ moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['amount'], $transInfo['currency_id'])) }}</p>

                                <div class="mt-4">
                                    <div class="text-center">
                                        <a href="{{ url('moneytransfer/print') }}/{{ $transInfo['trans_id'] }}" target="_blank" class="btn btn-grad mr-2 mt-4"><strong>@lang('message.dashboard.vouchers.success.print')</strong></a>
                                        <a href="{{ url('moneytransfer') }}" class="btn btn-primary px-4 py-2 ml-2 mt-4"><strong>@lang('message.dashboard.send-request.send.confirmation.send-again')</strong></a>
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
    //window.history.forward(1);
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
