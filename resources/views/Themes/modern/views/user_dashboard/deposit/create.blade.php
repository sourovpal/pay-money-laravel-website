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
                        <h3 class="sub-title">{{ __('Create Deposit') }}</h3>
                        <p class="text-gray-500 text-16 text-justify">{{ __('You can deposit to your wallets using our popular payment methods. Fill the details correctly & the amount you want to deposit.') }}</p>
                    </div>
                    <!-- Sub title end-->
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

                            <div class="bg-secondary mt-5 shadow p-35">
                                @include('user_dashboard.layouts.common.alert')
                                <form id="depositForm1" action="{{ url('deposit') }}" method="post" accept-charset='UTF-8'>
                                    <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
                                    <input type="hidden" name="percentage_fee" id="percentage_fee" class="form-control"
                                            value="">
                                    <input type="hidden" name="fixed_fee" id="fixed_fee" class="form-control" value="">
                                    <input type="hidden" name="fee" class="fee" value="0.00">
                                    <div class="row">
                                        {{-- Currency --}}
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">@lang('message.dashboard.deposit.currency')</label>
                                                <select class="form-control wallet" name="currency_id" id="currencies">
                                                    @foreach ($activeCurrencyList as $aCurrency)
                                                        <option data-type="{{ $aCurrency['type'] }}" value="{{ $aCurrency['id'] }}"{{ $defaultWallet->currency_id == $aCurrency['id'] ? 'selected="selected"' : '' }}>{{ $aCurrency['code'] }}</option>
                                                    @endforeach
                                                </select>
                                                <small id="walletlHelp" class="form-text text-muted">
                                                    @lang('message.dashboard.deposit.fee')(<span class="pFees">0</span>%+<span class="fFees">0</span>)
                                                    @lang('message.dashboard.deposit.total-fee') <span class="total_fees">0.00</span>
                                                </small>
                                            </div>
                                        </div>
                                        {{-- Amount --}}
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">@lang('message.dashboard.deposit.amount')</label>
                                                <input type="text" class="form-control amount" name="amount"
                                                        placeholder="0.00" type="text" id="amount"
                                                        onkeypress="return isNumberOrDecimalPointKey(this, event);"
                                                        value="{{isset($transInfo['amount'])?$transInfo['amount']:''}}" oninput="restrictNumberToPrefdecimalOnInput(this)">
                                                <span class="amountLimit error"></span>
                                            </div>
                                        </div>
                                        {{-- Payment Methods --}}
                                        <div class="col-md-12" id="paymentMethodEmpty" style="display: none;">
                                            <div class="form-group">
                                                <label>@lang('message.dashboard.deposit.fees-limit-payment-method-settings-inactive')</label>
                                            </div>
                                        </div>

                                        <div class="col-md-12" id="paymentMethodSection">
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">@lang('message.dashboard.deposit.payment-method')</label>
                                                <select class="form-control payment_method" name="payment_method" id="payment_method">
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-1">
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-primary px-4 py-2 transfer_form" id="deposit-money">
                                                <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="deposit-money-text" style="font-weight: bolder;">@lang('message.dashboard.button.next')</span>
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
@include('user_dashboard.layouts.common.help')
@endsection

@section('js')

<script src="{{ theme_asset('public/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ theme_asset('public/js/additional-methods.min.js') }}" type="text/javascript"></script>
<script src="{{ theme_asset('public/js/sweetalert/sweetalert-unpkg.min.js') }}" type="text/javascript"></script>
<script src="{{ theme_asset('public/js/jquery.ba-throttle-debounce.js') }}" type="text/javascript"></script>

@include('common.restrict_number_to_pref_decimal')
@include('common.restrict_character_decimal_point')

