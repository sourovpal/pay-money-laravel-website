@extends('user_dashboard.layouts.app')
@section('css')
<style>
    .jst-hours {
    float: left;
    }
    .jst-minutes {
    float: left;
    }
    .jst-seconds {
    float: left;
    }
    .jst-clearDiv {
    clear: both;
    }
    .jst-timeout {
    color: red;
    }

</style>
@endsection

@section('content')
    <section class="min-vh-100">
        <div class="my-30">
            <div class="container-fluid">
                <!-- Page title start -->
                <div>
                    <h3 class="page-title">{{ __('Transaction Summery') }}</h3>
                </div>
                <!-- Page title end-->

                <div class="row mt-5 justify-content-center">
                    <div class="col-md-12 col-lg-8  px-0 shadow bg-secondary py-4">
                        <div class="row px-4 py-2">
                            <div class="col-md-5">
                                <p class="text-lg-right"> {{ __('Status') }} :</p>
                            </div>

                            <div class="col-md-7">
                                <p>{{ $transactionInfo['result']['status_text'] }}</p>
                            </div>
                        </div>
                        <hr class="mt-2 mb-0">

                        <div class="row px-4 py-2">
                            <div class="col-md-5">
                                <p class="text-lg-right"> {{ __('Total amount to send') }} :</p>
                            </div>

                            <div class="col-md-7">
                                <p>{{ $transactionInfo['result']['amountf'] . ' ' . $transactionInfo['result']['coin'] }} ({{ __('Total confirms need') }}:
                                    {{ $transactionDetails['result']['confirms_needed'] }})</p>
                            </div>
                        </div>
                        <hr class="mt-2 mb-0">

                        <div class="row px-4 py-2">
                            <div class="col-md-5">
                                <p class="text-lg-right"> {{ __('Receive so far') }} :</p>
                            </div>

                            <div class="col-md-7">
                                <p>{{ $transactionInfo['result']['receivedf'] . ' ' . $transactionInfo['result']['coin'] }}</p>
                            </div>
                        </div>
                        <hr class="mt-2 mb-0">

                        <div class="row px-4 py-2">
                            <div class="col-md-5">
                                <p class="text-lg-right"> {{ __('Balance Remaining') }}:</p>
                            </div>

                            <div class="col-md-7">
                                <p>{{ $transactionInfo['result']['amountf'] . ' ' . $transactionInfo['result']['coin'] }}</p>
                            </div>
                        </div>
                        <hr class="mt-2 mb-0">

                        <div class="row justify-content-center">
                            <img src="{{ $transactionDetails['result']['qrcode_url'] }}" alt="image">
                        </div>
                        <div class="text-danger text-center"><small>
                                {{ __('Do not send value to us if address status is expired') }}!
                            </small></div>
                        <hr class="mt-2 mb-0">

                        <div class="row px-4 py-2">
                            <div class="col-md-5">
                                <p class="text-lg-right"> {{ __('Send To Address') }}:</p>
                            </div>

                            <div class="col-md-7">
                                <p class="text-break">{{ $transactionInfo['result']['payment_address'] }}</p>
                            </div>
                        </div>
                        <hr class="mt-2 mb-0">

                        <div class="row px-4 py-2">
                            <div class="col-md-5">
                                <p class="text-lg-right"> {{ __('Time Left For Us to Confirm Funds') }}:</p>
                            </div>

                            <div class="col-md-7">
                                <input type="hidden" name="" value="{{ $transactionInfo['result']['time_created'] }}"
                                    id="time_created">
                                <input type="hidden" name="" value="{{ $transactionInfo['result']['time_expires'] }}"
                                    id="time_expires">
                                <strong>
                                    <p class='timer' data-seconds-left=""></p>
                                </strong>
                            </div>
                        </div>
                        <hr class="mt-2 mb-0">
                        <div class="row px-4 py-2">
                            <div class="col-md-5">
                                <p class="text-lg-right"> {{ __('Payment ID') }}:</p>
                            </div>

                            <div class="col-md-7">
                                <p>{{ $transactionDetails['result']['txn_id'] }}</p>
                            </div>
                        </div>

                        <hr class="mt-2 mb-0">
                        <p class="text-center"> <a href="{{ $transactionDetails['result']['status_url'] }}"
                                target="_blank">{{ __('Alternative Link') }}</a> | <a href="{{ url('transactions') }}">{{ __('Transaction Histories') }}</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('user_dashboard.layouts.common.help')
@endsection

@section('js')

    <script src="{{ theme_asset('public/js/jquery.simple.timer.js') }}"></script>

    <script type="text/javascript">

            $(function(){
                var time_created = $('#time_created').val();
                var time_expires = $('#time_expires').val();

                var time = time_expires - time_created;

                $('.timer').attr('data-seconds-left', time)

                $('.timer').startTimer({
                    onComplete: function(){
                        console.log('Complete');
                    }
                });

            })

    </script>
@endsection
