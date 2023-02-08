@extends('user_dashboard.layouts.app')

@section('content')
<section class="min-vh-100">
    <div class="mt-30">
        <div class="container-fluid">
            <!-- Page title start -->
            <div class="d-flex justify-content-between">
                <div>
                    <h3 class="page-title">{{ __('Withdrawals') }}</h3>
                </div>

                <div>
                    <a href="{{ url('/payout') }}">
                        <button class="btn btn-primary px-4 py-2" data-toggle="modal" data-target="#addModal" id="addBtn">
                            <i class="fa fa-plus"></i> @lang('message.dashboard.payout.payout-setting.add-setting')
                        </button>
                    </a>
                </div>
            </div>

            <!-- Page title end-->
            <div class="mt-4 border-bottom">
                <div class="d-flex flex-wrap justify-content-between">
                    <div>
                        <div class="d-flex flex-wrap">
                            <a href="{{url('/payouts')}}">
                                <div class="mr-4 pb-3">
                                    <p class="text-16 font-weight-400 text-gray-500">{{ __('Payout list') }}</p>
                                </div>
                            </a>

                            <a href="{{url('/payout/setting')}}">
                                <div class="mr-4 border-bottom-active pb-3">
                                    <p class="text-16 font-weight-600 text-active">{{ __('Payout settings') }}  </p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            @include('user_dashboard.layouts.common.alert')
                            <div class="bg-secondary mt-3 shadow">
                                <div class="table-responsive">
                                    @if($payoutSettings->count() > 0)
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th class="pl-5">{{ __('Withdrawal Type') }}</th>
                                                    <th>@lang('message.dashboard.payout.payout-setting.account')</th>
                                                    <th class="pr-5 text-right">{{ __('Action') }}</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach($payoutSettings as $row)
                                                    <tr class="row_id_{{ $row->id }}">
                                                        <td class="pl-5">
                                                            <p class="">{{ $row->paymentMethod->name }}</p>
                                                        </td>

                                                        <td>
                                                            @if($row->paymentMethod->id == Paypal)
                                                                {{ $row->email }}
                                                            @elseif ($row->paymentMethod->id == Bank)
                                                                {{ $row->account_name }} (*****{{ substr($row->account_number,-4) }}
                                                                )<br/>
                                                                {{ $row->bank_name }}
                                                            @elseif ($row->paymentMethod->id == Crypto)
                                                                {{ $row->currency->code }} <br> {{ $row->crypto_address }}
                                                            @elseif(config('mobilemoney.is_active') && $row->paymentMethod->id == (defined('MobileMoney') ? MobileMoney : ''))
                                                                {{ $row->mobilemoney->mobilemoney_name ?? __('Not found') }} (*****{{ substr($row->mobile_number,-4) }})
                                                            @endif
                                                        </td>
                                                        <td class="pr-5 text-right">
                                                            <a data-id="{{ $row->id }}" data-type="{{ $row->type }}" data-obj="{{ json_encode($row->getAttributes()) }}" class="btn btn-sm btn-light mr-lg-2 mt-2 edit-setting"><i class="far fa-edit"></i></a>

                                                            <form action="{{ url('payout/setting/delete') }}" method="post" style="display: inline">
                                                                @csrf
                                                                <input type="hidden" name="id" value="{{ $row->id }}">
                                                                <a class="btn btn-sm btn-light mt-2 delete-setting" data-toggle="modal" data-target="#delete-warning-modal" data-title="{{ __("Delete Data") }}"
                                                                data-message="{{ __("Are you sure you want to delete this Data ?") }}" data-row="{{ $row->id }}" href=""><i class="fa fa-trash"></i></a>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="p-5 text-center">
                                            <svg width="96" height="96" fill="none" class="mx-auto mb-6 text-gray-900"><path d="M36 28.024A18.05 18.05 0 0025.022 39M59.999 28.024A18.05 18.05 0 0170.975 39" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><ellipse cx="37.5" cy="43.5" rx="4.5" ry="7.5" fill="currentColor"></ellipse><ellipse cx="58.5" cy="43.5" rx="4.5" ry="7.5" fill="currentColor"></ellipse><path d="M24.673 75.42a9.003 9.003 0 008.879 5.563m-8.88-5.562A8.973 8.973 0 0124 72c0-7.97 9-18 9-18s9 10.03 9 18a9 9 0 01-8.448 8.983m-8.88-5.562C16.919 68.817 12 58.983 12 48c0-19.882 16.118-36 36-36s36 16.118 36 36-16.118 36-36 36a35.877 35.877 0 01-14.448-3.017" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M41.997 71.75A14.94 14.94 0 0148 70.5c2.399 0 4.658.56 6.661 1.556a3 3 0 003.999-4.066 12 12 0 00-10.662-6.49 11.955 11.955 0 00-7.974 3.032c1.11 2.37 1.917 4.876 1.972 7.217z" fill="currentColor"></path></svg>
                                            <p>{{ __('Sorry!') }} @lang('message.dashboard.payout.list.not-found')</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-4">
                                {{ $payoutSettings->links('vendor.pagination.bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- addModal Modal-->
<div class="modal fade" id="addModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="display: block;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title text-18 font-weight-600">@lang('message.dashboard.payout.payout-setting.modal.title')</h3>
            </div>
            <div class="modal-body">
                <form id="payoutSettingForm" method="post">
                    {{csrf_field()}}
                    <div id="settingId"></div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>{{ __('Withdrawal Type') }}</label>
                            <select name="type" id="type" class="form-control">
                                @foreach($paymentMethods as $method)
                                    <option value="{{ $method->id }}">{{ $method->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- Bank Form --}}
                    <div id="bankForm">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('message.dashboard.payout.payout-setting.modal.bank-account-holder-name')</label>
                                <input name="account_name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>@lang('message.dashboard.payout.payout-setting.modal.account-number')</label>
                                <input name="account_number" class="form-control" onkeyup="this.value = this.value.replace(/\s/g, '')">
                            </div>
                            <div class="form-group">
                                <label>@lang('message.dashboard.payout.payout-setting.modal.swift-code')</label>
                                <input name="swift_code" class="form-control" onkeyup="this.value = this.value.replace(/\s/g, '')">
                            </div>
                            <div class="form-group">
                                <label>@lang('message.dashboard.payout.payout-setting.modal.bank-name')</label>
                                <input name="bank_name" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('message.dashboard.payout.payout-setting.modal.branch-name')</label>
                                <input name="branch_name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>@lang('message.dashboard.payout.payout-setting.modal.branch-city')</label>
                                <input name="branch_city" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>@lang('message.dashboard.payout.payout-setting.modal.branch-address')</label>
                                <input name="branch_address" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>@lang('message.dashboard.payout.payout-setting.modal.country')</label>
                                <select name="country" class="form-control">
                                    @foreach($countries as $country)
                                        <option value="{{$country->id}}">{{$country->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    {{-- Paypal --}}
                    <div id="paypalForm" style="margin:0 auto;display: none">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang('message.dashboard.payout.payout-setting.modal.email')</label>
                                <input name="email" class="form-control" onkeyup="this.value = this.value.replace(/\s/g, '')">
                            </div>
                        </div>
                    </div>
                    {{-- Crypto Payment Form --}}
                    <div id="CryptoForm" style="margin:0 auto;display: none">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>{{ __('Currency') }}</label>
                                <select name="currency" class="form-control" id="currency">
                                    @foreach($currencies as $currency)
                                        <option value="{{ $currency->id }}">{{ $currency->code }}</option>
                                    @endforeach
                                </select>
                                <label id="currency-error" class="error d-none"></label>
                            </div>
                            <div class="form-group">
                                <label>{{ __('Crypto Address') }}</label>
                                <input type="text" name="crypto_address" class="form-control" id="crypto_address">
                                <small class="form-text text-muted"><b>{{ __('*Providing wrong address may permanent loss of your coin') }}</b></small>
                                <label id="crypto-address-error" class="error d-none"></label>
                            </div>
                        </div>
                    </div>
                    {{-- Mobile Mooney --}}
                    @if (config('mobilemoney.is_active'))
                    <div id="mobileMoneyForm" style="margin:0 auto;display: none">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>{{ __('Network') }}</label>
                                <select name="mobilemoney_id" class="form-control" id="mobilemoney_id">
                                    @foreach($networks as $id => $network)
                                        <option value="{{ $id }}">{{ $network }}</option>
                                    @endforeach
                                </select>
                                <label id="currency-error" class="error d-none"></label>
                            </div>
                            <div class="form-group">
                                <label>{{ __('Mobile Number') }}</label>
                                <input type="text" name="mobile_number" class="form-control" >
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="row m-0">
                        <div class="col-md-12 pb-2">
                            <button type="submit" class="btn btn-primary px-4 py-2" id="submit_btn">
                                <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="submit_text">@lang('message.form.submit')</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script src="{{ theme_asset('public/js/jquery.validate.min.js') }}" type="text/javascript"></script>
    <script src="{{ theme_asset('public/js/additional-methods.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/dist/js/wallet-address-validator.js') }}" type="text/javascript"></script>

    @include('user_dashboard.layouts.common.check-user-status')

    <script>
        var isActiveMobileMoney = "{{ config('mobilemoney.is_active') }}";
        //Clear validation errors on modal close - starts
        $(document).ready(function() {
            $('#addModal').on('hidden.bs.modal', function (e) {
                $('#payoutSettingForm').validate().resetForm();
                $('#payoutSettingForm').find('.error').removeClass('error');
                $('#payoutSettingForm')[0].reset();
                $('#crypto-address-error').text('');
                $('#currency-error').text('');
            });
        });
        //Clear validation errors on modal close - ends

        $(document).ready(function(){
            $('#bankForm').hide();
            $('#paypalForm').css('display', 'flex');
        });

        $('#type').on('change', function()
        {
            $("#submit_btn").attr("disabled", false);

            if ($('option:selected', this).text() == 'Paypal') {
                $('#paypalForm').css('display', 'flex');
                $('#bankForm').hide();
                $('#CryptoForm').hide();
                if (isActiveMobileMoney) {
                    $('#mobileMoneyForm').hide();
                }
            } else if ($('option:selected', this).text() == 'Bank') {
                $('#bankForm').css('display', 'flex');
                $('#paypalForm').hide();
                $('#CryptoForm').hide();
                if (isActiveMobileMoney) {
                    $('#mobileMoneyForm').hide();
                }
            } else if ($('option:selected', this).text() == 'Crypto') {
                $('#CryptoForm').css('display', 'flex');
                $('#paypalForm').hide();
                $('#bankForm').hide();
                if (isActiveMobileMoney) {
                    $('#mobileMoneyForm').hide();
                }

                var currency = $('option:selected', '#currency').text();
                var cryptoAddress = $('#crypto_address').val();
                if (currency != '' && cryptoAddress != '') {
                    validateCryptoAddress(currency, cryptoAddress);
                }
            } else if (isActiveMobileMoney && $('option:selected', this).text() == 'MobileMoney') {
                $('#mobileMoneyForm').css('display', 'flex');
                $('#bankForm').hide();
                $('#paypalForm').hide();
                $('#CryptoForm').hide();
            }
        });

        function validateCryptoAddress(cryptoCoin, cryptoAddress)
        {
            var test = '';

            if (cryptoCoin.match('TEST')) var test = 'testnet';

            var crypto_coin = cryptoCoin.replace("TEST", "");
            var currency = WAValidator.findCurrency(crypto_coin);

            if (currency != null) {
                if (currency) {
                    var valid = WAValidator.validate(cryptoAddress, currency['name'], test);
                    if (valid) {
                        $('#crypto-address-error').text('');
                        $('#currency-error').text('');
                        $("#submit_btn").attr("disabled", false);
                    } else {
                        $('#currency-error').text('');
                        $('#crypto-address-error').removeClass('d-none').addClass('d-block').text("{{ __('This address is not valid for') }}" + ' ' + cryptoCoin).css('color', 'red');
                        $("#submit_btn").attr("disabled", true);
                    }
                }
            } else {
                $('#currency-error').removeClass('d-none').addClass('d-block').text(cryptoCoin + ' ' + "{{ __('is not a valid crypto currency.') }}").css('color','red');
                $("#submit_btn").attr("disabled", true);
            }
        }

        $(document).on('change', '#currency', function() {
            var currency = $('option:selected', '#currency').text();
            var cryptoAddress = $('#crypto_address').val();

            if (currency != '' && cryptoAddress != '') {
                validateCryptoAddress(currency, cryptoAddress);
            }
        });

        $('#crypto_address').on('input', function() {
            var currency = $('option:selected', '#currency').text();
            var cryptoAddress = $(this).val();
            if (currency != '' && cryptoAddress != '') {
                validateCryptoAddress(currency, cryptoAddress);
            }
        });

        $('#addBtn').on('click', function(e)
        {
            e.preventDefault();

            // if user is suspended
            checkUserSuspended(e);

            // if user is not suspended
            $('#settingId').html('');
            var form = $('#payoutSettingForm');
            form.attr('action', "{{ url('payout/setting/store') }}");
            $.each(form[0].elements, function(index, elem)
            {
                if (elem.name != "_token" && elem.name != "setting_id") {
                    $(this).val("");
                    if (elem.name == "type") {
                        $(this).val(1).change().removeAttr('disabled');
                    }
                }
            });
        });

        jQuery.extend(jQuery.validator.messages,
        {
            required: "{{ __('This field is required.') }}",
        })

        $('#payoutSettingForm').validate(
        {
            rules:
            {
                type: {
                    required: true
                },
                account_name: {
                    required: true
                },
                account_number: {
                    required: true
                },
                swift_code: {
                    required: true
                },
                bank_name: {
                    required: true
                },
                branch_name: {
                    required: true
                },
                branch_city: {
                    required: true
                },
                branch_address: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                },
                country: {
                    required: true
                },
                currency: {
                    required: true
                },
                crypto_address: {
                    required: true
                }
            },
            submitHandler: function(form)
            {
                $("#submit_btn").attr("disabled", true);
                $(".spinner").show();
                $("#submit_text").text("{{ __('Submitting...') }}");
                form.submit();
            }
        });

        $('.edit-setting').on('click', function(e)
        {
            e.preventDefault();
            checkUserSuspended(e);

            //if user is not suspended
            var obj = JSON.parse($(this).attr('data-obj'));
            var settingId = $(this).attr('data-id');
            var form = $('#payoutSettingForm');
            form.attr('action', "{{ url('payout/setting/update') }}");
            form.attr('method', 'post');
            var html = '<input type="hidden" name="setting_id" value="' + settingId + '">';
            $('#settingId').html(html);
            if (obj.type == '{{ Bank }}') {
                $.each(form[0].elements, function(index, elem)
                {
                    switch (elem.name)
                    {
                        case "type":
                            $(this).val(obj.type).change().attr('disabled', 'true');
                            break;
                        case "account_name":
                            $(this).val(obj.account_name);
                            break;
                        case "account_number":
                            $(this).val(obj.account_number);
                            break;
                        case "branch_address":
                            $(this).val(obj.bank_branch_address);
                            break;
                        case "branch_city":
                            $(this).val(obj.bank_branch_city);
                            break;
                        case "branch_name":
                            $(this).val(obj.bank_branch_name);
                            break;
                        case "bank_name":
                            $(this).val(obj.bank_name);
                            break;
                        case "country":
                            $(this).val(obj.country);
                            break;
                        case "swift_code":
                            $(this).val(obj.swift_code);
                            break;
                        default:
                            break;
                    }
                })
            } else if (obj.type == '{{ Paypal }}') {
                $.each(form[0].elements, function(index, elem)
                {
                    if (elem.name == 'email') {
                        $(this).val(obj.email);
                    } else if (elem.name == 'type') {
                        $(this).val(obj.type).change().attr('disabled', 'true');
                    }
                })
            } else if (obj.type == '{{ Crypto }}') {
                $.each(form[0].elements, function(index, elem)
                {
                    switch (elem.name)
                    {
                        case "type":
                            $(this).val(obj.type).change().attr('disabled', 'true');
                            break;
                        case "crypto_address":
                            $(this).val(obj.crypto_address);
                            break;
                        case "currency":
                            $(this).val(obj.currency_id);
                            break;
                        default:
                            break;
                    }
                })
            } else if (isActiveMobileMoney && obj.type == "{{ defined('MobileMoney') ? MobileMoney : '' }}") {
                $.each(form[0].elements, function(index, elem)
                {
                    switch (elem.name)
                    {
                        case "type":
                            $(this).val(obj.type).change().attr('disabled', 'true');
                            break;
                        case "mobilemoney_id":
                            $(this).val(obj.mobilemoney_id);
                            break;
                        case "mobile_number":
                            $(this).val(obj.mobile_number);
                            break;
                        default:
                            break;
                    }
                })
            }

            setTimeout(()=>{
                $('#addModal').modal();
            }, 400)

        });

        $('.delete-setting').on('click', function(e)
        {
            e.preventDefault();
            checkUserSuspended(e);
        });
    </script>
@endsection
