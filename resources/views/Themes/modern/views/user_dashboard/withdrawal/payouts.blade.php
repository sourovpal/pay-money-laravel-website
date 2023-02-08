@extends('user_dashboard.layouts.app')
@section('css')
    <!-- sweetalert -->
    <link rel="stylesheet" type="text/css" href="{{theme_asset('public/css/sweetalert.css')}}">
@endsection
@section('content')
<section class="min-vh-100">
    <div class="my-30">
        <div class="container-fluid">
            <!-- Page title start -->
            <div class="d-flex justify-content-between">
                <div>
                    <h3 class="page-title">{{ __('Withdrawals') }}</h3>
                </div>

                <div>
                    <a href="{{ url('/payout') }}">
                        <button class="btn btn-primary px-4 py-2"><i class="fa fa-arrow-up"></i> {{ __('Withdraw') }}</button>
                    </a>
                </div>
            </div>
            <!-- Page title end-->

            <div class="mt-4 border-bottom">
                <div class="d-flex flex-wrap">
                    <a href="{{ url('/payouts') }}">
                        <div class="mr-4 border-bottom-active pb-3">
                            <p class="text-16 font-weight-600 text-active">{{ __('Payout list') }}</p>
                        </div>
                    </a>

                    <a href="{{ url('/payout/setting') }}">
                        <div class="mr-4">
                            <p class="text-16 font-weight-400 text-gray-500">{{ __('Payout settings') }}  </p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            @include('user_dashboard.layouts.common.alert')
                            <div class="bg-secondary mt-3 shadow">
                                <div class="table-responsive">
                                    @if($payouts->count() > 0)
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th class="pl-5">
                                                        @lang('message.dashboard.payout.list.date')
                                                    </th>

                                                    <th>
                                                        @lang('message.dashboard.payout.list.method')
                                                    </th>


                                                    <th>
                                                        @lang('message.dashboard.payout.list.method-info')
                                                    </th>

                                                    <th>
                                                        @lang('message.dashboard.payout.list.fee')
                                                    </th>

                                                    <th class="text-center">
                                                        @lang('message.dashboard.payout.list.amount')
                                                    </th>

                                                    <th class="text-center">
                                                        @lang('message.dashboard.payout.list.currency')
                                                    </th>

                                                    <th class="text-right pr-5">
                                                        @lang('message.dashboard.payout.list.status')
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($payouts as $payout)
                                                <tr>
                                                    <td class="pl-5">
                                                        <p class="font-weight-600 text-16 mb-0">{{ $payout->created_at->format('jS F') }}</p>
                                                        <p class="td-text">{{ $payout->created_at->format('Y') }}</p>
                                                    </td>
                                                    <td>
                                                        <p>{{ ($payout->payment_method->name == "Mts") ? settings('name') : $payout->payment_method->name }}</p>
                                                    </td>
                                                    <td>
                                                        @if($payout->payment_method->name == "Bank")
                                                            @if ($payout->withdrawal_detail)
                                                                {{$payout->withdrawal_detail->account_name}} (*****{{substr($payout->withdrawal_detail->account_number,-4)}}
                                                                )<br/>
                                                                {{$payout->withdrawal_detail->bank_name}}
                                                            @else
                                                                {{ '-' }}
                                                            @endif
                                                        @elseif($payout->payment_method->name == "Mts")
                                                            {{ '-' }}
                                                        @else
                                                            {{ $payout->payment_method_info }}
                                                        @endif
                                                    </td>

                                                    @php
                                                        $payoutFee = ($payout->amount-$payout->subtotal);
                                                    @endphp

                                                    <td>{{ ($payoutFee == 0) ? '-' : formatNumber($payoutFee) }}</td>
                                                    <td class="text-center">
                                                        <p class="font-weight-600">{{ formatNumber($payout->amount, $payout->currency_id) }}</p>
                                                    </td>

                                                    <td class="text-center">
                                                        {{ $payout->currency->code }}
                                                    </td>

                                                    <td class="text-right pr-5">
                                                        @php
                                                            if ($payout->status == 'Success') {
                                                                echo '<span class="badge badge-success">'.$payout->status.'</span>';
                                                            }
                                                            elseif ($payout->status == 'Pending') {
                                                                echo '<span class="badge badge-primary">'.$payout->status.'</span>';
                                                            }
                                                            elseif ($payout->status == 'Blocked') {
                                                                echo '<span class="badge badge-danger">Cancelled</span>';
                                                            }
                                                        @endphp
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                    @else
                                        <div class="p-5 text-center">
                                            <img src="{{ theme_asset('public/images/banner/notfound.svg') }}" alt="notfound">
                                            <p class="mt-4">{{ __('Sorry!') }}  @lang('message.dashboard.payout.list.not-found')</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-4">
                                {{ $payouts->links('vendor.pagination.bootstrap-4') }}
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
<script src="{{theme_asset('public/js/sweetalert.min.js')}}" type="text/javascript"></script>
<script>
    $(document).ready(function()
    {
        var payoutSetting = {!! count($payoutSettings) !!}
        $( ".ticket-btn" ).click(function()
        {
            if ( payoutSetting <= 0 )
            {
                swal({
                        title: "{{ __("Error") }}!",
                        text: "{{ __("No Payout Setting Exists!") }}",
                        type: "error"
                    }
                );
                event.preventDefault();
            }
        });
    });
</script>
@endsection
