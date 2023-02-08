@extends('user_dashboard.layouts.app')

@section('css')
    <style>
        @media only screen and (max-width: 259px) {
            .chart-list ul li.active a {
                padding-bottom: 0px !important;
            }
        }
    </style>
@endsection

@section('content')
<section class="min-vh-100">
    <div class="my-30">
        <div class="container-fluid">
            <!-- Page title start -->
            <div class="d-flex justify-content-between">
                <div>
                    <h3 class="page-title">{{ __('Merchants') }}</h3>
                </div>

                <div>
                    <a href="{{url('/merchant/add')}}" class="btn btn-primary px-4 py-2 ticket-btn"><i class="fa fa-user"></i>&nbsp;
                        @lang('message.dashboard.button.new-merchant')</a>
                </div>
            </div>
            <!-- Page title end-->

            <div class="mt-4 border-bottom">
                <div class="d-flex flex-wrap">
                    <a href="{{ url('/merchants') }}">
                        <div class="mr-4 pb-3">
                            <p class="text-16 font-weight-600 text-gray-500">@lang('message.dashboard.merchant.menu.merchant')</p>
                        </div>
                    </a>

                    <a href="{{ url('/merchant/payments') }}">
                        <div class="mr-4 border-bottom-active pb-3">
                            <p class="text-16 font-weight-600 text-active">@lang('message.dashboard.merchant.menu.payment')</p>
                        </div>
                    </a>

                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="bg-secondary rounded mt-3 shadow">
                                @include('user_dashboard.layouts.common.alert')

                                <div class="table-responsive">
                                    @if($merchant_payments->count() > 0)
                                        <table class="table recent_activity" id="merchant">
                                            <thead>
                                            <tr>
                                                <td><strong>@lang('message.dashboard.merchant.payment.created-at')</strong>
                                                </td>
                                                <td><strong>@lang('message.dashboard.merchant.payment.merchant')</strong>
                                                </td>
                                                <td><strong>@lang('message.dashboard.merchant.payment.method')</strong></td>
                                                <td><strong>@lang('message.dashboard.merchant.payment.order-no')</strong>
                                                </td>
                                                <td><strong>@lang('message.dashboard.merchant.payment.amount')</strong></td>
                                                <td><strong>@lang('message.dashboard.merchant.payment.fee')</strong></td>
                                                <td><strong>@lang('message.dashboard.merchant.payment.total')</strong></td>
                                                <td><strong>@lang('message.dashboard.merchant.payment.currency')</strong>
                                                </td>
                                                <td><strong>@lang('message.dashboard.merchant.payment.status')</strong></td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($merchant_payments as $result)
                                                <tr>
                                                    <td>{{ dateFormat($result->created_at) }}</td>
                                                    <td>{{ $result->merchant->business_name }}</td>

                                                    <td>{{ ($result->payment_method->name == "Mts") ? settings('name') : $result->payment_method->name }}</td>

                                                    <td>{{ !empty($result->order_no) ? $result->order_no : "-" }}</td>

                                                    <td>{{ formatNumber($result->amount)}}</td>

                                                    <td>{{ (($result->charge_percentage + $result->charge_fixed) == 0) ? '-' : formatNumber($result->charge_percentage + $result->charge_fixed) }}</td>

                                                    <td>{{ formatNumber($result->total) }}</td>

                                                    <td>{{ $result->currency->code}}</td>

                                                    @if($result->status == 'Pending')
                                                        <td>
                                                            <span class="badge badge-primary">@lang('message.dashboard.merchant.payment.pending')</span>
                                                        </td>
                                                    @elseif($result->status == 'Success')
                                                        <td>
                                                            <span class="badge badge-success">@lang('message.dashboard.merchant.payment.success')</span>
                                                        </td>
                                                    @elseif($result->status == 'Blocked')
                                                        <td>
                                                            <span class="badge badge-danger">@lang('message.dashboard.merchant.payment.block')</span>
                                                        </td>
                                                    @elseif($result->status == 'Refund')
                                                        <td>
                                                            <span class="badge badge-warning">@lang('message.dashboard.transaction.refund')</span>
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="p-5 text-center">
                                            <img src="{{ theme_asset('public/images/banner/notfound.svg') }}" alt="notfound">
                                            <p class="mt-4">{{ __('Sorry!') }}  @lang('message.dashboard.merchant.table.not-found')</p>
                                        </div>
                                    @endif
                                </div>

                            </div>

                            <div class="mt-4">
                                {{ $merchant_payments->links('vendor.pagination.bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
