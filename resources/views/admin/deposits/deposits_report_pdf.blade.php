<!DOCTYPE html>
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
        <title>
            {{ __('Deposits') }}
        </title>
    </head>
    <style>
        body {
        font-family: "DeJaVu Sans", Helvetica, sans-serif;
        color: #121212;
        line-height: 15px;
    }

    table, tr, td {
        padding: 6px 6px;
        border: 1px solid black;
    }

    tr {
        height: 40px;
    }

    </style>

    <body>
        <div style="width:100%; margin:0px auto;">
            <div style="height:80px">
                <div style="width:80%; float:left; font-size:13px; color:#383838; font-weight:400;">
                    <div>
                        <strong>
                            {{ ucwords(settings('name')) }}
                        </strong>
                    </div>
                    <br>
                    <div>
                        {{ __('Period') }} : {{ $date_range }}
                    </div>
                    <br>
                    <div>
                        {{ __('Print Date') }} : {{ dateFormat(now())}}
                    </div>
                </div>
                <div style="width:20%; float:left;font-size:15px; color:#383838; font-weight:400;">
                    <div>
                        <div>
                            {!! getSystemLogo() !!}
                        </div>
                    </div>
                </div>
            </div>
            <div style="clear:both">
            </div>
            <div style="margin-top:30px;">
                <table style="width:100%; border-radius:1px;  border-collapse: collapse;">

                    <tr style="background-color:#f0f0f0;text-align:center; font-size:12px; font-weight:bold;">

                        <td>{{ __('Date') }}</td>
                        <td>{{ __('User') }}</td>
                        <td>{{ __('Amount') }}</td>
                        <td>{{ __('Fees') }}</td>
                        <td>{{ __('Total') }}</td>
                        <td>{{ __('Currency') }}</td>
                        <td>{{ __('Payment Method') }}</td>
                        <td>{{ __('Status') }}</td>
                    </tr>

                    @foreach($deposits as $deposit)

                    <tr style="background-color:#fff; text-align:center; font-size:12px; font-weight:normal;">

                        <td>{{ dateFormat($deposit->created_at) }}</td>

                        <td>{{ isset($deposit->user) ? $deposit->user->first_name.' '.$deposit->user->last_name :"-" }}</td>

                        <td>{{ formatNumber($deposit->amount, $deposit->currency->id) }}</td>

                        <td>{{ ($deposit->charge_percentage == 0) && ($deposit->charge_fixed == 0) ? '-' : formatNumber($deposit->charge_percentage + $deposit->charge_fixed, $deposit->currency->id) }}</td>

                        <td>{{ '+'.formatNumber($deposit->amount + ($deposit->charge_percentage + $deposit->charge_fixed), $deposit->currency->id) }}</td>

                        <td>{{ $deposit->currency->code }}</td>

                        <td>{{ ($deposit->payment_method->name == "Mts") ? settings('name') : $deposit->payment_method->name }}</td>

                        <td>{{ ($deposit->status == 'Blocked') ? 'Cancelled' : $deposit->status }}</td>

                    </tr>
                    @endforeach

                </table>
            </div>
        </div>
    </body>
</html>
