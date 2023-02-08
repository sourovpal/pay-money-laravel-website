@extends('admin.layouts.master')
@section('title', __('Edit Transfer'))

@section('page_content')
    <div class="box box-default">
        <div class="box-body">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="top-bar-title padding-bottom pull-left">{{ __('Transfer Details') }}</div>
                </div>

                <div>
                    @if ($transfer->status)
                        <p class="text-left f-18">{{ __('Status') }} : @if ($transfer->status == 'Success')
                                <span class="text-green">{{ __('Success') }}</span>@endif
                            @if ($transfer->status == 'Pending')<span
                                    class="text-blue">{{ __('Pending') }}</span>@endif
                            @if ($transfer->status == 'Refund')<span
                                    class="text-orange">{{ __('Refunded') }}</span>@endif
                            @if ($transfer->status == 'Blocked')<span
                                    class="text-red">{{ __('canceled') }}</span>@endif
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <section class="min-vh-100">
        <div class="my-30">
            <form action="{{ url(\Config::get('adminPrefix') . '/transfers/update') }}" class="form-horizontal row"
                id="transfers_form" method="POST">
                {{ csrf_field() }}
                <!-- Page title start -->
                <div class="col-md-8">
                    <div class="box">
                        <div>
                            <div class="panel">
                                <div>
                                    <div class="p-4">
                                        <input type="hidden" value="{{ $transfer->id }}" name="id" id="id">
                                        <input type="hidden" value="{{ $transfer->uuid }}" name="uuid" id="uuid">
                                        <input type="hidden" value="{{ $transfer->sender_id }}" name="sender_id" id="sender_id">
                                        <input type="hidden" value="{{ $transfer->receiver_id }}" name="receiver_id" id="receiver_id">
                                        <input type="hidden" value="{{ $transfer->currency->id }}" name="currency_id" id="currency_id">
                                        <input type="hidden" value="{{ $transfer->note }}" name="note" id="note">
                                        <input type="hidden" value="{{ $transfer->email }}" name="email" id="email">
                                        <input type="hidden" value="{{ $transfer->phone }}" name="phone" id="phone">
                                        <input type="hidden" value="{{ $transaction->transaction_type_id }}" name="transaction_type_id" id="transaction_type_id">
                                        <input type="hidden" value="{{ $transaction->transaction_type->name }}" name="transaction_type" id="transaction_type">
                                        <input type="hidden" value="{{ $transaction->status }}" name="transaction_status" id="transaction_status">
                                        <input type="hidden" value="{{ $transaction->transaction_reference_id }}" name="transaction_reference_id" id="transaction_reference_id">
                                        <input type="hidden" value="{{ $transaction->percentage }}" name="percentage" id="percentage">
                                        <input type="hidden" value="{{ $transaction->charge_percentage }}" name="charge_percentage" id="charge_percentage">
                                        <input type="hidden" value="{{ $transaction->charge_fixed }}" name="charge_fixed" id="charge_fixed">

                                        <div class="panel panel-default">
                                            <div class="panel-body">

                                                <div class="form-group row">
                                                    @if (!empty($transfer->bank))
                                                        <label class="control-label f-14 fw-bold text-end col-sm-3" for="sender">{{ __('Transferred By') }}</label>
                                                    @else
                                                        <label class="control-label f-14 fw-bold text-end col-sm-3" for="sender">{{ __('Paid By') }}</label>
                                                    @endif
                                                    <input type="hidden" class="form-control f-14" name="sender"
                                                        value="{{ isset($transfer->sender) ? $transfer->sender->first_name . ' ' . $transfer->sender->last_name : '-' }}">
                                                    <div class="col-sm-9">
                                                        <p class="form-control-static f-14">
                                                            {{ isset($transfer->sender) ? $transfer->sender->first_name . ' ' . $transfer->sender->last_name : '-' }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="control-label f-14 fw-bold text-end col-sm-3" for="receiver">{{ __('Paid To') }}</label>
                                                    @if ($transfer->receiver)
                                                        <input type="hidden" class="form-control f-14" name="receiver" value="{{ isset($transfer->receiver) ? $transfer->receiver->first_name . ' ' . $transfer->receiver->last_name : '-' }}">
                                                        <div class="col-sm-9">
                                                            <p class="form-control-static f-14">
                                                                {{ isset($transfer->receiver) ? $transfer->receiver->first_name . ' ' . $transfer->receiver->last_name : '-' }}
                                                            </p>
                                                        </div>
                                                    @else
                                                        <input type="hidden" class="form-control f-14" name="receiver"
                                                            value="{{ isset($transfer->email) ? $transfer->email : $transfer->phone }}">
                                                        <div class="col-sm-9">
                                                            <p class="form-control-static f-14">
                                                                {{ isset($transfer->email) ? $transfer->email : $transfer->phone }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>

                                                @if ($transfer->uuid)
                                                    <div class="form-group row">
                                                        <label class="control-label f-14 fw-bold text-end col-sm-3"
                                                            for="transfer_uuid">{{ __('Transaction ID') }}</label>
                                                        <input type="hidden" class="form-control f-14" name="transfer_uuid"
                                                            value="{{ $transfer->uuid }}">
                                                        <div class="col-sm-9">
                                                            <p class="form-control-static f-14">{{ $transfer->uuid }}</p>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if ($transfer->currency)
                                                    <div class="form-group row">
                                                        <label class="control-label f-14 fw-bold text-end col-sm-3"
                                                            for="currency">{{ __('Currency') }}</label>
                                                        <input type="hidden" class="form-control f-14" name="currency"
                                                            value="{{ $transfer->currency->code }}">
                                                        <div class="col-sm-9">
                                                            <p class="form-control-static f-14">
                                                                {{ $transfer->currency->code }}</p>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if ($transfer->created_at)
                                                    <div class="form-group row">
                                                        <label class="control-label f-14 fw-bold text-end col-sm-3" for="created_at">{{ __('Date') }}</label>
                                                        <input type="hidden" class="form-control f-14" name="created_at" value="{{ $transfer->created_at }}">
                                                        <div class="col-sm-9">
                                                            <p class="form-control-static f-14">{{ dateFormat($transfer->created_at) }}</p>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if ($transfer->status)
                                                    <div class="form-group row align-items-center">
                                                        <label class="control-label f-14 fw-bold text-end col-sm-3" for="status">{{ __('Change Status') }}</label>
                                                        <div class="col-sm-6">
                                                            @if (isset($transactionOfRefunded) && isset($transferOfRefunded))
                                                                <p class="form-control-static f-14"><span class="label label-success">{{ __('Already Refunded') }}</span></p>

                                                                <p class="form-control-static f-14"><span
                                                                        class="label label-danger">{{ __('Refunded Reference') }}:
                                                                        <i>
                                                                            <a id="transferOfRefunded"
                                                                                href="{{ url(\Config::get('adminPrefix') . "/transfers/edit/$transferOfRefunded->id") }}">(
                                                                                {{ $transactionOfRefunded->refund_reference }}
                                                                                )
                                                                            </a>
                                                                        </i>
                                                                    </span>
                                                                </p>
                                                            @else
                                                                <select class="form-control select2 w-60" name="status">
                                                                    @if ($transfer->status == 'Success')
                                                                        <option value="Success" {{ $transfer->status == 'Success' ? 'selected' : '' }}> {{ __('Success') }}</option>
                                                                        <option value="Pending" {{ $transfer->status == 'Pending' ? 'selected' : '' }}> {{ __('Pending') }}</option>
                                                                        <option value="Refund" {{ $transfer->status == 'Refund' ? 'selected' : '' }}>{{ __('Refunded') }}</option>
                                                                        <option value="Blocked" {{ $transfer->status == 'Blocked' ? 'selected' : '' }}> {{ __('Cancel') }}</option>
                                                                    @else
                                                                        <option value="Success" {{ $transfer->status == 'Success' ? 'selected' : '' }}>{{ __('Success') }}</option>
                                                                        <option value="Pending"{{ $transfer->status == 'Pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                                                        <option value="Blocked"{{ $transfer->status == 'Blocked' ? 'selected' : '' }}>{{ __('Cancel') }}</option>
                                                                    @endif
                                                                </select>
                                                            @endif

                                                        </div>
                                                    </div>
                                                @endif

                                            </div>
                                        </div>


                                        <div class="row">
                                            @if (!isset($transactionOfRefunded->refund_reference))
                                                <div class="col-md-6 offset-md-3">
                                                    <a id="cancel_anchor" class="btn btn-theme-danger me-1 f-14" href="{{ url(\Config::get('adminPrefix') . '/transfers') }}">{{ __('Cancel') }}</a>
                                                    <button type="submit" class="btn btn-theme f-14"
                                                        id="transfers_edit">
                                                        <i class="fa fa-spinner fa-spin d-none"></i> <span id="transfers_edit_text">{{ __('Update') }}</span>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="box">
                        <div>
                            <div class="panel">
                                <div>
                                    <div class="p-4">
                                        <div class="panel panel-default">
                                            <div class="panel-body">
                                                @if ($transfer->amount)
                                                    <div class="form-group row">
                                                        <label class="control-label f-14 fw-bold text-end col-sm-6"
                                                            for="amount">{{ __('Amount') }}</label>
                                                        <input type="hidden" class="form-control f-14" name="amount"
                                                            value="{{ $transfer->amount }}">
                                                        <div class="col-sm-6">
                                                            <p class="form-control-static f-14">
                                                                {{ moneyFormat(optional($transfer->currency)->symbol, formatNumber($transfer->amount, $transfer->currency->id)) }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="form-group row total-deposit-feesTotal-space">
                                                    <label class="control-label f-14 fw-bold text-end col-sm-6 d-flex justify-content-end" for="feesTotal">{{ __('Fees') }}
                                                        <span>
                                                            <small class="transactions-edit-fee">
                                                                @if (isset($transaction))
                                                                    ({{ formatNumber($transaction->percentage, $transfer->currency->id) }}% +
                                                                    {{ formatNumber($transaction->charge_fixed, $transfer->currency->id) }})
                                                                @else
                                                                    ({{ 0 }}%+{{ 0 }})
                                                                @endif
                                                            </small>
                                                        </span>
                                                    </label>
                                                    <input type="hidden" class="form-control f-14" name="feesTotal" value="{{ $transfer->fee }}">

                                                    <div class="col-sm-6">
                                                        <p class="form-control-static f-14">
                                                            {{ moneyFormat(optional($transfer->currency)->symbol, formatNumber($transfer->fee, $transfer->currency->id)) }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <hr class="increase-hr-height">

                                                @php
                                                    $total = $transfer->fee + $transfer->amount;
                                                @endphp

                                                @if (isset($total))
                                                    <div class="form-group row total-deposit-space">
                                                        <label class="control-label f-14 fw-bold text-end col-sm-6" for="total">{{ __('Total') }}</label>
                                                        <input type="hidden" class="form-control f-14" name="total"
                                                            value="{{ $total }}">
                                                        <div class="col-sm-6">
                                                            <p class="form-control-static f-14">
                                                                {{ moneyFormat(optional($transfer->currency)->symbol, formatNumber($total, $transfer->currency->id)) }}
                                                            </p>
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
                </div>
            </form>
        </div>
    </section>
@endsection

@push('extra_body_scripts')
    <script type="text/javascript">
        $(".select2").select2({});

        // disabling submit and cancel button after clicking it
        $(document).ready(function() {
            $('form').submit(function() {
                $("#transfers_edit").attr("disabled", true);
                $('#cancel_anchor').attr("disabled", "disabled");
                $(".fa-spin").removeClass("d-none");
                $("#transfers_edit_text").text('Updating...');

                // Click False
                $('#transfers_edit').click(false);
                $('#cancel_anchor').click(false);
            });

            $('#transferOfRefunded').css('color', 'white');
        });
    </script>
@endpush
