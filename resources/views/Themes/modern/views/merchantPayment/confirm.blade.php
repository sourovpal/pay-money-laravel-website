<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="{{ meta(Route::current()->uri(), 'description') }}">
        <meta name="keywords" content="{{ meta(Route::current()->uri(), 'keywords') }}">
        <title>{{ meta(Route::current()->uri(), 'title') }}<?= isset($additionalTitle) ? ' | '.$additionalTitle : '' ?></title>
        <script src="{{ theme_asset('public/js/jquery.min.js') }}" type="text/javascript"></script>
        @include('user_dashboard.layouts.common.style')
        <link rel="javascript" href="{{ theme_asset('public/js/respond.js') }}">

        <!---favicon-->
        @if (!empty(settings('favicon')))
            <link rel="shortcut icon" href="{{theme_asset('public/images/logos/'.settings('favicon'))}}" />
        @endif

        @include('user_dashboard.layouts.common.style')
    </head>
    <body>
        <br><br><br>
        <div class="container">
            <div class="row">
                <div class="col-md-6 offset-md-3 marginTopPlus">
                    <h3 style="margin-bottom:15px;">{{$transInfo->app->merchant->user->first_name}} {{$transInfo->app->merchant->user->last_name}}'s {{$transInfo->app->merchant->business_name}} </h3>
                    <div class="bg-secondary rounded mt-5 shadow p-35">


                        <div>
                            <div class="d-flex flex-wrap">
                                <div>
                                    <p >@lang('message.express-payment.pay-with') {{ $transInfo->payment_method }}</p>
                                </div>
                            </div>

                            <div class="mt-4">
                                <p class="sub-title">@lang('message.dashboard.confirmation.details')</p>
                            </div>

                            <div>
                                <div class="d-flex flex-wrap justify-content-between mt-2">
                                    <div>
                                        <p>@lang('message.express-payment.about-to-make')&nbsp;{{ $transInfo->payment_method }}&nbsp;</p>
                                    </div>

                                   
                                </div>

                                <div class="d-flex flex-wrap justify-content-between mt-2">
                                    <div>
                                        <p>@lang('message.dashboard.left-table.amount')</p>
                                    </div>

                                    <div class="pl-2">
                                        <p><strong>{{$currSymbol}} {{ formatNumber($transInfo->amount) }}</strong></p>
                                    </div>
                                </div>
                                
                            </div>


                            <div class="row m-0 mt-4 justify-content-between">
                                <div>
                                    <form action="{{ url('merchant/payment/cancel') }}" method="get">
                                        <button class="text-active deposit-confirm-back-link express-payment-confirm-back-btn" style="border:none;background:transparent">
                                            <strong><u><i class="fas fa-long-arrow-alt-left"></i>@lang('message.form.cancel')</u></strong>
                                        </button>
                                    </form>


                                </div>

                                <div>
                                    <form action="{{url('merchant/payment/confirm')}}" method="get" id="express-payment-confirm-form">
                                        <button type="button" class="btn btn-cust express-payment-confirm-submit-btn">
                                          <i class="spinner fa fa-spinner fa-spin d-none"></i>
                                          <span class="btn btn-primary express-payment-submit-btn-txt" style="font-weight: bolder;">
                                            <strong>@lang('message.dashboard.button.confirm') &nbsp; <i class="fa fa-angle-right"></i></strong>
                                          </span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

<script type="text/javascript">
    $(document).on('click', '.express-payment-confirm-submit-btn', function (e)
    {
        e.preventDefault();
        $('.express-payment-confirm-submit-btn, .express-payment-confirm-back-btn').attr("disabled", true).click(function (e)
        {
            e.preventDefault();
        });
        $(".fa-spin").removeClass("d-none");
        $('.express-payment-submit-btn-txt').text('Confirming...')
        $('#express-payment-confirm-form').submit();
    });
</script>
