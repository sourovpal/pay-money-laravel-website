<!DOCTYPE html>
<html lang="en">
    <head>
      <title>@lang('message.express-payment-form.merchant-payment')</title>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Ensures optimal rendering on mobile devices. -->
      <meta http-equiv="X-UA-Compatible" content="IE=edge" /> <!-- Optimal Internet Explorer compatibility -->
      <!-- Bootstrap 5.0.2 -->
      <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/bootstrap/dist/css/bootstrap-css/bootstrap.min.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ asset('public/frontend/css/merchant_payment/merchant_payment.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/font-awesome/css/font-awesome.min.css')}}">
      <!-- jQuery 3 -->
      <script src="{{ asset('public/backend/jquery/dist/jquery.min.js') }}" type="text/javascript"></script>
      <script src="{{ asset('public/backend/bootstrap/dist/js/bootstrap-js/popper.min.js') }}" type="text/javascript"></script>
      <script src="{{ asset('public/backend/bootstrap/dist/js/bootstrap-js/bootstrap.min.js') }}" type="text/javascript"></script>
      <script type="text/javascript">
        var SITE_URL = "{{url('/')}}";
      </script>
    </head>

    <body>

      <div class="container mt-3">
        <div class="row">
            <div class="col-md-2 offset-md-8">
                <h4>@lang('message.footer.language')</h4>
                <div class="form-group">
                    <select class="form-control form-select" id="lang">
                        @foreach (getLanguagesListAtFooterFrontEnd() as $lang)
                        <option {{ Session::get('dflt_lang') == $lang->short_name ? 'selected' : '' }}
                        value='{{ $lang->short_name }}'> {{ $lang->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
          </div>
      </div>

      <div class="container text-center">
        <div class="row">
          <div class="col-md-8 offset-md-2">
            <div class="panel panel-default box-shadow mt-3">
              <div class="panel-body">
                <div class="row">
                  <div class="col-md-12">
                    @if($isMerchantAvailable)
                    <h2>
                     <?php
                     $amount = isset($paymentInfo['amount']) ? $paymentInfo['amount'] : 0;
                     ?>
                     {{ moneyFormat(optional($merchant->currency)->code, formatNumber($amount)) }}
                   </h1>
                   <h6>{{$paymentInfo['item_name'] ? $paymentInfo['item_name']:""}}</h6>
                   @endif
                 </div>
                  <div class="col-md-12">
                    <div class="bs-callout bs-callout-danger">
                      @if(!$isMerchantAvailable)
                        <h4 class="text-danger">@lang('message.express-payment-form.merchant-not-found')</h4>
                      @else
                        <p>@lang('message.express-payment-form.merchant-found')</p>
                      @endif
                    </div>

                    @if($isMerchantAvailable)
                      <div class="row">
                        <div class="col-md-12">
                          <!-- Tab panes -->
                          <div class="tab-content">
                            <div class="tab-pane show active" id="home">
                              <form class="d-inline-block" id="check" action="">
                                <div class="plan-card-group">
                                  <div class="row">

                                    @if(!empty($payment_methods))
                                      @foreach($payment_methods as $value)
                                        @php
                                        $name = strtolower($value['name']).'.jpg';
                                        @endphp

                                        @if(!in_array($value['id'], [Bank, Crypto]) && in_array($value['id'],$cpm))
                                          <div class="col-md-3 col-xs-4">
                                            <div class="radio-card">
                                              <input class="planes-radio" name="method" value="{{$value['name']}}" id="{{$value['id']}}" type="radio">
                                              <label for="{{$value['id']}}" id="{{$value['id']}}">
                                                <span class="card-title">
                                                  @if($value['id'] == Mts)
                                                    <div class="setting-img">
                                                      <div class="img-wrap-general-logo">
                                                        {!! getSystemLogo('img-fluid p-2') !!}
                                                      </div>
                                                    </div>
                                                  @else
                                                    <img class="img-fluid" src='{{asset("public/images/payment_gateway/$name")}}' alt="">
                                                  @endif
                                                </span>
                                              </label>
                                            </div>
                                          </div>
                                        @endif
                                      @endforeach
                                    @endif

                                    <div class="col-md-12">
                                      <div class="pull-right">
                                        <a href="#payment" data-bs-toggle="tab" class="btn btn-primary tab-name">
                                          @lang('message.express-payment-form.continue')
                                        </a>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </form>
                            </div>

                            <div class="tab-pane" id="payment">
                              <!--- MTS GATEWAY START-->
                              <form class="dis-none" action="{{url('payment/mts_pay')}}" id="Mts" name="Mts" method="POST" accept-charset="utf-8">
                               {{csrf_field()}}
                                <div class="row">
                                  <div class="col-md-12">
                                    <div class="form-group">
                                      <label for="exampleInputEmail1">@lang('message.express-payment-form.email')</label>
                                      <input class="form-control" name="email" id="email" placeholder="Email" type="text" required>
                                    </div>
                                    <div class="form-group">
                                      <label for="exampleInputEmail1">@lang('message.express-payment-form.password')</label>
                                      <input name="password" class="form-control" id="password" placeholder="********" type="password" required>
                                    </div>
                                  </div>
                                  <div class="col-md-12">
                                    <input name="merchant" value="{{isset($paymentInfo['merchant_id']) ? $paymentInfo['merchant_id'] : ''}}" type="hidden">
                                    <input name="merchant_uuid" value="{{isset($paymentInfo['merchant']) ? $paymentInfo['merchant'] : ''}}" type="hidden">
                                    <input name="amount" value="{{ $amount }}" type="hidden">
                                    <input name="currency" value="{{$merchant->currency->code}}" type="hidden">
                                    <input name="order_no" value="{{isset($paymentInfo['order']) ? $paymentInfo['order'] : ''}}" type="hidden">
                                    <input name="item_name" value="{{isset($paymentInfo['item_name']) ? $paymentInfo['item_name'] : ''}}" type="hidden">
                                    <div class="pull-right mt-3">
                                      <a href="#home" data-bs-toggle="tab" class="standard-payment-cancel-link text-decoration-none">
                                          <button class="btn btn-default standard-payment-cancel-btn border f-14 fw-bold me-2">@lang('message.express-payment-form.cancel')</button>
                                      </a>
                                      <button type="submit" class="btn btn-primary standard-payment-submit-btn f-14">
                                          <i class="spinner fa fa-spinner fa-spin d-none"></i>
                                          <span class="standard-payment-submit-btn-txt f-14 fw-bold">
                                            @lang('message.express-payment-form.go-to-payment')
                                          </span>
                                      </button>
                                    </div>
                                  </div>
                                </div>
                              </form>
                              <!--- MTS GATEWAY END-->

                              <!--- PAYPAL GATEWAY START-->
                              <form class="dis-none" id="Paypal" name="Paypal" method="post" action="{{url('payment/paypal')}}" accept-charset="UTF-8">
                                {{csrf_field()}}
                                <input name="order_no" value="{{isset($paymentInfo['order']) ? $paymentInfo['order'] : ''}}" type="hidden">
                                <input name="item_name" value="{{isset($paymentInfo['item_name']) ? $paymentInfo['item_name'] : ''}}" type="hidden">
                                <input name="merchant" value="{{isset($paymentInfo['merchant_id']) ? $paymentInfo['merchant_id'] : ''}}" type="hidden">
                                <input name="merchant_uuid" value="{{isset($paymentInfo['merchant']) ? $paymentInfo['merchant'] : ''}}" type="hidden">
                                <input name="no_shipping" value="1" type="hidden">
                                <input name="currency" value="{{$merchant->currency->code}}" type="hidden">
                                <input class="form-control" name="amount" value="{{ $amount }}" type="hidden">
                                <div id="paypal-button-container"></div>
                                <div class="row">
                                  <div class="col-md-12">
                                    <div class="bs-callout-warning">
                                      <p>@lang('message.express-payment-form.payment-agreement')</p>
                                    </div>
                                    <div class="pull-left">
                                      <a href="#home" data-bs-toggle="tab" class="standard-payment-cancel-link">
                                          <button class="btn btn-danger standard-payment-cancel-btn f-14">@lang('message.express-payment-form.cancel')</button>
                                      </a>
                                    </div>
                                  </div>
                                </div>
                              </form>
                              <!--- PAYPAL GATEWAY END-->

                              <!--- STRIPE GATEWAY START-->
                              <form class="dis-none" id="Stripe" name="Stripe" method="post" action="#" accept-charset="UTF-8">
                                {{ csrf_field() }}
                                <input name="order_no" value="{{ isset($paymentInfo['order']) ? $paymentInfo['order'] : '' }}" type="hidden">
                                <input name="item_name" value="{{ isset($paymentInfo['item_name']) ? $paymentInfo['item_name'] : '' }}" type="hidden">
                                <input name="merchant" value="{{ isset($paymentInfo['merchant_id']) ? $paymentInfo['merchant_id'] : '' }}" type="hidden">
                                <input name="merchant_uuid" value="{{ isset($paymentInfo['merchant']) ? $paymentInfo['merchant'] : '' }}" type="hidden">
                                <input name="currency" value="{{ $merchant->currency->code}}" type="hidden">
                                <input name="amount" class="form-control" value="{{ $amount }}" type="hidden">
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
                                                            <option value="10">11</option>
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
                                    <div class="col-md-12">
                                        <p class="text-danger" id="stripeError"></p>
                                    </div>
                                </div>
                                <div class="row">
                                  <div class="col-md-12">
                                    <br>
                                    <br>
                                    <div class="pull-right">
                                      <a href="#home" data-bs-toggle="tab" class="standard-payment-cancel-link first-letter text-decoration-none">
                                          <button class="btn btn-default standard-payment-cancel-btn border f-14 fw-bold me-2">@lang('message.express-payment-form.cancel')</button>
                                      </a>
                                      <button type="submit" class="btn btn-primary standard-payment-submit-btn">
                                          <i class="spinner fa fa-spinner fa-spin d-none"></i>
                                          <span class="standard-payment-submit-btn-txt f-14 fw-bold">
                                            @lang('message.express-payment-form.go-to-payment')
                                          </span>
                                      </button>
                                    </div>
                                  </div>
                                </div>
                              </form>
                              <!--- STRIPE GATEWAY END-->

                              <!--- PayUmoney GATEWAY START-->
                              <form class="dis-none" id="PayUmoney" name="PayUmoney" method="post" action="{{url('payment/payumoney')}}" accept-charset="UTF-8">
                                {{csrf_field()}}
                                <input name="order_no" value="{{isset($paymentInfo['order']) ? $paymentInfo['order'] : ''}}" type="hidden">
                                <input name="item_name" value="{{isset($paymentInfo['item_name']) ? $paymentInfo['item_name'] : ''}}" type="hidden">
                                <input name="merchant" value="{{isset($paymentInfo['merchant_id']) ? $paymentInfo['merchant_id'] : ''}}" type="hidden">
                                <input name="merchant_uuid" value="{{isset($paymentInfo['merchant']) ? $paymentInfo['merchant'] : ''}}" type="hidden">
                                <input class="form-control" name="amount" value="{{ $amount }}" type="hidden">
                                <div class="row">
                                  <div class="col-md-12">
                                    <div class="bs-callout-warning">
                                      <p>@lang('message.express-payment-form.payment-agreement')</p>
                                    </div>
                                    <div class="pull-right">
                                      <a href="#home" data-bs-toggle="tab" class="standard-payment-cancel-link text-decoration-none">
                                          <button class="btn btn-default standard-payment-cancel-btn border f-14 fw-bold me-2">@lang('message.express-payment-form.cancel')</button>
                                      </a>
                                      <button formnovalidate type="submit" class="btn btn-primary standard-payment-submit-btn">
                                          <i class="spinner fa fa-spinner fa-spin d-none"></i>
                                          <span class="standard-payment-submit-btn-txt f-14 fw-bold">
                                            @lang('message.express-payment-form.go-to-payment')
                                          </span>
                                      </button>
                                    </div>
                                  </div>
                                </div>
                              </form>
                              <!--- PayUmoney GATEWAY END-->

                              <!--- CoinPayments GATEWAY START-->
                              <form class="dis-none" id="Coinpayments" name="coinpayments" method="post" action="{{url('payment/coinpayments')}}" accept-charset="UTF-8" novalidate="novalidate">
                                {{csrf_field()}}
                                <input name="order_no" value="{{isset($paymentInfo['order']) ? $paymentInfo['order'] : ''}}" type="hidden">
                                <input name="item_name" value="{{isset($paymentInfo['item_name']) ? $paymentInfo['item_name'] : ''}}" type="hidden">
                                <input name="merchant" value="{{isset($paymentInfo['merchant_id']) ? $paymentInfo['merchant_id'] : ''}}" type="hidden">
                                <input name="merchant_uuid" value="{{isset($paymentInfo['merchant']) ? $paymentInfo['merchant'] : ''}}" type="hidden">
                                <input name="currency" value="{{isset($merchant->currency) ? $merchant->currency->code : ''}}" type="hidden">
                                <input class="form-control" name="amount" value="{{ $amount }}" type="hidden">
                                <div class="row">
                                  <div class="col-md-12">
                                    <div class="bs-callout-warning">
                                      <p>@lang('message.express-payment-form.payment-agreement')</p>
                                    </div>
                                    <div class="pull-right">
                                      <a href="#home" data-bs-toggle="tab" class="standard-payment-cancel-link text-decoration-none">
                                          <button class="btn btn-default standard-payment-cancel-btn border f-14 fw-bold me-2">@lang('message.express-payment-form.cancel')</button>
                                      </a>
                                      <button formnovalidate type="submit" class="btn btn-primary standard-payment-submit-btn">
                                          <i class="spinner fa fa-spinner fa-spin d-none"></i>
                                          <span class="standard-payment-submit-btn-txt f-14 fw-bold">
                                            @lang('message.express-payment-form.go-to-payment')
                                          </span>
                                      </button>
                                    </div>
                                  </div>
                                </div>
                              </form>
                              <!--- CoinPayments GATEWAY END-->
                            </div>
                          </div>
                        </div>
                      </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <script src="{{ theme_asset('public/js/jquery.validate.min.js') }}" type="text/javascript"></script>
      <script src="{{ theme_asset('public/js/jquery.ba-throttle-debounce.js') }}" type="text/javascript"></script>

      <script>

        $(document).on('click', '.tab-name', function() {
            $('.tab-pane').removeClass('show active')
            $("#payment").addClass('show active')
        })
        $(document).on('click', '.standard-payment-cancel-link', function() {

            $('.tab-pane').removeClass('show active')
            $("#home").addClass('show active')
            console.log('5');

        })

        var goToPaymentText = '{{ trans('message.express-payment-form.go-to-payment') }}';

        var forms = document.querySelectorAll('form');
        if (forms.length != 0)
        {
          forms[0].addEventListener("click", function(e)
          {
            if (e.target && e.target.nodeName == "INPUT")
            {
              hideFormsButFirst();
              setFormVisible(e.target.value);
            }
          });

          function hideFormsButFirst()
          {
            for (var i = 0; i < forms.length; ++i)
            {
              forms[i].style.display = 'none';
            }
            forms[0].style.display = 'block';
          }

          function setFormVisible(id)
          {
            id = id || "Mts";
            var form = document.getElementById(id);
            form.style.display = 'block';
          }

          function init()
          {
            hideFormsButFirst();
            setFormVisible();
          }
          init();
        }

        //Language script
        $('#lang').on('change', function(e)
        {
            e.preventDefault();
            lang = $(this).val();
            url = '{{ url('change-lang') }}';
            $.ajax(
            {
                type: 'get',
                url: url,
                data:
                {
                    lang: lang
                },
                success: function(msg)
                {
                    if (msg == 1)
                    {
                        location.reload();
                    }
                }
            });
        });

        $.validator.setDefaults({
          highlight: function(element) {
            $(element).parent('div').addClass('has-error');
          },
          unhighlight: function(element) {
            $(element).parent('div').removeClass('has-error');
          },
          errorPlacement: function (error, element) {
            error.insertAfter(element);
          }
        });

        function disableSumbitCancelButtons()
        {
            // initialize spinner and disable buttons
            $('.standard-payment-submit-btn, .standard-payment-cancel-btn').attr("disabled", true).click(function (e)
            {
                e.preventDefault();
            });
            $(".fa-spin").removeClass('d-none');
            $(".standard-payment-cancel-link").click(function (e)
            {
                e.preventDefault();
            });
            $(".standard-payment-submit-btn-txt").text('Paying...');
            form.submit();

            setTimeout(function(){
                // destroy spinner and enable buttons after 10 secs
                $('.standard-payment-submit-btn, .standard-payment-cancel-btn').removeAttr("disabled");
                $(".fa-spin").addClass('d-none');
                console.log('1');
                $('.standard-payment-cancel-link').attr({
                  'href': '#home',
                  'data-bs-toggle': 'tab'
                });
                $(".standard-payment-submit-btn-txt").text(goToPaymentText);
            },10000);
        }

        $('#Mts').validate(
        {
            rules: {
              email: {
                  required: true,
                  email: true,
              },
              password: {
                  required: true,
                  minlength: 6,
              },
            },
            submitHandler: function(form)
            {
                disableSumbitCancelButtons();
                form.submit();
            }
        });

        $('#2Checkout').validate(
        {
            submitHandler: function(form)
            {
                disableSumbitCancelButtons();
                form.submit();
            }
        });

        $('#PayUmoney').validate(
        {
            submitHandler: function(form)
            {
                disableSumbitCancelButtons();
                form.submit();
            }
        });

        $('#Coinpayments').validate(
        {
            submitHandler: function(form)
            {
                disableSumbitCancelButtons();
                form.submit();
            }
        });
        function isNumber(evt)
        {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }
            return true;
        }
        $('#Stripe').validate(
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
            submitHandler: function(form, e)
            {
              e.preventDefault();
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
              var currency   = $('#Stripe').find('input[name="currency"]').val().trim();
              var merchant   = $('#Stripe').find('input[name="merchant"]').val().trim();
              var amount     = {!! $paymentInfo['amount'] !!};

              $("#stripeError").html('');
              if (cardNumber && month && year && cvc) {
                $.ajax({
                    type: "POST",
                    url: SITE_URL + "/standard-merchant/stripe-make-payment",
                    data:
                    {
                      "_token":  '{{ csrf_token() }}',
                      'cardNumber': cardNumber,
                      'month': month,
                      'year': year,
                      'cvc': cvc,
                      'currency': currency,
                      'merchant': merchant,
                      'amount': amount,
                    },
                    dataType: "json",
                    beforeSend: function (xhr) {
                        $(".standard-payment-submit-btn").attr("disabled", true);
                    },
                }).done(function(response)
                {
                  if (response.data.status != 200) {
                      $("#stripeError").html(response.data.message);
                      $(".standard-payment-submit-btn").attr("disabled", true);
                      reject(response.data.status);
                      return false;
                  } else {
                      $(".standard-payment-submit-btn").attr("disabled", false);
                      resolve(response.data);
                  }
                });
            }
          });
          return promiseObj;
        }
        function confirmPayment()
        {
            makePayment().then(function(result) {
              var form = $('#Stripe')[0];
              var formData = new FormData(form);
              formData.append('_token', "{{ csrf_token() }}");
              formData.append('paymentIntendId', result.paymentIntendId);
              formData.append('paymentMethodId', result.paymentMethodId);
              $.ajax({
                  type: "POST",
                  url: SITE_URL + "/payment/stripe",
                  data: formData,
                  processData: false,
                  contentType: false,
                  cache: false,
                  beforeSend: function (xhr) {
                      $(".standard-payment-submit-btn").attr("disabled", true);
                      $(".fa-spin").removeClass('d-none');
                  },
              }).done(function(response)
              {
                  $(".fa-spin").addClass('d-none');
                  if (response.data.status != 200) {
                      $(".standard-payment-submit-btn").attr("disabled", true);
                      $("#stripeError").html(response.data.message);
                      return false;
                  } else {
                      window.location.replace(SITE_URL + '/payment/success');
                  }
              });
            });
        }
        $("#month").change(function() {
            makePayment();
        });
        $("#year, #cvc").on('keyup', $.debounce(1000, function() {
            makePayment();
        }));
        $("#cardNumber").on('keyup', $.debounce(1000, function() {
            makePayment();
        }));
        // For card number design
        document.getElementById('cardNumber').addEventListener('input', function (e) {
          var target = e.target, position = target.selectionEnd, length = target.value.length;
          target.value = target.value.replace(/[^\d]/g, '').replace(/(.{4})/g, '$1 ').trim();
          target.selectionEnd = position += ((target.value.charAt(position - 1) === ' ' && target.value.charAt(length - 1) === ' ' && length !== target.value.length) ? 1 : 0);
        });

      </script>
      <script src="https://www.paypal.com/sdk/js?client-id={{ isset($clientId) ? $clientId : '' }}&disable-funding=paylater&currency={{ isset($currencyCode) ? $currencyCode : '' }}"></script>
      <script>
          paypal.Buttons({
              createOrder: function (data, actions) {
                  // This function sets up the details of the transaction, including the amount and line item details.
                  return actions.order.create({
                      purchase_units: [{
                          amount: {
                              value: "{!! $amount !!}"
                          }
                      }]
                  });
              },
              onApprove: function (data, actions) {
                  // This function captures the funds from the transaction.
                  return actions.order.capture().then(function (details) {
                      // This function shows a transaction success message to your buyer.
                      // alert('Transaction completed by ' + details.payer.name.given_name);
                      $('#Paypal').append('<input type="hidden" name="amount" id="paypalAmount" />');
                      $('#Paypal').append('<input type="hidden" name="payment_id" id="payment_id" />');
                      $("#paypalAmount").val(btoa(details.purchase_units[0].amount.value));
                      $("#payment_id").val(btoa(details.id));
                      paypalSuccess();
                  });
              }
          }).render('#paypal-button-container');
          function paypalSuccess()
          {
              var form = $('#Paypal')[0];
              var formData = new FormData(form);
              formData.append('_token', "{{ csrf_token() }}");
              $.ajax({
                  type: "POST",
                  url: SITE_URL + "/payment/paypal_payment_success",
                  data: formData,
                  processData: false,
                  contentType: false,
                  cache: false,
                  beforeSend: function (xhr) {
                      $(".standard-payment-submit-btn").attr("disabled", true);
                      $(".fa-spin").show();
                  },
              }).done(function(response) {
                  $(".fa-spin").hide();
                  window.location.replace(SITE_URL + response.data.redirectedUrl);
            });
          }
      </script>
    </body>
</html>
