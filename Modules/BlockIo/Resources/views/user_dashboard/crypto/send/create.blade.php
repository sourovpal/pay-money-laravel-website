@php
    $walletCurrencyCode = strtoupper($walletCurrencyCode);
@endphp

@extends('user_dashboard.layouts.app')  

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('Modules/BlockIo/Resources/assets/user/css/crypto_send_receive.min.css') }}">
@endsection

@section('content')
<section class="min-vh-100">
    <div class="my-3">
        <div class="container-fluid" id="crypto-send-create">
            <!-- Page title start -->
            <div>
                <h3 class="page-title">{{ __('Send :x', ['x' => $walletCurrencyCode]) }}</h3>
            </div>
            <div class="row mt-4">
                <div class="col-lg-4">
                    <!-- Sub title start -->
                    <div class="mt-5">
                        <h3 class="sub-title">{{ __('Send :x', ['x' => $walletCurrencyCode]) }}</h3>
                        <p class="text-gray-500 text-16">
                            {{ __('Enter recipient address and amount.') }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-xl-10">
                            <div class="d-flex w-100 mt-4">
                                <ol class="breadcrumb w-100">
                                    <li class="breadcrumb-first text-white">{{ __('Create') }}</li>
                                    <li>{{ __('Confirmation') }}</li>
                                    <li class="active">{{ __('Success') }}</li>
                                </ol>
                            </div>
                            <div class="bg-secondary rounded mt-5 shadow p-35">
                                @include('user_dashboard.layouts.common.alert')
                                <form accept-charset="UTF-8" action="{{ route('user.crypto_send.confirm') }}" id="crypto-send-form" method="POST">
                                    <input name="_token" type="hidden" value="{{ csrf_token() }}" id="token"/>
                                    <input name="walletCurrencyCode" type="hidden" data-type="{{ $currencyType }}" value="{{ encrypt($walletCurrencyCode) }}" id="network"/>
                                    <input name="walletId" type="hidden" value="{{ encrypt($walletId) }}"/>
                                    <input name="senderAddress" type="hidden" value="{{ encrypt($senderAddress) }}"/>
                                    
                                    <div>
                                        <!-- Address -->
                                        <div class="form-group">
                                            <label for="receiverAddress">{{ __('Recipient Address') }}</label>
                                            <input class="form-control receiverAddress" id="receiverAddress" name="receiverAddress" placeholder="{{ __('Enter valid recipient :x address', ['x' => $walletCurrencyCode]) }}" type="text" value="{{ old('receiverAddress') }}" required data-value-missing="{{ __("Please provide a :x address.", ['x' => $walletCurrencyCode]) }}">
                                        </div>
                                        <div class="form-group">
                                            <p class="receiver-address-validation-error text-danger"></p>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <small class="form-text text-muted">*{{ __('Crypto transactions might take few moments to complete.') }}</small>
                                            <small class="form-text text-muted">*{{ __('Only send :x to this address, receiving any other coin will result in permanent loss.', ['x' => $walletCurrencyCode]) }}</small>
                                        </div>

                                        <!-- Amount -->
                                        <div class="form-group">
                                            <label for="amount">{{ __('Amount') }}</label>
                                            <input class="form-control amount" id="amount" name="amount" onkeypress="return isNumberOrDecimalPointKey(this, event);"
                                                value="{{ old('amount') }}" oninput="restrictNumberToPrefdecimalOnInput(this)" placeholder="0.00000000" type="text" required data-value-missing="{{ __('Amount field is required.') }}"/>
                                            <p class="amount-validation-error text-danger"></p>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <small class="form-text text-muted">*{{ __('The amount withdrawn/sent must at least be :x :y.', ['x' => getBlockIoMinLimit('amount', $walletCurrencyCode), 'y' => $walletCurrencyCode]) }}</small>
                                            <small class="form-text text-muted">*{{ __('Please keep at least :x :y for network fees.', ['x' => getBlockIoMinLimit('networkFee', $walletCurrencyCode), 'y' => $walletCurrencyCode]) }}</small>
                                        </div>
                                        <!-- Priority -->
                                        <div class="form-group">
                                            <label for="priority">{{ __('Priority') }}</label>
                                            <select class="form-control priority" id="priority" name="priority">
                                                <option {{ old('priority') == 'low' ? 'selected' : '' }} value="low">{{ __('Low') }}</option>
                                                <option {{ old('priority') == 'medium' ? 'selected' : '' }} value="medium">{{ __('Medium') }}</option>
                                                <option {{ old('priority') == 'high' ? 'selected' : '' }} value="high">{{ __('High') }}</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <small class="form-text text-muted">*{{ __('Larger transactions incur higher network fees.') }}</small>
                                            <small class="form-text text-muted">*{{ __('You can specify the priority for your transactions to adjust the network fee you wish to pay.') }}</small>
                                        </div>
                                        <div class="mt-1">
                                            <button class="btn btn-primary px-4 py-2" id="crypto-send-submit-btn" type="submit">
                                                <i class="spinner fa fa-spinner fa-spin d-none"></i>
                                                <span id="crypto-send-submit-btn-txt" class="font-bolder">
                                                    {{ __('Send') }}
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
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
    <script src="{{ theme_asset('public/js/sweetalert/sweetalert-unpkg.min.js') }}" type="text/javascript"></script>
    <script src="{{ theme_asset('public/js/jquery.ba-throttle-debounce.js') }}" type="text/javascript"></script>
    <script src="{{ asset('Modules/BlockIo/Resources/assets/admin/js/validation.min.js') }}" type="text/javascript"></script>

    @include('common.restrict_number_to_pref_decimal')
    @include('common.restrict_character_decimal_point')

    <script type="text/javascript">
        'use strict';
        var walletCurrencyCode = '{{ $walletCurrencyCode }}';
        var senderAddress = '{{ $senderAddress }}';
        var validateAddressUrl = '{{ route("user.crypto_send.validate_address") }}';
        var validateBalanceUrl = '{{ route("user.crypto_send.validate_balance") }}';
        var cryptoSendConfirmUrl = '{{ route("user.crypto_send.confirm") }}';
        var pleaseWait = '{{ __("Please Wait") }}';
        var loading = '{{ __("Loading...") }}';
        var minCryptoAmount = '{{ __("The minimum amount must be :x.") }}';
        var sendBtn = '{{ __("Send") }}';
        var sending = '{{ __("Sending...") }}';
    </script>

    <script src="{{ asset('Modules/BlockIo/Resources/assets/user/js/crypto_send_receive.min.js') }}" type="text/javascript"></script>
@endsection
