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
                        <p class="text-gray-500 text-16"> {{ __('Enter your deposit amount, currency and payment method') }}</p>
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


                            <div class="bg-secondary mt-5 shadow p-35">
                                @include('user_dashboard.layouts.common.alert')
								<div>
                                    <div class="mb-4">
                                        <p class="sub-title">{{ __('Card Information') }}</p>
                                    </div>
									<form id="payment-form" method="POST">
                                        {{ csrf_field() }}
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="text-center" for="usr">@lang('message.dashboard.deposit.deposit-stripe-form.card-no')</label>
                                                    <div id="card-number"></div>
                                                    <input type="text" class="form-control" name="cardNumber" maxlength="19" id="cardNumber" onkeypress="return isNumber(event)">
                                                    <div id="card-errors" class="error"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group mb-0">
                                                    <div class="row">
                                                        <div class="col-lg-4 pr-4">
                                                            <label for="usr">{{ __('Month') }}</label>
                                                            <div>
                                                                <select class="form-control" name="month" id="month">
                                                                    <option value="01">01</option>
                                                                    <option value="02">02</option>
                                                                    <option value="03">03</option>
                                                                    <option value="04">04</option>
                                                                    <option value="05">05</option>
                                                                    <option value="06">06</option>
                                                                    <option value="07">07</option>
                                                                    <option value="08">08</option>
                                                                    <option value="09">09</option>
                                                                    <option value="10">10</option>
                                                                    <option value="11">11</option>
                                                                    <option value="12">12</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-4 mt-4 mt-lg-0 pr-4">
                                                            <label for="usr">{{ __('Year') }}</label>
                                                            <input type="text" class="form-control" name="year" id="year" maxlength="2" onkeypress="return isNumber(event)">
                                                        </div>

                                                        <div class="col-lg-4 mt-4 mt-lg-0">
                                                            <div class="form-group">
                                                                <label for="usr">{{ __('cvc') }}</label>
                                                                <input type="text" class="form-control" name="cvc" id="cvc" maxlength="4" onkeypress="return isNumber(event)">
                                                                <div id="card-cvc"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-12">
                                                <p class="error" id="stripeError"></p>
                                            </div>
                                        </div>

                                        <div class="row m-0 justify-content-between mt-2">
                                            <div>
                                                <a href="#" class="deposit-confirm-back-btn">
                                                    <p class="py-2 text-active text-underline deposit-confirm-back-btn mt-2"><u><i class="fas fa-long-arrow-alt-left"></i> @lang('message.dashboard.button.back')</u></p>
                                                </a>
                                            </div>

                                            <div>
                                                <button type="submit" class="btn btn-primary px-4 py-2 float-left" style="margin-top:10px;" id="deposit-stripe-submit-btn">
                                                    <i class="spinner fa fa-spinner fa-spin d-none"></i> <span id="deposit-stripe-submit-btn-txt" style="font-weight: bolder;">@lang('message.form.submit')</span>
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
    </div>
</section>


