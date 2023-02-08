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
                        <h3 class="sub-title">{{ __('Confirmation') }}</h3>
                        <p class="text-gray-500 text-16"> {{ __('Check your deposit information before confirm.') }}</p>
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
                                    <li class="active">{{ __('Success') }}</li>
                                </ol>
                            </div>
                            
                            <div class="bg-secondary rounded mt-5 shadow p-35">
                                @include('user_dashboard.layouts.common.alert')
								<div>
									<div class="d-flex flex-wrap">
										<div>
											<p >@lang('message.dashboard.deposit.deposit-via')</p>
										</div>

										<div class="pl-2">
                                            <span class="font-weight-600">{{ $transInfo['payment_name'] }}</span>
										</div>
									</div>

									<div class="mt-4">
										<p class="sub-title">@lang('message.dashboard.confirmation.details')</p>
									</div>

									<div>
										<div class="d-flex flex-wrap justify-content-between mt-2">
											<div>
												<p>@lang('message.dashboard.deposit.deposit-amount')</p>
											</div>

											<div class="pl-2">
												<p>{{ moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['amount'], $transInfo['currency_id'])) }}</p>
											</div>
										</div>

										<div class="d-flex flex-wrap justify-content-between mt-2">
											<div>
												<p>@lang('message.dashboard.confirmation.fee')</p>
											</div>

											<div class="pl-2">
												<p>{{ moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['fee'], $transInfo['currency_id'])) }}</p>
											</div>
										</div>
										<hr class="mb-2">

										<div class="d-flex flex-wrap justify-content-between">
											<div>
												<p class="font-weight-600">@lang('message.dashboard.confirmation.total')</p>
											</div>

											<div class="pl-2">
												<p class="font-weight-600">{{ moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['totalAmount'], $transInfo['currency_id'])) }}</p>
											</div>
										</div>
									</div>


									<div class="row m-0 mt-4 justify-content-between">
										<div>
											<a href="#" class="deposit-confirm-back-link">
												<p class="py-2 text-active text-underline deposit-confirm-back-btn mt-2"><u><i class="fas fa-long-arrow-alt-left"></i> @lang('message.dashboard.button.back')</u></p>
											</a>
										</div>

										<div>
											<form action="{{url('deposit/store')}}" style="display: block;" method="POST" accept-charset="UTF-8" id="deposit_form" novalidate="novalidate" enctype="multipart/form-data">
												<input value="{{csrf_token()}}" name="_token" id="token" type="hidden">
												<input value="{{$transInfo['payment_method']}}" name="method" id="method" type="hidden">
												<input value="{{$transInfo['totalAmount']}}" name="amount" id="amount" type="hidden">
												<button type="submit" class="btn btn-primary px-4 py-2 mt-2" id="deposit-money-confirm">
													<i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="deposit-money-confirm-text" style="font-weight: bolder;">@lang('message.dashboard.button.confirm')</span>
												</button>
											</form>
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

@include('user_dashboard.layouts.common.help')
@endsection

@section('js')
<script src="{{ theme_asset('public/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ theme_asset('public/js/additional-methods.min.js') }}" type="text/javascript"></script>

<script>

    function depositBack()
    {
        window.localStorage.setItem("depositConfirmPreviousUrl",document.URL);
        window.history.back();
    }

    jQuery.extend(jQuery.validator.messages, {
        required: "{{ __('This field is required.') }}",
    })

    $('#deposit_form').validate({
        rules: {
            amount: {
                required: false,
            },
            method: {
                required: false,
            },
        },
        submitHandler: function(form)
        {
            $("#deposit-money-confirm").attr("disabled", true);
            $(".spinner").show();
            var pretext=$("#deposit-money-confirm-text").text();
            $("#deposit-money-confirm-text").text("{{ __('Confirming...') }}");

            //Make back button disabled and prevent click
            $('.deposit-confirm-back-btn').attr("disabled", true).click(function (e)
            {
                e.preventDefault();
            });

            //Make back anchor prevent click
            $('.deposit-confirm-back-link').click(function (e)
            {
                e.preventDefault();
            });

            form.submit();

            setTimeout(function(){
                $("#deposit-money-confirm").removeAttr("disabled");
                $(".spinner").hide();
                $("#deposit-money-confirm-text").text(pretext);
            },10000);
        }
    });

    //Only go back by back button, if submit button is not clicked
    $(document).on('click', '.deposit-confirm-back-btn', function (e)
    {
        e.preventDefault();
        depositBack();
    });

</script>
@endsection
