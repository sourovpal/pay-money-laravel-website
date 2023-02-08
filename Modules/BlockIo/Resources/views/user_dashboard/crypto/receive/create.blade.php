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
                <h3 class="page-title">{{ __('Receive :x', ['x' => strtoupper($walletCurrencyCode)]) }}</h3>
            </div>
            <!-- Page title end-->
            <div class="row mt-4" id="crypto-receive-create">
                <div class="col-md-4">
                </div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="bg-secondary mt-3 shadow p-35">
                                @include('user_dashboard.layouts.common.alert')

                                <form method="POST" action="" id="transfer_form" accept-charset='UTF-8'>
                                    <input type="hidden" value="{{ csrf_token() }}" name="_token" id="token">
                                    <div>
                                        <div class="text-center">
                                            <h3>{{ __('Receiving Address Qr Code') }}</h3>
                                            <div class="mt-5 d-flex justify-content-center" id="wallet-address"></div>
                                            <br>
                                            <small class="form-text text-muted">{!! __('<b>Only receive :x to this address</b>, receiving any other coin will result in permanent loss.', ['x' => strtoupper($walletCurrencyCode)]) !!}</small>
                                            <br>
                                        </div>

                                        <div class="">
                                            <div class="form-group">
                                                <label>{{ __('Receiving Address') }}</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control" id="wallet-address-input" value="{{ decrypt($address) }}" readonly >
                                                    <div class="input-group-append wallet-address-copy-btn">
                                                        <span class="input-group-text btn-primary copy-button">{{ __('Copy') }}</span>
                                                    </div>
                                                </div>
                                            </div>
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

<script src="{{ theme_asset('public/js/jquery-qrcode/jquery.qrcode.min.js') }}" type="text/javascript"></script>
<script src="{{ theme_asset('public/js/jquery-qrcode/qrcode.js') }}" type="text/javascript"></script>
<script src="{{ theme_asset('public/js/sweetalert/sweetalert-unpkg.min.js') }}" type="text/javascript"></script>

<script>
    var copied = "{{ __('Copied!') }}";
    var addressCopyText = "{{ __('Address Copied!') }}";
    var addressText = "{{ decrypt($address) }}";
</script>
<script src="{{ asset('Modules/BlockIo/Resources/assets/user/js/crypto_send_receive.min.js') }}" type="text/javascript"></script>

@endsection