@include('user_dashboard.layouts.common.help')
@endsection
@section('js')
<script src="{{ theme_asset('public/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ theme_asset('public/js/jquery.ba-throttle-debounce.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        var pretext = $("#deposit-stripe-submit-btn-txt").text();
        var paymentIntendId = null;
        var paymentMethodId = null;
        function isNumber(evt) 
        {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }
            return true;
        }
        function depositBack()
        {
            window.localStorage.setItem("depositConfirmPreviousUrl",document.URL);
            window.history.back();
        }

        //Only go back by back button, if submit button is not clicked
        $(document).on('click', '.deposit-confirm-back-btn', function (e)
        {
            e.preventDefault();
            depositBack();
        });

        $('#payment-form').validate(
        {
            rules:
            {
                cardNumber:
                {
                    required: true,
                },
                month:
                {
                    required: true,
                    maxlength: 2
                },
                year:
                {
                    required: true,
                    maxlength: 2
                },
                cvc:
                {
                    required: true,
                    maxlength: 4
                },
            },
            submitHandler: function(form)
            {
                confirmPayment();
            }
        });
        function makePayment()
        {
            var promiseObj = new Promise(function(resolve, reject)
            {
                var cardNumber = $("#cardNumber").val().trim();
                var month      = $("#month").val().trim();
                var year       = $("#year").val().trim();
                var cvc        = $("#cvc").val().trim();
                $("#stripeError").html('');
                if (cardNumber && month && year && cvc) {
                    $.ajax({
                        type: "POST",
                        url: SITE_URL + "/deposit/stripe-make-payment",
                        data:
                        {
                            "_token":  '{{ csrf_token() }}',
                            'cardNumber': cardNumber,
                            'month': month,
                            'year': year,
                            'cvc': cvc
                        },
                        dataType: "json",
                        beforeSend: function (xhr) {
                            $("#deposit-stripe-submit-btn").attr("disabled", true);
                        },
                    }).done(function(response)
                    {   
                        if (response.data.status != 200) {
                            $("#stripeError").html(response.data.message);
                            $("#deposit-stripe-submit-btn").attr("disabled", true);
                            reject(response.data.status);
                            return false;    
                        } else {
                            // paymentIntendId = response.data.paymentIntendId;
                            // paymentMethodId = response.data.paymentMethodId;
                            resolve(response.data);
                            $("#deposit-stripe-submit-btn").attr("disabled", false);
                        }
                    });
                }
            });
            return promiseObj;
        }
        function confirmPayment()
        {
            makePayment().then(function(result) {
                $.ajax({
                    type: "POST",
                    url: SITE_URL + "/deposit/stripe-confirm-payment",
                    data:
                    {
                        "_token":  '{{ csrf_token() }}',
                        'paymentIntendId': result.paymentIntendId,
                        'paymentMethodId': result.paymentMethodId,
                    },
                    dataType: "json",
                    beforeSend: function (xhr) {
                        $("#deposit-stripe-submit-btn").attr("disabled", true);
                        $(".fa-spin").removeClass("d-none");
                        $("#deposit-stripe-submit-btn-txt").text("{{__('Submitting...')}}");
                    },
                }).done(function(response)
                {
                    $("#deposit-stripe-submit-btn-txt").text(pretext);
                    $(".fa-spin").hide();
                    if (response.data.status != 200) {
                        $("#deposit-stripe-submit-btn").attr("disabled", true);
                        $("#stripeError").html(response.data.message);
                        return false;
                    } else {
                        $("#deposit-stripe-submit-btn").attr("disabled", false);
                    }
                    window.location.replace(SITE_URL + '/deposit/stripe-payment/success');
                });
            });
        }
        $("#month").change(function() {
            $("#deposit-stripe-submit-btn").attr("disabled", true);
            makePayment();
        });
        $("#year, #cvc").on('keyup', $.debounce(500, function() {
            $("#deposit-stripe-submit-btn").attr("disabled", true);
            makePayment();
        }));
        $("#cardNumber").on('keyup', $.debounce(1000, function() {
            $("#deposit-stripe-submit-btn").attr("disabled", true);
            makePayment();
        }));
        // For card number design
        document.getElementById('cardNumber').addEventListener('input', function (e) {
            var target = e.target, position = target.selectionEnd, length = target.value.length;
            target.value = target.value.replace(/[^\d]/g, '').replace(/(.{4})/g, '$1 ').trim();
            target.selectionEnd = position += ((target.value.charAt(position - 1) === ' ' && target.value.charAt(length - 1) === ' ' && length !== target.value.length) ? 1 : 0);
        });

        $(document).ready(function() {
            $("#deposit-stripe-submit-btn").attr("disabled", true);

            window.history.pushState(null, "", window.location.href);
            window.onpopstate = function() {
                window.history.pushState(null, "", window.location.href);
            };
        });
    </script>
@endsection
