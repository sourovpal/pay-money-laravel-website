@extends('user_dashboard.layouts.app')

@section('css')

@endsection

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
                    <a href="{{url('/payouts')}}">
                        <div class="mr-4 pb-3">
                            <p class="text-16 font-weight-400 text-gray-500">{{ __('Payout list') }}</p>
                        </div>
                    </a>

                    <a href="{{url('/payout/setting')}}">
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
                                <h3 class="sub-title">{{ __('Payout confirmation') }}</h3>
                                <p class="text-gray-500 text-16 text-justify">{{ __('Please take a look before you confirm. After the confirmation the administrator review the withdrawal and fund amount to your Paypal or Bank account.') }}</p>
                            </div>
                            <!-- Sub title end-->
                        </div>

                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-10">
                                    <div class="d-flex w-100 mt-4">
                                        <ol class="breadcrumb w-100">
                                            <li class="breadcrumb-active text-white">{{ __('Create') }}</li>
                                            <li class="breadcrumb-first text-white">{{ __('Confirmation') }}</li>
                                            <li class="active">{{ __('Success') }}</li>
                                        </ol>
                                    </div>
                                   
                                    <div class="bg-secondary rounded p-35 mt-5 shadow">
                                        @include('user_dashboard.layouts.common.alert')
                                        <div>
                                            <div class="d-flex flex-wrap">
                                                <div>
                                                    <p>@lang('message.dashboard.payout.new-payout.withdraw-via')</p>
                                                </div>
        
                                                <div class="pl-2">
                                                    <p class="font-weight-600"> {{ ucwords($transInfo['payout_setting']->paymentMethod->name) }}</p>
                                                </div>
                                            </div>
        
                                            @if ( isset($transInfo['payout_setting']->paymentMethod) && $transInfo['payout_setting']->paymentMethod->name == 'Bank')
                                                <p class="mb20"> @lang('message.dashboard.payout.payout-setting.modal.bank-account-holder-name')&nbsp;&nbsp;: <b>{{ $transInfo['payout_setting']->account_name }}</b></p>
                                                <p class="mb20"> @lang('message.dashboard.payout.payout-setting.modal.account-number')&nbsp;&nbsp;: <b>{{ $transInfo['payout_setting']->account_number }}</b></p>
                                                <p class="mb20"> @lang('message.dashboard.payout.payout-setting.modal.swift-code')&nbsp;&nbsp;: <b>{{ $transInfo['payout_setting']->swift_code }}</b></p>
                                                <p class="mb20"> @lang('message.dashboard.payout.payout-setting.modal.bank-name')&nbsp;&nbsp;: <b>{{ $transInfo['payout_setting']->bank_name }}</b></p>
                                            @endif
        
                                            <div class="mt-4">
                                                <p class="sub-title">@lang('message.dashboard.confirmation.details')</p>
                                            </div>
                                            <div>
                                                <div class="d-flex flex-wrap justify-content-between mt-2">
                                                    <div>
                                                        <p>@lang('message.dashboard.left-table.withdrawal.withdrawan-amount')</p>
                                                    </div>
        
                                                    <div class="pl-2">
                                                        <p>{{  moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['amount'], $transInfo['currency_id'])) }}</p>
                                                    </div>
                                                </div>
        
                                                <div class="d-flex flex-wrap justify-content-between mt-2">
                                                    <div>
                                                        <p>@lang('message.dashboard.confirmation.fee')</p>
                                                    </div>
        
                                                    <div class="pl-2">
                                                        <p>{{  moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['fee'], $transInfo['currency_id'])) }}</p>
                                                    </div>
                                                </div>
        
                                                <hr class="mb-2">
        
                                                <div class="d-flex flex-wrap justify-content-between">
                                                    <div>
                                                        <p class="font-weight-600">@lang('message.dashboard.confirmation.total')</p>
                                                    </div>
        
                                                    <div class="pl-2">
                                                        <p class="font-weight-600">{{  moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['totalAmount'], $transInfo['currency_id'])) }}</p>
                                                    </div>
                                                </div>
                                            </div>
        
        
                                            <div class="row m-0 mt-4 justify-content-between">
                                                <div>
                                                    <a href="#" class="withdrawal-confirm-back-link">
                                                        <p class="py-2 text-active text-underline withdrawal-confirm-back-btn mt-2"><u><i class="fas fa-long-arrow-alt-left"></i> @lang('message.dashboard.button.back')</u></p>
                                                    </a>
                                                </div>
        
                                                <div>
                                                    <a href="{{ url('withdrawal/confirm-transaction') }}" class="withdrawal-confirm-submit-link">
                                                        <button class="btn btn-primary px-4 py-2  withdrawal-confirm-submit-btn mt-2">
                                                            <i class="fa fa-spinner fa-spin" style="display: none;" id="spinner"></i>
                                                            <strong>
                                                                <span class="withdrawal-confirm-submit-btn-txt">
                                                                    @lang('message.dashboard.button.confirm')
                                                                </span>
                                                            </strong>
                                                        </button>
                                                    </a>
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

<script src="{{ theme_asset('public/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script>
    function payoutBack()
    {
        localStorage.setItem("payoutConfirmPreviousUrl",document.URL);
        window.history.back();
    }

    $(document).on('click', '.withdrawal-confirm-submit-btn', function (e)
    {
        $(".fa-spin").show()
        $('.withdrawal-confirm-submit-btn-txt').text("{{ __('Confirming...') }}");
        $(this).attr("disabled", true);
        $('.withdrawal-confirm-submit-link').click(function (e) {
            e.preventDefault();
        });

        //Make back button disabled and prevent click
        $('.withdrawal-confirm-back-btn').attr("disabled", true).click(function (e)
        {
            e.preventDefault();
        });

        //Make back anchor prevent click
        $('.withdrawal-confirm-back-link').click(function (e)
        {
            e.preventDefault();
        });
    });

    //Only go back by back button, if submit button is not clicked
    $(document).on('click', '.withdrawal-confirm-back-btn', function (e)
    {
        e.preventDefault();
        payoutBack();
    });
</script>

@endsection
