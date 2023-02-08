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
                        <h3 class="sub-title">{{ __('Confirmation') }}</h3>
                        <p class="text-gray-500 text-16 text-justify">{{ __('Fill in the details of your bank deposit with the attach. Take a look over the details before confirm') }}</p>
                    </div>
                    <!-- Sub title end-->
                </div>

                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-xl-10">
                            <div class="d-flex w-100 mt-4">
                                <ol class="breadcrumb w-100">
                                    <li class="breadcrumb-active"><a href="#">{{ __('Create') }}</a></li>
                                    <li class="breadcrumb-first"><a href="#">{{ __('Confirmation') }}</a></li>
                                    <li class="active">{{ __('Success') }}</li>
                                </ol>
                            </div>

                            <div class="bg-secondary rounded mt-4 shadow p-35">
                                <form action="{{ url('deposit/bank-payment') }}" style="display: block;" method="POST" accept-charset="UTF-8" id="bank_deposit_form" enctype="multipart/form-data">
                                    <input value="{{csrf_token()}}" name="_token" id="token" type="hidden">
                                    <input value="{{$transInfo['payment_method']}}" name="method" id="method" type="hidden">
                                    <input value="{{$transInfo['totalAmount']}}" name="amount" id="amount" type="hidden">

                                    <div class="form-group">
                                        <label for="bank">@lang('message.dashboard.deposit.select-bank')</label>
                                        <select class="form-control bank" name="bank" id="bank">
                                            @foreach($banks as $bank)
                                                <option value="{{ $bank['id'] }}" {{ isset($bank['is_default']) && $bank['is_default'] == 'Yes' ? "selected" : "" }}>{{ $bank['bank_name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="card">
                                        <div class="card-body">
                                                <div class="container">
                                                    @if ($bank['account_name'])
                                                        <div class="row">
                                                            <div class="col-sm">
                                                                <p class="form-control-static">@lang('message.dashboard.left-table.bank-transfer.bank-account-name')</p>
                                                            </div>

                                                            <div class="col-sm">
                                                                <p class="form-control-static" id="account_name">{{  $bank['account_name'] }}</p>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <br>

                                                    @if ($bank['account_number'])
                                                        <div class="row">
                                                            <div class="col-sm">
                                                            <p class="form-control-static">@lang('message.dashboard.left-table.bank-transfer.bank-account-number')</p>
                                                            </div>
                                                            <div class="col-sm">
                                                            <p class="form-control-static" id="account_number">{{  $bank['account_number'] }}</p>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <br>

                                                    @if ($bank['bank_name'])
                                                        <div class="row">
                                                            <div class="col-sm">
                                                            <p class="form-control-static">@lang('message.dashboard.left-table.bank-transfer.bank-name')</p>
                                                            </div>
                                                            <div class="col-sm">
                                                            <p class="form-control-static" id="bank_name">{{  $bank['bank_name'] }}</p>
                                                            </div>
                                                        </div>
                                                    @endif

                                                </div>
                                        </div>
                                    </div>

                                    <div class="mt-4" id="attached_file">
                                        <div class="form-group">
                                            <label for="bank">@lang('message.dashboard.payout.payout-setting.modal.attached-file')</label>
                                            <input type="file" name="attached_file" class="form-control input-file-field" data-rel="">
                                        </div>
                                    </div>

                                    <!--bank logo-->
                                    <p>
                                        @lang('message.dashboard.deposit.deposit-via')
                                        <span id="bank_logo"></span>
                                    </p>

                                    <div class="mt-4"><strong>@lang('message.dashboard.confirmation.details')</strong></div>

                                    <div class="row mt-4">
                                        <div class="col-md-6">@lang('message.dashboard.deposit.deposit-amount')</div>
                                        <div class="col-md-6 text-right"><strong>{{ moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['amount'])) }}</strong></div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-md-6">@lang('message.dashboard.confirmation.fee')</div>
                                        <div class="col-md-6 text-right"><strong>{{ moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['fee'])) }}</strong></div>
                                    </div>
                                    <hr />

                                    <div class="row">
                                        <div class="col-md-6 h6"><strong>@lang('message.dashboard.confirmation.total')</strong></div>
                                        <div class="col-md-6 text-right"><strong>{{ moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['totalAmount'])) }}</strong></div>
                                    </div>

                                    <div class="row m-0 mt-4 justify-content-between">
                                        <div>
                                            <a href="#" class="deposit-bank-confirm-back-link">
                                                <p class="py-2 text-active text-underline deposit-bank-confirm-back-btn mt-2">
                                                   <u><i class="fas fa-long-arrow-alt-left"></i> @lang('message.dashboard.button.back')</u>
                                                </p>
                                            </a>
                                        </div>

                                        <div>
                                            <button type="submit" class="btn btn-primary px-4 py-2 mt-2" id="deposit-money">
                                                <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="deposit-money-text">@lang('message.dashboard.button.confirm')</span>
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

