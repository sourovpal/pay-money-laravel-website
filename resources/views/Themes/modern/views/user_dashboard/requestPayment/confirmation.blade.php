@extends('user_dashboard.layouts.app')

@section('content')
<section class="min-vh-100">
    <div class="my-30">
        <div class="container-fluid">
            <!-- Page title start -->
            <div>
                <h3 class="page-title">{{ __('Request money') }}</h3>
            </div>
            <!-- Page title end-->

            <div class="row mt-4">
                <div class="col-lg-4">
                    <!-- Sub title start -->
                    <div class="mt-5">
                        <h3 class="sub-title">{{ __('Confirmation') }}</h3>
                        <p class="text-gray-500 text-16 text-justify">{{ __('Take a look before sending request. Don\'t worry, if the payer does not have a account, we will get them set up for free.') }}</p>
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
									<div class="d-flex flex-wrap">
										<div>
											<p>
												@lang('message.dashboard.send-request.request.confirmation.request-money-from')
												<span class="font-weight-600">{{ isset($transInfo['email']) ? $transInfo['email'] : '' }}</span>
											</p>
										</div>
									</div>

									<div class="mt-4">
										<p class="sub-title">@lang('message.dashboard.confirmation.details')</p>
									</div>

									<div>
										<div class="d-flex flex-wrap justify-content-between mt-2">
											<div>
												<p>@lang('message.dashboard.send-request.request.confirmation.requested-amount')</p>
											</div>

											<div class="pl-2">
												<p>{{  moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['amount'], $transInfo['currency_id'])) }}</p>
											</div>
										</div>
									</div>


									<div class="row m-0 mt-4 justify-content-between">
										<div>
											<a href="#" class="request-create-confirm-back-link">
												<p class="text-active text-underline request-create-confirm-back-btn mt-2"><u><i class="fas fa-long-arrow-alt-left"></i> @lang('message.dashboard.button.back')</u></p>
											</a>
										</div>


										<div>
											<a href="{{ url('request-money-confirm') }}" class="request-create-confirm-submit-link">
												<button class="btn btn-primary px-4 py-2 ml-2 float-right request-create-confirm-submit-btn">
													<i class="fa fa-spinner fa-spin d-none" id="spinner"></i>
													<strong>
														<span class="request-create-confirm-submit-btn">
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
</section>
@endsection

@section('js')

<script type="text/javascript">

	$(document).on('click', '.request-create-confirm-submit-btn', function (e)
    {
    	$(".fa-spin").removeClass("d-none");
    	$('.request-create-confirm-submit-btn-txt').text("{{ __('Confirming...') }}");
    	$(this).attr("disabled", true);
    	$('.request-create-confirm-submit-link').click(function (e) {
            e.preventDefault();
        });

        //Make back button disabled and prevent click
        $('.request-create-confirm-back-btn').attr("disabled", true).click(function (e)
        {
            e.preventDefault();
        });

        //Make back anchor prevent click
        $('.request-create-confirm-back-link').click(function (e)
        {
            e.preventDefault();
        });
    });

    //Only go back by back button, if submit button is not clicked
    $(document).on('click', '.request-create-confirm-back-btn', function (e)
    {
        e.preventDefault();
        window.history.back();
    });

</script>

@endsection
