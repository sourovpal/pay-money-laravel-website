@extends('user_dashboard.layouts.app')

@section('css')
	<link rel="stylesheet" type="text/css" href="{{ asset('Modules/BlockIo/Resources/assets/user/css/crypto_send_receive.min.css') }}">
@endsection

@section('content')
<section class="min-vh-100">
    <div class="my-3">
        <div class="container-fluid">
            <!-- Page title start -->
            <div>
                <h3>{{ __('Send :x', ['x' => $walletCurrencyCode]) }}</h3>
            </div>
            <div class="row mt-4">
                <div class="col-lg-4">
                    <!-- Sub title start -->
                    <div class="mt-5">
                        <h3 class="sub-title">{{ __('Success') }}</h3>
                        <p class="text-gray-500 text-16">{{ __('Coin has been transferred to the address. Amount will be added to the user wallet after 1 confirmation.') }}</p>
                    </div>
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

                            <div class="bg-secondary rounded mt-5 shadow py-4 p-35">
                                <div class="card-body">
                                    <div class="d-flex justify-content-center">
                                        <div class="confirm-check"><i class="fa fa-check"></i></div>
                                    </div>

                                    <div class="text-center" class="mt-20">
                                        <div class="h3 sub-title">{{ __('Success') }}!</div>
                                    </div>

                                    <div class="text-center">
                                        <p><strong>{{ __(':x Sent Successfully.', ['x' => $walletCurrencyCode]) }}</strong></p>
                                    </div>

                                    <div class="text-center" class="mt-10">
                                        <p><strong>{{ __('Amount will be added after :x confirmations.', ['x' => $confirmations]) }}</strong></p>
                                    </div>

                                    <div class="text-center" class="mt-10">
                                        <h6>{{ __('Address') }}:</h6>
                                        <strong>
                                            {!! $receiverAddress !!}
                                        </strong>
                                    </div>

                                    <h5 class="text-center" class="mt-10">
                                        <p>{{ __('Sent Amount') }}: {{ moneyFormat($currencySymbol, formatNumber($amount, $currencyId)) }}</p>
                                    </h5>
                                    <div class="mt-5">
                                        <div class="text-center">
                                            <!-- Print Button -->
                                            <a href="{{ route('user.crypto_send_receive.print', [encrypt($transactionId)]) }}" target="_blank" class="btn btn-grad mr-2 mt-4"><strong>{{ __('Print') }}</strong></a>
                                            
                                            <!-- Send Crypto Again Button -->
                                            <a href="{{ route('user.crypto_send.create', [encrypt($walletCurrencyCode), encrypt($walletId)]) }}" class="btn btn-primary ml-2 mt-4">
                                                <strong>{{ __('Send :x Again', ['x' => $walletCurrencyCode]) }}</strong>
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
    <script src="{{ asset('Modules/BlockIo/Resources/assets/user/js/crypto_send_receive.min.js') }}" type="text/javascript"></script>
@endsection