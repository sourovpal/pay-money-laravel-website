@extends('user_dashboard.layouts.app')

@section('content')
<section class="min-vh-100">
    <div class="my-30">
        <div class="container-fluid">
            <!-- Page title start -->
            <div>
                <h3 class="page-title">@lang('message.home.title-bar.dashboard')</h3>
            </div>
            <!-- Page title end-->

            <!--welcome section start-->
            <div class="row bg-secondary m-0 mt-4 shadow rounded">
                <div class="col-md-7 border-right p-4">

                    @php
                        if (!empty($lastTransaction)) {
                            if ($lastTransaction->transaction_type->name == 'Transferred') {
                                $transactionName = 'Money Transfer';
                            } elseif ($lastTransaction->transaction_type->name == 'Received') {
                                $transactionName = 'Money Received';
                            } elseif ($lastTransaction->transaction_type->name == 'Exchange_From' || $lastTransaction->transaction_type->name == 'Exchange_To') {
                                $transactionName = 'Money Exchange';
                            } elseif ($lastTransaction->transaction_type->name == 'Request_From' || $lastTransaction->transaction_type->name == 'Request_To') {
                                $transactionName = 'Request Money';
                            } else {
                                if (str_contains($lastTransaction->transaction_type->name, '_')) {
                                    $transactionName = str_replace('_', ' ', $lastTransaction->transaction_type->name);
                                } else {
                                    $transactionName = $lastTransaction->transaction_type->name;
                                }
                            }
                        }
                    @endphp

                    @if (session('login') == 'success') 
                    <h2>{{  (count(auth()->user()->activity_log()->get()) == 1 || count(auth()->user()->activity_log()->get()) < 1) ? __('Howdy') :  __('Welcome Back') }}, <span class="text-primary">{{ ' ' . auth()->user()->first_name . ' ' . auth()->user()->last_name}}</span></h2>
                    @else 
                        @if (!empty($lastTransaction))
                        <h5>{{ __('Your last transaction was') }} <span class="text-primary">{{ formatNumber($lastTransaction->subtotal, $lastTransaction->currency->id) }} {{ $lastTransaction->currency->code }} </span> <span style="font-size:small;">( {{ $transactionName }} )</span> </h5>
                        @else
                            @if ((count(auth()->user()->activity_log()->get()) == 1 || count(auth()->user()->activity_log()->get()) < 1))
                            <h2>{{__('Howdy') }}, <span class="text-primary">{{ ' ' . auth()->user()->first_name . ' ' . auth()->user()->last_name}}</span></h2>
                            @else
                            <h2><span class="text-primary">{{ ' ' . auth()->user()->first_name . ' ' . auth()->user()->last_name}}</span></h2>
                            @endif
                        @endif
                    @endif
                    <p class="wel-text">{{ __('Thanks for using') }} <span class="text-primary">{{ settings('name') }} </span>{{ __('services') }}</p>
                </div>

                <div class="col-md-5 p-4">
                    <div class="short-list">
                        <a href="{{ url('/profile') }}" class="d-flex align-items-center hover-active" >
                            <i class="fas fa-chevron-right"></i>
                            <p class="my-0 ml-2">{{ __('Manage your profile') }}</p>
                        </a>
                        <hr class="mt-2 mb-0">
                    </div>

                    @if ($user->type == 'merchant')
                        <div class="short-list">
                            <a href="{{ url('merchant/add') }}" class="d-flex align-items-center hover-active" >
                                <i class="fas fa-chevron-right"></i>
                                <p class="my-0 ml-2">{{ __('Create new merchants') }}</p>
                            </a>
                            <hr class="mt-2 mb-0">
                        </div>
                    @endif

                    <div class="short-list">
                        <a href="{{ url('payout/setting') }}" class="d-flex align-items-center hover-active" >
                            <i class="fas fa-chevron-right"></i>
                            <p class="my-0 ml-2">{{ __('Add payout settings') }}</p>
                        </a>
                        <hr class="mt-2 mb-0">
                    </div>

                    <div class="short-list">
                        <a href="{{ url('ticket/add') }}" class="d-flex align-items-center" >
                            <i class="fas fa-chevron-right"></i>
                            <p class="my-0 ml-2">{{ __('Create tickets') }}</p>
                        </a>
                    </div>
                </div>
            </div>
            <!--Welcome end-->

            <div class="row mt-30 mb-30 flex-column-reverse flex-md-row">
                <div class="col-lg-8 mt-4">
                    <!-- Sub title start -->
                    <div>
                        <h3 class="sub-title">{{ __('Latest Transaction') }}</h3>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="bg-secondary mt-3 shadow">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th class="pl-5" scope="col">@lang('message.dashboard.left-table.date')</th>
                                                <th scope="col">@lang('message.dashboard.left-table.description')</th>
                                                <th scope="col">@lang('message.dashboard.left-table.status')</th>
                                                <th class="text-right pr-5" scope="col">@lang('message.dashboard.left-table.amount')</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @if($transactions->count()>0)
                                                    @foreach($transactions as $key=>$transaction)
                                                        <tr  click="0" data-toggle="modal" data-target="#collapseRow{{$key}}" aria-expanded="false" aria-controls="collapseRow{{$key}}" class="show_area cursor-pointer" trans-id="{{$transaction->id}}" id="{{$key}}">
                                                            <td class="pl-5">
                                                                <p class="font-weight-600 text-16 mb-0">{{ $transaction->created_at->format('jS F') }}</p>
                                                                <p class="td-text">{{ $transaction->created_at->format('Y') }}</p>
                                                            </td>

                                                            <!-- Transaction Type -->
                                                            @if(empty($transaction->merchant_id))
                                                                @if(!empty($transaction->end_user_id))
                                                                    <td class="text-left">
                                                                        @if($transaction->transaction_type_id)
                                                                            @if($transaction->transaction_type_id==Request_From)
                                                                                <p class="text-16 mb-0">
                                                                                    {{ $transaction->end_user->first_name.' '.$transaction->end_user->last_name }}
                                                                                </p>
                                                                                <p  class="td-text">@lang('Request Sent')</p>
                                                                            @elseif($transaction->transaction_type_id==Request_To)
                                                                                <p class="text-16 mb-0">
                                                                                    {{ $transaction->end_user->first_name.' '.$transaction->end_user->last_name }}
                                                                                <p  class="td-text">@lang('Request Received')</p>

                                                                            @elseif($transaction->transaction_type_id == Transferred)
                                                                                <p class="text-16 mb-0">
                                                                                    {{ $transaction->end_user->first_name.' '.$transaction->end_user->last_name }}
                                                                                </p>
                                                                                <p  class="td-text">@lang('Transferred')</p>

                                                                            @elseif($transaction->transaction_type_id == Received)
                                                                                <p class="text-16 mb-0">
                                                                                    {{ $transaction->end_user->first_name.' '.$transaction->end_user->last_name }}
                                                                                </p>
                                                                                <p  class="td-text">@lang('Received')</p>
                                                                            @else
                                                                                <p>{{ __(str_replace('_',' ',$transaction->transaction_type->name)) }}</p>
                                                                            @endif
                                                                        @endif
                                                                    </td>
                                                                @else
                                                                    <?php
                                                                        if (isset($transaction->payment_method->name))
                                                                        {
                                                                            if ($transaction->payment_method->name == 'Mts')
                                                                            {
                                                                                $payment_method = settings('name');
                                                                            }
                                                                            else
                                                                            {
                                                                                $payment_method = $transaction->payment_method->name;
                                                                            }
                                                                        }
                                                                    ?>
                                                                    <td class="text-left">
                                                                        <p class="text-16 mb-0">
                                                                            @if($transaction->transaction_type->name == 'Deposit')
                                                                                @if ($transaction->payment_method->name == 'Bank')
                                                                                {{ $transaction->transaction_type->name . ' ' . 'via' . ' ' . $payment_method . ' ' .  $transaction->bank->bank_name }}
                                                                                @else
                                                                                    @if(!empty($payment_method))
                                                                                    {{ $transaction->transaction_type->name . ' ' . 'via' . ' ' . $payment_method }}
                                                                                    @endif
                                                                                @endif

                                                                            @elseif($transaction->transaction_type->name == 'Exchange_To' || $transaction->transaction_type->name == 'Exchange_From')
                                                                                {{ __(str_replace('_',' ',$transaction->transaction_type->name)) .' ' . $transaction->currency->code }}

                                                                            @elseif($transaction->transaction_type->name == 'Withdrawal')
                                                                                @if(!empty($payment_method))
                                                                                {{ __('Payout via') }} {{ $payment_method }}
                                                                                @endif


                                                                            @elseif($transaction->transaction_type->name == 'Transferred' && $transaction->user_type = 'unregistered')
                                                                                {{ ($transaction->email) ? $transaction->email : $transaction->phone }} <!--for send money by phone - mobile app-->
                                                                            @elseif($transaction->transaction_type->name == 'Request_From' && $transaction->user_type = 'unregistered')
                                                                                {{ ($transaction->email) ? $transaction->email : $transaction->phone }} <!--for send money by phone - mobile app-->
                                                                            @endif
                                                                        </p>

                                                                        @if($transaction->transaction_type_id)
                                                                            <p class="td-text">
                                                                                @if($transaction->transaction_type_id==Request_From)
                                                                                    @lang('Request Sent')
                                                                                @elseif($transaction->transaction_type_id==Request_To)
                                                                                    @lang('Request Received')

                                                                                @elseif($transaction->transaction_type_id == Withdrawal)
                                                                                    @lang('Payout')
                                                                                @else
                                                                                    <p>{{ __(str_replace('_',' ',$transaction->transaction_type->name)) }}</p>
                                                                                @endif
                                                                            </p>
                                                                        @endif
                                                                    </td>
                                                                @endif
                                                            @else
                                                                <td>
                                                                    <p class="text-16 mb-0">{{ $transaction->merchant->business_name }}</p>
                                                                    @if($transaction->transaction_type_id)
                                                                        <p>{{ __(str_replace('_',' ',$transaction->transaction_type->name)) }}</p>
                                                                    @endif
                                                                </td>
                                                            @endif

                                                            <!--Status -->
                                                            <td>
                                                                <span id="status_{{$transaction->id}}" class="badge {{ $transaction->status }}">
                                                                    {{
                                                                        (
                                                                            ($transaction->status == 'Blocked') ? __("Cancelled") :
                                                                            (
                                                                                ($transaction->status == 'Refund') ? __("Refunded") : __($transaction->status)
                                                                            )
                                                                        )
                                                                    }}
                                                                </span>
                                                            </td>

                                                            <!-- Amount -->
                                                            @if($transaction->transaction_type_id == Deposit)
                                                                @if($transaction->subtotal > 0)
                                                                    <td class="text-right pr-5">
                                                                        <p>
                                                                            <span class="text-16 font-weight-600"> +{{ formatNumber($transaction->subtotal, $transaction->currency->id) }}</span> 
                                                                            <span class="c-code">({{ $transaction->currency->code }})</span>
                                                                        </p>
                                                                    </td>
                                                                @endif
                                                            @elseif($transaction->transaction_type_id == Withdrawal)
                                                                <td class="text-right pr-5">
                                                                    <p>
                                                                        <span class="text-16 font-weight-600"> -{{ formatNumber($transaction->subtotal, $transaction->currency->id) }}</span> 
                                                                        <span class="c-code"> ({{ $transaction->currency->code }}) </span>
                                                                    </p>
                                                                </td>
                                                            @elseif($transaction->transaction_type_id == Payment_Received)
                                                                @if($transaction->subtotal > 0)
                                                                    @if($transaction->status == 'Refund')
                                                                        <td class="text-right pr-5">
                                                                            <p><span class="text-16 font-weight-600">-{{ formatNumber($transaction->subtotal, $transaction->currency->id) }}</span> <span class="c-code">({{ $transaction->currency->code }})</span></p>
                                                                            <p>{{ $transaction->currency->code }}</p>
                                                                        </td>
                                                                    @else
                                                                        <td class="text-right pr-5">
                                                                            <p><span class="text-16 font-weight-600">+{{ formatNumber($transaction->subtotal, $transaction->currency->id) }}</span> <span class="c-code">({{ $transaction->currency->code }})</span></p>
                                                                        </td>
                                                                    @endif
                                                                @elseif($transaction->subtotal == 0)
                                                                    <td>
                                                                        <p><span class="text-16 font-weight-600"> {{ formatNumber($transaction->subtotal, $transaction->currency->id) }} </span><span class="c-code">({{ $transaction->currency->code }})</span></p>
                                                                    </td>
                                                                @elseif($transaction->subtotal < 0)
                                                                    <td class="text-right pr-5">
                                                                        <p><span class="text-16 font-weight-600"> {{ formatNumber($transaction->subtotal, $transaction->currency->id) }} </span> <span class="c-code">({{ $transaction->currency->code }})</span></p>
                                                                    </td>
                                                                @endif
                                                            @else
                                                                @if($transaction->total > 0)
                                                                    <td class="text-right pr-5">
                                                                        <p> <span class="text-16 font-weight-600"> {{ "+".formatNumber($transaction->total, $transaction->currency->id) }} </span> <span class="c-code">({{ $transaction->currency->code }})</span></p>
                                                                    </td>
                                                                @elseif($transaction->total == 0)
                                                                    <td class="text-right pr-5">
                                                                        <p><span class="text-16 font-weight-600"> {{ formatNumber($transaction->total, $transaction->currency->id) }} </span><span class="c-code">({{ $transaction->currency->code }})</span></p>
                                                                    </td>
                                                                @elseif($transaction->total < 0)
                                                                    <td class="text-right pr-5">
                                                                        <p><span class="text-16 font-weight-600">{{ formatNumber($transaction->total, $transaction->currency->id) }} </span> <span class="c-code">({{ $transaction->currency->code }})</span></p>
                                                                    </td>
                                                                @endif
                                                            @endif
                                                        </tr>

                                                            <!-- Modal -->
                                                            <div class="modal fade-scale" id="collapseRow{{$key}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-body p-0">
                                                                            <button type="button" class="close text-28 pr-4 mt-2" data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>

                                                                            <div class="row activity-details" id="loader_{{$transaction->id}}"
                                                                                style="min-height: 400px">
                                                                                <div class="col-md-5 bg-primary">
                                                                                        <div id="total_{{$key}}" class="p-center mt-5">

                                                                                        </div>
                                                                                </div>
                                                                                <div class="col-md-7 col-sm-12 text-left p-0">
                                                                                        <div class="preloader transaction-loader" style="display: none;">
                                                                                            <div class="loader"></div>
                                                                                        </div>

                                                                                        <div class="modal-header">
                                                                                            <h3 class="modal-title text-18 font-weight-600" id="exampleModalLabel">{{ __('Transaction details') }}</h3>
                                                                                        </div>

                                                                                        <div id="html_{{$key}}" class="px-4 mt-4">

                                                                                        </div>
                                                                                        <div class="col-md-12 col-sm-12 mt-5">
                                                                                            <div class="text-center mb-2">
                                                                                                @if( $transaction->transaction_type_id == Payment_Sent && $transaction->status == 'Success' && !isset($transaction->dispute->id))
                                                                                                    <a id="dispute_{{$transaction->id}}" href="{{url('/dispute/add/').'/'.$transaction->id}}" class="btn btn-primary btn-sm">@lang('message.dashboard.transaction.open-dispute')</a>
                                                                                                @endif
                                                                                            </div>
                                                                                        </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                    @endforeach
                                                @else
                                                <tr>
                                                    <td colspan="6" class="text-center p-4">
                                                        <img src="{{ theme_asset('public/images/banner/notfound.svg') }}" alt="notfound">
                                                        <p class="mt-4">{{ __('Sorry!') }} @lang('message.dashboard.left-table.no-transaction')</p>
                                                    </td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mt-4">
                    <div>
                        <h3 class="sub-title">{{ __('Wallet') }}</h3>
                    </div>
                    <div class="row">

                        @if($wallets->count()>0)
                            @foreach($wallets as $wallet)
                                <div class="col-md-6 mt-3">
                                    <div class="shadow rounded bg-secondary p-4 ">
                                        <div class="d-flex align-items-center" >
                                            <div class="w-100">
                                                <h4 class="text-18 font-weight-600">
                                                    <span>{{ '+'.formatNumber($wallet->balance, $wallet->currency->id) }}</span>
                                                </h4>
                                                <p class="side-text my-0 ml-2">
                                                    @if($wallet->currency->type == 'fiat' && $wallet->is_default == 'Yes')
                                                        <span>{{ $wallet->currency->code }}&nbsp;<span class="badge badge-secondary">@lang('message.dashboard.right-table.default-wallet-label')</span></span>
                                                    @else
                                                        <span>{{ $wallet->currency->code }}</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')

<!-- sweetalert -->
<script src="{{theme_asset('public/js/sweetalert/sweetalert-unpkg.min.js')}}" type="text/javascript"></script>

@include('user_dashboard.layouts.common.check-user-status')

@include('common.user-transactions-scripts')

@endsection
