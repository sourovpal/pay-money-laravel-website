@extends('user_dashboard.layouts.app')

@section('css')
	<link rel="stylesheet" type="text/css" href="{{ asset('Modules/BlockIo/Resources/assets/user/css/crypto_send_receive.min.css') }}">
@endsection

@section('content')
<section class="min-vh-100">
	<div class="my-3">
		<div class="container-fluid" id="crypto-send-confirm">
			<!-- Page title start -->
			<div>
				<h3>{{ __('Send :x', ['x' => $walletCurrencyCode]) }}</h3>
			</div>
			<div class="row mt-4">
				<div class="col-lg-4">
					<!-- Sub title start -->
					<div class="mt-5">
						<h3 class="sub-title">{{ __('Confirmation') }}</h3>
						<p class="text-gray-500 text-16">{{ __('Take a look before you send. Once the coin sent to this address, its never be undone.') }}</p>
					</div>
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
								<div>

									<div class="card-body p-0">
										<p>
											<div class="float-left">
												{{ __('You are about to send :x to', ['x' => $walletCurrencyCode]) }}&nbsp;
											</div>
											<div class="word-break">
												<strong>{{ $cryptoTrx['receiverAddress'] }}</strong>
											</div>
										</p>
									</div>

									<div class="mt-4">
										<p class="sub-title">{{ __('Details') }}</p>
									</div>

									<div>
										<div class="d-flex flex-wrap justify-content-between mt-2">
											<div>
												<p>{{ __('Sent Amount') }}</p>
											</div>

											<div class="pl-2">
												<p>{{ moneyFormat($cryptoTrx['currencySymbol'], formatNumber($cryptoTrx['amount'], $currencyId)) }}</p>
											</div>
										</div>

										<div class="d-flex flex-wrap justify-content-between mt-2">
											<div>
												<p>{{ __('Estimate Network Fee') }}</p>
											</div>

											<div class="pl-2">
												<p>{{ moneyFormat($cryptoTrx['currencySymbol'], formatNumber($cryptoTrx['networkFee'], $currencyId)) }}</p>
											</div>
										</div>
									</div>
									<hr class="mb-2">

									<div class="d-flex flex-wrap justify-content-between">
										<div>
											<p class="font-weight-600">{{ __('Total') }}</p>
										</div>

										<div class="pl-2">
											<p class="font-weight-600">{{ moneyFormat($cryptoTrx['currencySymbol'], formatNumber($cryptoTrx['amount'] + $cryptoTrx['networkFee'], $currencyId)) }}</p>
										</div>
									</div>


									<div class="row m-0 mt-4 justify-content-between">
										<div>
											<a href="{{ route('user.crypto_send.create', [encrypt($walletCurrencyCode), encrypt($walletId)]) }}" class="crypto-send-confirm-back-link">
												<p class="text-active text-underline crypt-send-confirm-back-button mt-2"><u><i class="fas fa-long-arrow-alt-left"></i> {{ __('Back') }}</u></p>
											</a>
										</div>


										<div>
											<a href="{{ route('user.crypto_send.success') }}" class="crypto-send-confirm-link">
												<button class="btn btn-primary px-4 py-2 ml-2 float-right crypto-send-confirm">
													<i class="fa fa-spinner fa-spin d-none" id="spinner"></i>
													<strong>
														<span class="crypto-send-confirm-text">{{ __('Confirm') }} &nbsp;</span>
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
		'use strict';
		var cryptoSentCreateUrl = '{{ route("user.crypto_send.create", [encrypt(strtolower($walletCurrencyCode)), encrypt($walletId)]) }}';
		var confirming = '{{ __("Confirming...") }}';
	</script>
	<script src="{{ asset('Modules/BlockIo/Resources/assets/user/js/crypto_send_receive.min.js') }}" type="text/javascript"></script>

@endsection