<script src="{{theme_asset('public/js/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{theme_asset('public/js/additional-methods.min.js')}}" type="text/javascript"></script>
<script>

    function depositBankBack()
    {
        localStorage.setItem("depositConfirmPreviousUrl",document.URL);
        window.history.back();
    }

    function getBanks()
    {
        var bank = $('#bank').val();
        if (bank)
        {
            $.ajax({
                headers:
                {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                url: SITE_URL+"/deposit/bank-payment/get-bank-detail",
                dataType: "json",
                cache: false,
                data: {
                    'bank': bank,
                }
            })
            .done(function(response)
            {
                // log(response);
                if (response.status == true)
                {
                    $('#bank_name').html(response.bank.bank_name);
                    $('#account_name').html(response.bank.account_name);
                    $('#account_number').html(response.bank.account_number);

                    if (response.bank_logo) {
                        $("#bank_logo").html(`<img class="" src="${SITE_URL}/public/uploads/files/bank_logos/${response.bank_logo}" class="w-120p" width="120" height="80"/>`);
                    } else {
                        $("#bank_logo").html(`<img class="" src="${SITE_URL}/public/images/payment_gateway/bank.jpg" class="w-120p" width="120" height="80"/>`);
                    }
                }
                else
                {
                    $('#bank_name').html('');
                    $('#bank_branch_name').html('');
                    $('#bank_branch_city').html('');
                    $('#bank_branch_address').html('');
                    $('#swift_code').html('');
                    $('#account_name').html('');
                    $('#account_number').html('');
                }
            });
        }
    }

    $(window).on('load',function()
    {
        getBanks();
    });

    $("#bank").change(function()
    {
        getBanks();
    });

    $(document).on('change', '#bank', function()
    {
        getBanks();
    });

    jQuery.extend(jQuery.validator.messages, {
        required: "{{ __('This field is required.') }}",
    })

    $('#bank_deposit_form').validate({
        rules: {
            attached_file: {
                required: true,
                extension: "png|jpg|jpeg|gif|bmp|pdf|docx|txt|rtf",
            },
        },
        messages: {
          attached_file: {
            extension: "{{ __("Please select (png, jpg, jpeg, gif, bmp, pdf, docx,txt or rtf) file!") }}"
          },
        },
        submitHandler: function(form)
        {
            $("#deposit-money").attr("disabled", true);
            $(".spinner").show();
            var pretext=$("#deposit-money-text").text();
            $("#deposit-money-text").text("{{ __('Confirming...') }}");

            //Make back button disabled and prevent click
            $('.deposit-bank-confirm-back-btn').attr("disabled", true).click(function (e)
            {
                e.preventDefault();
            });

            //Make back anchor prevent click
            $('.deposit-bank-confirm-back-link').click(function (e)
            {
                e.preventDefault();
            });

            form.submit();
            setTimeout(function(){
                $("#deposit-money").removeAttr("disabled");
                $(".spinner").hide();
                $("#deposit-money-text").text(pretext);
            },10000);
        }
    });

    //Only go back by back button, if submit button is not clicked
    $(document).on('click', '.deposit-bank-confirm-back-btn', function (e)
    {
        e.preventDefault();
        depositBankBack();
    });

</script>
@endsection