<script>

    $(window).on('load',function()
    {
        var previousUrl = localStorage.getItem("depositConfirmPreviousUrl");
        var confirmationUrl = SITE_URL + '/deposit';
        if (confirmationUrl == previousUrl)
        {
            var getDepositPaymentMethodId = localStorage.getItem('depositPaymentMethodId');
            var feesPercentage = localStorage.getItem('percentage_fee');
            var fixed_fee = localStorage.getItem('fixed_fee');
            var total_fees = localStorage.getItem('total_fees');
            var total_fees_html = localStorage.getItem('total_fees_html');
            var pFees = localStorage.getItem('pFees');
            var fFees = localStorage.getItem('fFees');

            if (getDepositPaymentMethodId && feesPercentage && fixed_fee && total_fees && total_fees_html && pFees && fFees)
            {
                swal('{{ __("Please Wait") }}'.replace( /&#039;/g, "'"), "{{ __('Loading...') }}", {
                    closeOnClickOutside: false,
                    closeOnEsc: false,
                    buttons: false,
                });
                setTimeout(function(getDepositPaymentMethodId, feesPercentage, fixed_fee, total_fees, total_fees_html, pFees, fFees)
                {
                    $('#payment_method').val(getDepositPaymentMethodId);

                    $("#percentage_fee").val(feesPercentage);

                    $("#fixed_fee").val(fixed_fee);

                    $(".fee").val(total_fees);
                    $(".total_fees").html(total_fees_html);

                    $('.pFees').html(pFees);

                    $('.fFees').html(fFees);

                    swal.close();
                }, 1300, getDepositPaymentMethodId, feesPercentage, fixed_fee, total_fees, total_fees_html, pFees, fFees);
                removeDepositLocalStorageValues();
            }
        }
        else
        {
            determineDecimalPoint();
            setTimeout(function()
            {
                removeDepositLocalStorageValues();
            }, 1300);
        }

        //Fees Limit check on load
        var currency_id = $('#currencies').val();
        getMatchedFeesLimitsCurrencyPaymentMethodsSettingsPaymentMethods(currency_id);
    });

    function restrictNumberToPrefdecimalOnInput(e)
    {
        var type = $('select#currencies').find(':selected').data('type')
        restrictNumberToPrefdecimal(e, type);
    }

    function determineDecimalPoint() {
        
        var currencyType = $('select#currencies').find(':selected').data('type')

        if (currencyType == 'crypto') {
            $('.pFees, .fFees, .total_fees').text(CRYPTODP);
            $("#amount").attr('placeholder', CRYPTODP);

        } else if (currencyType == 'fiat') {
            
            $('.pFees, .fFees, .total_fees').text(FIATDP);
            $("#amount").attr('placeholder', FIATDP);
        }
    }

    var currenctCurrencyType, lastCurrencyType;
    $(document).on('click', 'select', function()
    {
        lastCurrencyType = $(this).find(':selected').data('type');

    }).on("change","select",function(){
        currenctCurrencyType = $(this).find(':selected').data('type');
    });

    //Fees Limit check on currencies change
    $(document).on('change', '#currencies', function()
    {
        if (lastCurrencyType !== currenctCurrencyType) {
            $('#amount').val('');
        }
        $('.amountLimit').text('');

        determineDecimalPoint();
        
        var currency_id = $('#currencies').val();
        getMatchedFeesLimitsCurrencyPaymentMethodsSettingsPaymentMethods(currency_id);
    });


    //Fees Limit check on payment method change
    $(document).on('change', '#payment_method', function()
    {
        getDepositFeesLimit();
    });


    //Fees Limit check on amount input
    $(document).on('input', '.amount', $.debounce(1000, function()
    {
        getDepositFeesLimit();
    }));

    function getMatchedFeesLimitsCurrencyPaymentMethodsSettingsPaymentMethods(currency_id)
    {
        var token = $('#_token').val();
        $.ajax(
        {
            method: 'post',
            url: SITE_URL + "/deposit/fees-limit-currency-payment-methods-is-active-payment-methods-list",
            data: {
                "_token": token,
                'transaction_type_id': '{{ Deposit }}',
                'currency_id': currency_id,
            },
            dataType: "json",
        }).done(function(response)
        {
            let options = '';
            $.map(response.success.paymentMethods, function(value, index)
            {
                options += `<option value="${value.id}">${value.name}</option>`;
            });

            if (response.success.paymentMethods != '')
            {
                $('#payment_method').html(options);
                $('#walletlHelp').show();
                $('#paymentMethodSection').show();
                $('#paymentMethodEmpty').hide();
                $('#deposit-money').show();
                getDepositFeesLimit();
            }
            else
            {
                $('#payment_method').val('');
                $("#percentage_fee").val(0.00000000);
                $("#fixed_fee").val(0.00000000);
                $(".fee").val(0.00000000);
                $('.pFees').html('0');
                $('.fFees').html('0');
                $(".total_fees").html('0.00');
                $('#paymentMethodSection').hide();
                $('#paymentMethodEmpty').show();
                $('#deposit-money').hide();
            }
        });
    }

    function getDepositFeesLimit()
    {
        var token = $("#token").val();
        var amount = $('#amount').val().trim();
        var currency_id = $('#currencies').val();
        var payment_method_id = $('#payment_method option:selected').val();

        if (amount != '')
        {
            $.ajax(
            {
                method: "POST",
                url: SITE_URL + "/deposit/getDepositFeesLimit",
                dataType: "json",
                data:
                {
                    "_token": token,
                    'amount': amount,
                    'currency_id': currency_id,
                    'payment_method_id': payment_method_id,
                    'transaction_type_id': '{{Deposit}}'
                }
            }).done(function(response)
            {
                if (response.success.status == 200)
                {
                    $("#percentage_fee").val(response.success.feesPercentage);
                    $("#fixed_fee").val(response.success.feesFixed);
                    $(".fee").val(response.success.totalFees);

                    $(".total_fees").html(response.success.totalFeesHtml);
                    $('.pFees').html(response.success.pFeesHtml); //2.3
                    $('.fFees').html(response.success.fFeesHtml);//2.3

                    $('.amountLimit').text('');
                    $('#deposit-money').attr('disabled', false);
                    // return true;
                }
                else
                {
                    if (amount == '')
                    {
                        $('.amountLimit').text('');
                        $('#deposit-money').attr('disabled', false);
                    }
                    else
                    {
                        $('.amountLimit').text(response.success.message);
                        $('#deposit-money').attr('disabled', true);
                        return false;
                    }
                }
            });
        }
    }

    function removeDepositLocalStorageValues()
    {
        localStorage.removeItem('depositConfirmPreviousUrl');
        localStorage.removeItem('depositPaymentMethodId');
        localStorage.removeItem('percentage_fee');
        localStorage.removeItem('fixed_fee');
        localStorage.removeItem('total_fees');
        localStorage.removeItem('total_fees_html');
        localStorage.removeItem('pFees');
        localStorage.removeItem('fFees');
    }

    jQuery.extend(jQuery.validator.messages,
    {
        required: "{{ __('This field is required.') }}",
    })


    $('#depositForm1').validate(
    {
        rules:
        {
            payment_method:
            {
                required: true,
            },
            amount:
            {
                required: true,
            },
            wallet:
            {
                required: true,
            },
        },
        submitHandler: function(form)
        {
            //set values to localStorage
            var depositPaymentMethodId = $('#payment_method').val();
            localStorage.setItem("depositPaymentMethodId", depositPaymentMethodId);

            var percentage_fee =  $("#percentage_fee").val();
            localStorage.setItem("percentage_fee", percentage_fee);

            var fixed_fee =  $("#fixed_fee").val();
            localStorage.setItem("fixed_fee", fixed_fee);

            var total_fees = $(".fee").val();
            localStorage.setItem("total_fees", total_fees);

            var total_fees_html = $(".total_fees").html();
            localStorage.setItem("total_fees_html", total_fees_html);

            var pFees = $('.pFees').html();
            localStorage.setItem("pFees", pFees);

            var fFees = $('.fFees').html();
            localStorage.setItem("fFees", fFees);
            //


            $("#deposit-money").attr("disabled", true);
            $(".spinner").show();
            var pretext = $("#deposit-money-text").text();
            $("#deposit-money-text").text("{{ __('Depositing...') }}");
            form.submit();
            setTimeout(function()
            {
                $("#deposit-money-text").text(pretext);
                $("#deposit-money").removeAttr("disabled");
                $(".spinner").hide();
            }, 1000);
        }
    });

</script>

@endsection
