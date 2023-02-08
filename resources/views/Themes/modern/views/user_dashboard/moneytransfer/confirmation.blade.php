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
                        <h3 class="sub-title">{{ __('Confirmation') }}</h3>
                        <p class="text-gray-500 text-16 text-justify">{{ __('Take a look before you send. Don\'t worry, if the recipient does not have an account, we will get them set up for free.') }}</p>
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

                            <div class="bg-secondary rounded mt-5 shadow py-4 p-35">
                                @include('user_dashboard.layouts.common.alert')
                                <div>
									<div class="d-flex flex-wrap">
										<div>
											<p class="font-weight-600">
												@lang('message.dashboard.send-request.send.confirmation.send-to')
												<strong>{{ isset($transInfo['receiver']) ? $transInfo['receiver'] : '' }}</strong>
											</p>
										</div>
									</div>

									<div class="mt-4">
										<p class="sub-title">@lang('message.dashboard.confirmation.details')</p>
									</div>

									<div>
										<div class="d-flex flex-wrap justify-content-between mt-2">
											<div>
												<p>@lang('message.dashboard.send-request.send.confirmation.transfer-amount')</p>
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


									<div class="row m-0 mt-4 justify-content-between">
										<div>
											<a href="#" class="send-money-confirm-back-link">
												<p class="text-active text-underline send-money-confirm-back-button mt-2"><u><i class="fas fa-long-arrow-alt-left"></i> @lang('message.dashboard.button.back')</u></p>
											</a>
										</div>


										<div>
											<a href="{{url('send-money-confirm')}}" class="sendMoneyPaymentConfirmLink">
												<button class="btn btn-primary px-4 py-2 ml-2 float-right sendMoneyConfirm">
													<i class="fa fa-spinner fa-spin" style="display: none;" id="spinner"></i>
													<strong>
														<span class="sendMoneyConfirmText">
															@lang('message.dashboard.button.confirm') &nbsp;
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
</section>
@endsection

@section('js')
<script type="text/javascript">

	$(document).on('click', '.sendMoneyConfirm', function (e)
    {
    	$(".fa-spin").show()
    	$('.sendMoneyConfirmText').text("{{ __('Confirming...') }}");
    	$(this).attr("disabled", true);
    	$('.sendMoneyPaymentConfirmLink').click(function (e)
        {
            e.preventDefault();
        });

        //Make back button disabled and prevent click
        $('.send-money-confirm-back-button').attr("disabled", true).click(function (e)
        {
            e.preventDefault();
        });

        //Make back anchor prevent click
    	$('.send-money-confirm-back-link').click(function (e)
        {
            e.preventDefault();
        });
    });

	//Only go back by back button, if submit button is not clicked
    $(document).on('click', '.send-money-confirm-back-button', function (e)
    {
    	e.preventDefault();
        window.history.back();
    });

</script>
@endsection
