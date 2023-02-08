<!DOCTYPE html>
<html>
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
        <title>
            {{ __('Revenues') }}
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
                        <td>{{ __('Transaction Type') }}</td>
                        <td>{{ __('Percentage Charge') }}</td>
                        <td>{{ __('Fixed Charge') }}</td>
                        <td>{{ __('Total') }}</td>
                        <td>{{ __('Currency') }}</td>

                    </tr>

                    @foreach($revenues as $revenue)

                    <tr style="background-color:#fff; text-align:center; font-size:12px; font-weight:normal;">

                        <td>{{ dateFormat($revenue->created_at) }}</td>

                        <td>{{ ($revenue->transaction_type->name == "Withdrawal") ? "Payout" : str_replace('_', ' ', $revenue->transaction_type->name) }}</td>

                        <td>{{ ($revenue->charge_percentage == 0) ?  '-' : formatNumber($revenue->charge_percentage) }}</td>

                        <td>{{ ($revenue->charge_fixed == 0) ?  '-' : formatNumber($revenue->charge_fixed) }}</td>

                        @php
                            $total = ($revenue->charge_percentage == 0) && ($revenue->charge_fixed == 0) ? '-' : formatNumber($revenue->charge_percentage + $revenue->charge_fixed);
                        @endphp

                        <td>{{ '+'.$total }}</td>

                        <td>{{ $revenue->currency->code }}</td>

                    </tr>
                    @endforeach

                </table>
            </div>
        </div>
    </body>
</html>
