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
                        <h3 class="sub-title">{{ __('Confirmation') }}</h3>
                        <p class="text-gray-500 text-16 text-justify">{{ __('Save time and exchange your currency at an attractive rate. You are just one click away to exchange your currency.') }}</p>
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
								<div>
									<p class="text-center">
										{{ __('You will get ') }} <strong>{{ isset($transInfo['finalAmount']) ? formatNumber($transInfo['finalAmount']) : 0.00 }} {{ $transInfo['currCode'] }}</strong>

									</p>
									<p class="text-center mt-1">
										{{ __('Rate') }}:
										&nbsp;1 {{$fromCurrency->code}}  = {{ ($transInfo['dCurrencyRate']) }} {{ $transInfo['currCode'] }}
									</p>


									<div class="mt-2">
										<p class="sub-title">@lang('message.dashboard.confirmation.details')</p>
									</div>

									<div class="d-flex mt-2 justify-content-between">
										<div class="pr-2">
											<p>@lang('message.dashboard.exchange.confirm.amount')</p>
										</div>
										<div>
											<p>{{  moneyFormat($fromCurrency->symbol, isset($transInfo['defaultAmnt']) ? formatNumber($transInfo['defaultAmnt']) : 0.00) }}</p>
										</div>
									</div>

									<div class="d-flex justify-content-between">
										<div class="pr-2">
											<p>@lang('message.dashboard.confirmation.fee')</p>
										</div>
										<div>
											<p>{{  moneyFormat($fromCurrency->symbol, isset($transInfo['fee']) ? formatNumber($transInfo['fee']) : 0.00) }}</p>
										</div>
									</div>
									<hr class="mb-2 mt-0">

									<div class="d-flex justify-content-between">
										<div class="pr-2">
											<p class="font-weight-600">@lang('message.dashboard.confirmation.total')</p>
										</div>
										<div>
											<p class="font-weight-600">{{  moneyFormat($fromCurrency->symbol, isset($transInfo['totalAmount']) ? formatNumber($transInfo['totalAmount']) : 0.00) }}</p>
										</div>
									</div>
								</div>

								<div class="row m-0 mt-4 justify-content-between">
									<div>
										<a href="#" class="exchange-confirm-back-link">
											<p class="py-2 text-active text-underline exchange-confirm-back-btn mt-2">
												<u><i class="fas fa-long-arrow-alt-left"></i> @lang('message.dashboard.button.back')</u>
											</p>
										</a>
									</div>
									<div>
										<a href="{{url('exchange-of-money-success')}}" class="exchange-confirm-submit-link">
											<button class="btn btn-primary px-4 py-2 float-right exchange-confirm-submit-btn mt-2">
												<i class="fa fa-spinner fa-spin d-none" id="spinner"></i>
												<strong>
													<span class="exchange-confirm-submit-btn-txt">
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
</section>
@endsection

@section('js')

<script type="text/javascript">

	function exchangeBack()
	{
		localStorage.setItem("previousUrl",document.URL);
		window.history.back();
	}

	$(document).on('click', '.exchange-confirm-submit-btn', function (e)
    {
    	$(".fa-spin").removeClass("d-none");
    	$('.exchange-confirm-submit-btn-txt').text("{{ __('Confirming...') }}");
    	$(this).attr("disabled", true);
    	$('.exchange-confirm-submit-link').click(function (e) {
            e.preventDefault();
        });

        //Make back button disabled and prevent click
        $('.exchange-confirm-back-btn').attr("disabled", true).click(function (e)
        {
            e.preventDefault();
        });

        //Make back anchor prevent click
        $('.exchange-confirm-back-link').click(function (e)
        {
            e.preventDefault();
        });
    });

    //Only go back by back button, if submit button is not clicked
    $(document).on('click', '.exchange-confirm-back-btn', function (e)
    {
        e.preventDefault();
        exchangeBack();
    });

</script>

@endsection
