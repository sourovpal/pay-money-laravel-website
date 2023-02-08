@extends('user_dashboard.layouts.app')
@section('content')
<section class="min-vh-100">
    <div class="my-30">
        <div class="container-fluid">
            <!-- Page title start -->
            <div>
                <h3 class="page-title">{{ __('Withdrawals') }}</h3>
            </div>
            <!-- Page title end-->

            <div class="row mt-4">
                <div class="col-lg-4">
                    <div class="mt-5">
                        <h3 class="sub-title">{{ __('Create Payout') }}</h3>
                        <p class="text-gray-500 text-16 text-justify">{{ __('Accumulated wallet funds can simply be withdrawn at any time, to your paypal ID or bank account. Setting up the withdrawal settings is must before proceding to make a withdraw.') }}</p>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-10">
                            <div class="d-flex w-100 mt-4">
                                <ol class="breadcrumb w-100">
                                    <li class="breadcrumb-first text-white">{{ __('Create') }}</li>
                                    <li>{{ __('Confirmation') }}</li>
                                    <li class="active">{{ __('Success') }}</li>
                                </ol>
                            </div>

                            <div class="bg-secondary rounded p-35 mt-5 shadow">
                                @include('user_dashboard.layouts.common.alert')
                                <form action="{{ url('payout') }}"  method="POST" accept-charset='UTF-8' id="payout_form">
                                    <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="payment_method_id" id="payment_method_id">
                                    <div>
                                        <div class="row">
                                            <!-- Payment Method -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>@lang('message.dashboard.payout.new-payout.payment-method')</label>
                                                    <select class="form-control" name="payout_setting_id" id="method">
                                                        @foreach ($payment_methods as $method)
                                                            @if($method->type == Paypal )
                                                                <option data-obj="{{json_encode($method->getAttributes())}}" value="{{ $method->id }}" data-type="{{ $method->type }}">
                                                                    {{$method->paymentMethod->name}} ({{ $method->email }})
                                                                </option>
                                                            @elseif($method->type == Bank)
                                                                <option data-obj="{{json_encode($method->getAttributes())}}" value="{{ $method->id }}" data-type="{{ $method->type }}">
                                                                    {{$method->paymentMethod->name}} ({{ $method->account_name }})
                                                                </option>
                                                            @elseif($method->type == Crypto)
                                                                <option data-obj="{{json_encode($method->getAttributes())}}" value="{{ $method->id }}" data-type="{{ $method->type }}">
                                                                    {{ $method->paymentMethod->name }} ({{ $method->currency->code . ' - ' . $method->crypto_address}})
                                                                </option>
                                                            @elseif (config('mobilemoney.is_active') && $method->type == (defined('MobileMoney') ? MobileMoney : ''))
                                                                <option data-obj="{{ json_encode($method->getAttributes()) }}" value="{{ $method->id }}" data-type="{{ $method->type }}">
                                                                    {{$method->paymentMethod->name}} ({{ $method->mobilemoney->mobilemoney_name ?? '' }} ****{{substr($method->mobile_number,-4)}})
                                                                </option>
                                                            @else
                                                                <option data-obj="{{json_encode($method->getAttributes())}}" value="{{ $method->id }}" data-type="{{ $method->type }}">
                                                                    {{$method->paymentMethod->name}} ({{ $method->account_number }})
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Currency -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>@lang('message.dashboard.payout.new-payout.currency')</label>

                                                    <select class="form-control" name="currency_id" id="currency_id">
                                                    </select>

                                                    <small id="walletHelp" class="form-text text-muted">
                                                        @lang('message.dashboard.deposit.fee') (<span class="pFees">0</span>%+<span class="fFees">0</span>)
                                                        @lang('message.dashboard.deposit.total-fee') <span class="total_fees">0.00</span>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Amount -->
                                        <div class="form-group">
                                            <label>@lang('message.dashboard.payout.new-payout.amount')</label>
                                            <input class="form-control" name="amount" id="amount" 
                                            onkeypress="return isNumberOrDecimalPointKey(this, event);" 
                                            placeholder="0.00" type="text" 
                                            oninput="restrictNumberToPrefdecimalOnInput(this)">
                                            <span class="amountLimit error" id="amountLimit"></span>
                                        </div>

                                        <div class="form-group" id="bank" style="display: none;">
                                            <label>@lang('message.dashboard.payout.new-payout.bank-info')</label>
                                            <span id="bank_info_input"></span>
                                        </div>

                                        <div class="mt-1">
                                            <a href="{{ url('/payouts') }}" class="btn btn-danger px-4 py-2" style="color: white !important;">Cancel</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                            <button type="submit" class="btn btn-primary px-4 py-2" id="withdrawal-create">
                                                <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="withdrawal-create-text" style="font-weight: bolder;">@lang('message.dashboard.button.next')</span>
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

