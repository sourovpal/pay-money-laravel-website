@extends('admin.layouts.master')
@section('title', __('Currency Payment Methods'))

@section('head_style')
    <!-- sweetalert -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/sweetalert/sweetalert.css') }}">
    <style>
        .sa-button-container .confirm {
            color: #fff;
            background-color: #635BFF !important;
            border-color: #635BFF;
            padding: 8px 15px;
        }

        .sa-button-container .confirm:hover {
            background-color: #7ecff4 !important;
        }

        .sa-button-container .cancel {
            color: #fff;
            background-color: #f87272 !important;
            border-color: #f87272;
            padding: 8px 15px;
        }

        .sa-button-container .cancel:hover {
            background-color: #dd1515 !important;
        }

    </style>
@endsection

@section('page_content')
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="top-bar-title padding-bottom">{{ __('Currency Payment Methods') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-body">
            <div class="dropdown">
                <button class="btn btn-default dropdown-toggle F-14" type="button" data-bs-toggle="dropdown">{{ __('Currency') }} : <span
                        class="currencyName">{{ $currency->name }}</span>
                    <span class="caret"></span></button>
                <ul class="dropdown-menu xss f-14 p-0">
                    @foreach ($currencyList as $currencyItem)
                        <li class="listItem px-2 py-1" data-rel="{{ $currencyItem->id }}">
                            <a class="px-2 py-1 d-block" href="#">{{ $currencyItem->name }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            @include('admin.common.paymentMethod_menu')
        </div>

        <div class="col-md-9">
            <div class="box box-info">
                <div class="box-header with-border text-center">
                    <h3 class="box-title">{{ ucfirst($list_menu) }} {{ __('Settings') }}</h3>
                </div>
                <form action='{{ url(\Config::get('adminPrefix') . '/settings/payment-methods/update-paymentMethod-Credentials') }}' class="form-horizontal" method="POST" id="currencyPaymentMethod_form">
                    @csrf

                    <input type="hidden" value="{{ isset($currencyPaymentMethod->id) ? $currencyPaymentMethod->id : '' }}"
                        name="id" id="id">
                    <input type="hidden" value="{{ $currency->id }}" name="currency_id" id="currency_id">
                    <input type="hidden" value="{{ $paymentMethod }}" name="paymentMethod" id="paymentMethod">
                    <input type="hidden" value="{{ $list_menu }}" name="tabText" id="tabText">

                    <div class="box-body">
                        @if ($list_menu == 'coinpayments')
                            <!-- coinPayments - Merchant Id -->
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="coinPayments[merchant_id]">{{ __('Merchant Id') }}</label>
                                <div class="col-sm-5">
                                    <input class="form-control coinPayments[merchant_id]" name="coinPayments[merchant_id]"
                                        type="text" placeholder="CoinPayments Merchant Id"
                                        value="{{ isset($currencyPaymentMethod->method_data) ? json_decode($currencyPaymentMethod->method_data)->merchant_id : '' }}"
                                        id="coinPayments_merchant_id">
                                    @if ($errors->has('coinPayments[merchant_id]'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('coinPayments[merchant_id]') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <!-- coinPayments - Public Key -->
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="coinPayments[public_key]">{{ __('Public Key') }}</label>
                                <div class="col-sm-5">
                                    <input class="form-control coinPayments[public_key]" name="coinPayments[public_key]"
                                        type="text" placeholder="CoinPayments Public Key"
                                        value="{{ isset($currencyPaymentMethod->method_data) ? json_decode($currencyPaymentMethod->method_data)->public_key : '' }}"
                                        id="coinPayments_public_key">
                                    @if ($errors->has('coinPayments[public_key]'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('coinPayments[public_key]') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <!-- coinPayments - Private Key -->
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="coinPayments[private_key]">{{ __('Private Key') }}</label>
                                <div class="col-sm-5">
                                    <input class="form-control coinPayments[private_key]" name="coinPayments[private_key]"
                                        type="text" placeholder="CoinPayments Private Key"
                                        value="{{ isset($currencyPaymentMethod->method_data) ? json_decode($currencyPaymentMethod->method_data)->private_key : '' }}"
                                        id="coinPayments_private_key">
                                    @if ($errors->has('coinPayments[private_key]'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('coinPayments[private_key]') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <!-- coinPayments - processing_time -->
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="processing_time">{{ __('Processing Time (days)') }}</label>
                                <div class="col-sm-5">
                                    <input class="form-control processing_time" name="processing_time" type="text"
                                        placeholder="{{ __('CoinPayments Processing Time') }}"
                                        value="{{ isset($currencyPaymentMethod->processing_time) ? $currencyPaymentMethod->processing_time : '' }}"
                                        id="processing_time">

                                    @if ($errors->has('processing_time'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('processing_time') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <!-- coinPayments - Status -->
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="coinPayments_status">Status</label>
                                <div class="col-sm-5">
                                    <select class="form-control" name="coinPayments_status" id="coinPayments_status">
                                        <option value=''>{{ __('Select Status') }}</option>
                                        <option value='Active'
                                            {{ isset($currencyPaymentMethod->activated_for) && $currencyPaymentMethod->activated_for == json_encode(['deposit' => '']) ? 'selected' : '' }}>
                                            {{ __('Active') }}</option>
                                        <option value='Inactive'
                                            {{ isset($currencyPaymentMethod->activated_for) && $currencyPaymentMethod->activated_for == json_encode(['' => '']) ? 'selected' : '' }}>
                                            {{ __('Inactive') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div class="box-footer">
                                <a id="cancel_anchor" href="{{ url(\Config::get('adminPrefix') . '/settings/currency') }}"
                                    class="btn btn-theme-danger">{{ __('Cancel') }}</a>
                                <button type="submit" class="btn btn-theme pull-right" id="paymentMethodList_update">
                                    <i class="fa fa-spinner fa-spin d-none"></i> <span
                                        id="paymentMethodList_update_text">{{ __('Update') }}</span>
                                </button>
                            </div>

                        @elseif ($list_menu == 'blockio')
							{{ __('Work on future') }}
                        @endif

                    </div>
                </form>
            </div>
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
@endsection

@push('extra_body_scripts')

    <!-- jquery.validate -->
    <script src="{{ asset('public/dist/js/jquery.validate.min.js') }}" type="text/javascript"></script>

    <!-- jquery.validate additional-methods -->
    <script src="{{ asset('public/dist/js/jquery-validation-1.17.0/dist/additional-methods.min.js') }}"
        type="text/javascript"></script>

    <!-- sweetalert -->
    <script src="{{ asset('public/backend/sweetalert/sweetalert.min.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(function() {
            $(".select2").select2({});
        });

        $('#currencyPaymentMethod_form').validate({
            rules: {
                "coinPayments[merchant_id]": {
                    required: true,
                },
                "coinPayments[public_key]": {
                    required: true,
                },
                "coinPayments[private_key]": {
                    required: true,
                },
                coinPayments_status: {
                    required: true,
                },

                processing_time: {
                    required: true,
                    number: true,
                },
            },
            messages: {
            },
            submitHandler: function(form) {
                $("#paymentMethodList_update").attr("disabled", true);
                $('#cancel_anchor').attr("disabled", "disabled");
                $(".fa-spin").removeClass("d-none");
                $("#paymentMethodList_update_text").text('Updating...');
                // Click False
                $('#paymentMethodList_update').click(false);
                $('#cancel_anchor').click(false);
                form.submit();
            }
        });

        function getCpmId(bank_id, currency_id, paymentMethod) {
            $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: "POST",
                    url: SITE_URL + "/" + ADMIN_PREFIX + "/settings/payment-methods/getCpmId",
                    cache: false,
                    dataType: 'json',
                    data: {
                        'bank_id': bank_id,
                        'currency_id': currency_id,
                    },
                })
                .done(function(response) {
                    var preview_edit_bank_logo = $('.preview_edit_bank_logo'),
                        img;

                    if (response.status == true) {
                        $('#bank_id').val(bank_id);
                        $('#edit_currency_id').val(currency_id);
                        $('#edit_paymentMethod').val(paymentMethod);
                        $('#currencyPaymentMethodId').val(response.cpmId);

                        $('#edit_default').val(response.is_default);

                        var activated_for = JSON.parse(response.cpmActivatedFor);
                        if (activated_for.hasOwnProperty('deposit')) {
                            $('#edit_status').val('Active');
                        } else {
                            $('#edit_status').val('Inactive');
                        }
                        $('#edit_account_name').val(response.account_name);
                        $('#edit_account_number').val(response.account_number);
                        $('#edit_branch_address').val(response.bank_branch_address);
                        $('#edit_branch_city').val(response.bank_branch_city);
                        $('#edit_branch_name').val(response.bank_branch_name);
                        $('#edit_bank_name').val(response.bank_name);
                        $('#edit_country').val(response.country_id);
                        $('#edit_swift_code').val(response.swift_code);

                        if (response.bank_logo && response.file_id) {
                            //et file ID of bank logo
                            $("#file_id").val(response.file_id);

                            $(".preview_edit_bank_logo").html(
                                `<img class="thumb-bank-logo" data-bank-logo="${response.bank_logo}" data-file-id="${response.file_id}"
		         	                                  src="${SITE_URL}/public/uploads/files/bank_logos/${response.bank_logo}" width="120" height="80"/><span class="remove_edit_bank_logo_preview"></span>`
                                );
                        } else {
                            $(".preview_edit_bank_logo").html(
                                `<img class="thumb-bank-logo" src="${SITE_URL}/public/uploads/userPic/default-image.png" width="120" height="80"/>`
                                );
                        }

                        $('#editModal').modal();
                    } else {
                        swal('Oops...', 'No response !', 'error');
                    }
                });
        }

        $(window).on('load', function() {
            var previousUrl = document.referrer;
            var urlByOwn = SITE_URL + '/' + ADMIN_PREFIX + '/settings/currency';
            if (previousUrl == urlByOwn) {
                localStorage.removeItem('currencyId');
                localStorage.removeItem('currencyName');
            } else {
                if ((localStorage.getItem('currencyName')) && (localStorage.getItem('currencyId'))) {
                    $('.currencyName').text(localStorage.getItem('currencyName'));
                    $('#currency_id').val(localStorage.getItem('currencyId'));
                    getPaymentMethodsDetails();
                } else {
                    getPaymentMethodsSpecificCurrencyDetails();
                }
            }
        });


        $('.listItem').on('click', function() {
            var currencyId = $(this).attr('data-rel');
            var currencyName = $(this).text();

            localStorage.setItem('currencyId', currencyId);
            localStorage.setItem('currencyName', currencyName);

            $('.currencyName').text(currencyName);
            $('#currency_id').val(currencyId);
            getPaymentMethodsDetails();
        });


        //Window on load/click on list item get fees limit details
        function getPaymentMethodsDetails() {
            var currencyId = $('#currency_id').val();
            var paymentMethod = $('#paymentMethod').val();
            var token = $("input[name=_token]").val();

            var url = SITE_URL + '/' + ADMIN_PREFIX + '/settings/get-payment-methods-details';
            $.ajax({
                url: url,
                type: "post",
                // async : false,
                data: {
                    'currency_id': currencyId,
                    'paymentMethod': paymentMethod,
                    '_token': token
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status == 200) {
                        $('#id').val(data.currencyPaymentMethod.id);

                        $('#coinPayments_merchant_id').val(JSON.parse(data.currencyPaymentMethod.method_data).merchant_id);
                        $('#coinPayments_public_key').val(JSON.parse(data.currencyPaymentMethod.method_data).public_key);
                        $('#coinPayments_private_key').val(JSON.parse(data.currencyPaymentMethod.method_data).private_key);

                        $('#processing_time').val(data.currencyPaymentMethod.processing_time);

                        var activated_for = JSON.parse(data.currencyPaymentMethod.activated_for);

                        if (activated_for.hasOwnProperty('deposit')) {
                            $('#coinPayments_status').val('Active');
                        } else {
                            $('#coinPayments_status').val('Inactive');
                        }
                    } else {
                        $('#id').val('');

                        $('#coinPayments_merchant_id').val('');
                        $('#coinPayments_public_key').val('');
                        $('#coinPayments_private_key').val('');

                        $('#processing_time').val('');

                        $('#coinPayments_status').val('');
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

        //Get Specific Currency Details
        function getPaymentMethodsSpecificCurrencyDetails() {
            var currencyId = $('#currency_id').val();
            var paymentMethod = $('#paymentMethod').val();
            var token = $("input[name=_token]").val();
            var url = SITE_URL + '/' + ADMIN_PREFIX + '/settings/get-payment-methods-specific-currency-details';

            $.ajax({
                url: url,
                type: "post",
                // async : false,
                data: {
                    'currency_id': currencyId,
                    'paymentMethod': paymentMethod,
                    '_token': token
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status == 200) {
                        $('.currencyName').text(data.currency.name);
                        $('#currency_id').val(data.currency.id);

                        $('#coinPayments_merchant_id').val(JSON.parse(data.currencyPaymentMethod.method_data).merchant_id);
                        $('#coinPayments_public_key').val(JSON.parse(data.currencyPaymentMethod.method_data).public_key);
                        $('#coinPayments_private_key').val(JSON.parse(data.currencyPaymentMethod.method_data).private_key);

                        $('#processing_time').val(data.currencyPaymentMethod.processing_time);

                        var activated_for = JSON.parse(data.currencyPaymentMethod.activated_for);
                        if (activated_for.hasOwnProperty('deposit')) {
                            $('#coinPayments_status').val('Active');
                        } else {
                            $('#coinPayments_status').val('Inactive');
                        }
                    } else {
                        $('#id').val('');
                        $('.currencyName').text(data.currency.name);
                        $('#currency_id').val(data.currency.id);

                        $('#coinPayments_merchant_id').val('');
                        $('#coinPayments_public_key').val('');
                        $('#coinPayments_private_key').val('');

                        $('#processing_time').val('');

                        $('#coinPayments_status').val('');
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }


        function checkBankDefault(is_default) {
            var cell = '';
            if (is_default == "Yes") {
                cell = '<span class="label label-success">Yes</span>';
            } else if (is_default == "No") {
                cell = '<span class="label label-danger">No</span>';
            }
            return cell;
        }

        function checkBankStatus(activated_for) {
            var cell = '';
            var activated = JSON.parse(activated_for);
            if (activated.hasOwnProperty('deposit')) {
                cell = '<span class="label label-success">Active</span>';
            } else {
                cell = '<span class="label label-danger">Inactive</span>';
            }
            return cell;
        }
    </script>

@endpush