<script src="{{ theme_asset('public/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ theme_asset('public/js/additional-methods.min.js') }}" type="text/javascript"></script>
<script src="{{ theme_asset('public/js/sweetalert/sweetalert-unpkg.min.js') }}" type="text/javascript"></script>
<script src="{{ theme_asset('public/js/jquery.ba-throttle-debounce.js') }}" type="text/javascript"></script>

@include('common.restrict_number_to_pref_decimal')
@include('common.restrict_character_decimal_point')

<script>
    var isActiveMobileMoney = "{!! config('mobilemoney.is_active') !!}";
    $(window).on('load',function()
    {
        var previousUrl = localStorage.getItem("payoutConfirmPreviousUrl");
        var confirmationUrl = SITE_URL + '/payout';
        if (confirmationUrl == previousUrl)
        {
            var payoutPaymentMethodId = localStorage.getItem('payoutPaymentMethodId');
            var currency_id = localStorage.getItem('currency_id');
            var pFees = localStorage.getItem('pFees');
            var fFees = localStorage.getItem('fFees');
            var total_fees_html = localStorage.getItem('total_fees_html');

            if (payoutPaymentMethodId && currency_id && total_fees_html && pFees && fFees)
            {
                swal('{{ __("Please Wait") }}'.replace( /&#039;/g, "'"), "{{ __('Loading...') }}", {
                    closeOnClickOutside: false,
                    closeOnEsc: false,
                    buttons: false,
                });
                setTimeout(function(payoutPaymentMethodId, currency_id, total_fees_html, pFees, fFees)
                {
                    $('#payment_method').val(payoutPaymentMethodId);
                    $('#currency_id').val(currency_id);
                    $(".total_fees").html(total_fees_html);
                    $(".total_fees").html(total_fees_html);
                    $('.pFees').html(pFees);
                    $('.fFees').html(fFees);
                    removePayoutLocalStorageValues();
                    swal.close();
                }, 1300, payoutPaymentMethodId, currency_id, total_fees_html, pFees, fFees);
            }
        }
        else
        {
            setTimeout(function()
            {
                removePayoutLocalStorageValues();
            }, 1300);
        }

        var paymentMethodId = JSON.parse($('option:selected','#method').attr('data-type'));

        getFeesLimitsPaymentMethodsCurrencies(paymentMethodId)
        .then((data) => {
            determineDecimalPoint();
        }).then((data) => {
            withdrawalAmountLimitCheck(paymentMethodId);
        })
        .catch((error) => {
            console.log(error)
        });

        var paymentMethodObject = JSON.parse($('option:selected','#method').attr('data-obj'));
        if(paymentMethodObject.email != null) {
            var p = '<input value="' + paymentMethodObject.email + '" type="text" name="payment_method_info" class="form-control" id="payment_method_info">';
        }
        else if(paymentMethodObject.account_name != null) {
            var p = '<input value="' + paymentMethodObject.account_name + '" type="text" name="payment_method_info" class="form-control" id="payment_method_info">';
        }
        else if(paymentMethodObject.account_number != null) {
            var p = '<input value="' + paymentMethodObject.account_number + '" type="text" name="payment_method_info" class="form-control" id="payment_method_info">';
        }
        else if(paymentMethodObject.crypto_address != null) {
            var p = '<input value="' + paymentMethodObject.crypto_address + '" type="text" name="payment_method_info" class="form-control" id="payment_method_info">';
        } else if (isActiveMobileMoney && paymentMethodObject.mobilemoney_id != null && paymentMethodObject.mobile_number != null) {
            var p = '<input value="' + paymentMethodObject.mobile_number + '" type="text" name="payment_method_info" class="form-control" id="payment_method_info">';
        }
        $('#bank_info_input').html(p);
        //bug fix finished
    });

    var lastPaymentMethod, currentPaymentMethod;

    $("select[name=payout_setting_id]").focus(function () {
        lastPaymentMethod = $('select#method').find(':selected').data('type');
    }).change(function() {
        currentPaymentMethod = $(this).find(':selected').data('type');
    });

    $(document).ready(function()
    {
        $("#method").on('change', function ()
        {
            // Payment method (crypto to fiat or fiat to crypto)
            if (lastPaymentMethod != currentPaymentMethod) {
                $('#amount').val('');
                $('#amountLimit').text('');
            }
            lastPaymentMethod = currentPaymentMethod;

            $("#bank").css("display", "none");

            var paymentMethodObject = JSON.parse($('option:selected','#method').attr('data-obj'));

            if(paymentMethodObject.email != null)
            {
                var p = '<input value="' + paymentMethodObject.email + '" type="text" name="payment_method_info" class="form-control" id="payment_method_info">';
            }
            else if(paymentMethodObject.account_name != null)
            {
                var p = '<input value="' + paymentMethodObject.account_name + '" type="text" name="payment_method_info" class="form-control" id="payment_method_info">';
            }
            else if(paymentMethodObject.account_number != null)
            {
                var p = '<input value="' + paymentMethodObject.account_number + '" type="text" name="payment_method_info" class="form-control" id="payment_method_info">';
            }
            else if(paymentMethodObject.crypto_address != null)
            {
                var p = '<input value="' + paymentMethodObject.crypto_address + '" type="text" name="payment_method_info" class="form-control" id="payment_method_info">';
            }
            $('#bank_info_input').html(p);

            var paymentMethodId = JSON.parse($('option:selected','#method').attr('data-type'));
            getFeesLimitsPaymentMethodsCurrencies(paymentMethodId).then((data) => {
                determineDecimalPoint();
            }).then((data) => {
                withdrawalAmountLimitCheck(paymentMethodId);
            })
            .catch((error) => {
                console.log(error)
            });
        });

        $('#currency_id, #amount').on('change keyup', $.debounce(1000, function (e)
        {
            var paymentMethodId = JSON.parse($('option:selected','#method').attr('data-type'));
            withdrawalAmountLimitCheck(paymentMethodId);
        }));
    });

    function restrictNumberToPrefdecimalOnInput(e)
    {
        var type = $('select#currency_id').find(':selected').data('type')
        restrictNumberToPrefdecimal(e, type);
    }

    function determineDecimalPoint() {
        
        var currencyType = $('select#currency_id').find(':selected').data('type');

        if (currencyType == 'crypto') {
            $('.pFees, .fFees, .total_fees').text(CRYPTODP);
            $("#amount").attr('placeholder', CRYPTODP);

        } else if (currencyType == 'fiat') {
            
            $('.pFees, .fFees, .total_fees').text(FIATDP);
            $("#amount").attr('placeholder', FIATDP);
        }
    }

    function getFeesLimitsPaymentMethodsCurrencies(paymentMethodId)
    {
        $('#payment_method_id').val(paymentMethodId);
        var token = $('#_token').val();
        var paymentMethodObject = JSON.parse($('option:selected','#method').attr('data-obj'));

        var cryptoCurrencyId = paymentMethodObject != null ? paymentMethodObject.currency_id : null;

        return new Promise((resolve, reject) => {
            $.ajax({
                method: 'post',
                url: SITE_URL + "/withdrawal/fees-limit-payment-method-isActive-currencies",
                data: {
                    "_token": token,
                    'transaction_type_id': '{{Withdrawal}}',
                    'payment_method_id': paymentMethodId,
                    'currencyId': cryptoCurrencyId
                },
                dataType: "json",
                success: function (response)
                {
                    if (response.success.status == 'success') {
                        let options = '';
                        $.map(response.success.currencies, function(value, index)
                        {
                            options += `<option data-type="${value.type}" value="${value.id}" ${value.default_wallet == 'Yes' ? 'selected="selected"': ''}>${value.code}</option>`;
                        });
                        $('#currency_id').html(options);
                        resolve(response.success.status);
                    }
                },
                error: function (error) {
                    reject(error)
                },
            });
        })
    }

    function withdrawalAmountLimitCheck(paymentMethodId)
    {
        $('#payment_method_id').val(paymentMethodId);
        var amount = $('#amount').val().trim();

        var currency_id = $('#currency_id').val();
        if (currency_id == '')
        {
            $('#walletHelp').hide();
        }
        else
        {
            $('#walletHelp').show();
        }

        if (currency_id && amount != '')
        {
            var token = $('#_token').val();

            $.ajax({
                method: 'post',
                url: SITE_URL + "/withdrawal/amount-limit",
                data: {
                    "_token": token,
                    'payment_method_id': paymentMethodId,
                    'currency_id': currency_id,
                    'transaction_type_id': '{{Withdrawal}}',
                    'amount': amount,
                },
                dataType: "json",
                success: function (res)
                {
                    if (res.success.status == 200)
                    {
                        $('.total_fees').html(res.success.totalHtml);
                        $('.pFees').html(res.success.pFeesHtml);
                        $('.fFees').html(res.success.fFeesHtml);

                        //checking balance
                        if(res.success.totalAmount > res.success.balance){
                            $('#amountLimit').html("{{ __("Not have enough balance !") }}");
                            $('#withdrawal-create').attr('disabled', true);
                        }else {
                            $('#amountLimit').html('');
                            $('#withdrawal-create').removeAttr('disabled');
                        }
                    }
                    else
                    {
                        if (amount == '')
                        {
                            $('#amountLimit').text('');
                        }
                        else
                        {
                            $('#amountLimit').text(res.success.message);
                        }

                        $('#withdrawal-create').attr('disabled', true);
                        return false;
                    }
                }
            });
        }
    }

    function removePayoutLocalStorageValues()
    {
        localStorage.removeItem('payoutConfirmPreviousUrl');
        localStorage.removeItem('payoutPaymentMethodId');
        localStorage.removeItem('currency_id');
        localStorage.removeItem('pFees');
        localStorage.removeItem('fFees');
        localStorage.removeItem('total_fees_html');
    }

    jQuery.extend(jQuery.validator.messages, {
      required: "{{ __('This field is required.') }}",
    })


    $('#payout_form').validate({
        rules: {
            amount: {
                required: true
            },
            currency_id: {
                required: true
            },
            payout_setting_id:{
                required:true
            }
        },
        submitHandler: function (form)
        {

            //set values to localStorage
            var payoutPaymentMethodId = JSON.parse($('option:selected','#method').attr('data-type'));
            localStorage.setItem("payoutPaymentMethodId", payoutPaymentMethodId);

            var currency_id = $('#currency_id').val();
            localStorage.setItem("currency_id", currency_id);

            var pFees = $('.pFees').html();
            localStorage.setItem("pFees", pFees);

            var fFees = $('.fFees').html();
            localStorage.setItem("fFees", fFees);

            var total_fees_html = $(".total_fees").html();
            localStorage.setItem("total_fees_html", total_fees_html);
            //

            $("#withdrawal-create").attr("disabled", true);
            $(".spinner").show();
            var pretext=$("#withdrawal-create-text").text();
            $("#withdrawal-create-text").text("{{ __('Payout...') }}");
            form.submit();
            setTimeout(function(){
                $("#withdrawal-create").removeAttr("disabled");
                $(".spinner").hide();
                $("#withdrawal-create-text").text(pretext);
            },1000);
        }
    });
</script>
@endsection
