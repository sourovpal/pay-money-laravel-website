<!DOCTYPE html>
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
        <title>
            {{ __('Request Payments') }}
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
                        <td>{{ __('Requested Amount') }}</td>
                        <td>{{ __('Accepted Amount') }}</td>
                        <td>{{ __('Currency') }}</td>
                        <td>{{ __('Receiver') }}</td>
                        <td>{{ __('Status') }}</td>
                    </tr>

                    @foreach($requestpayments as $requestpayment)

                        <tr style="background-color:#fff; text-align:center; font-size:12px; font-weight:normal;">
                            <td>{{ dateFormat($requestpayment->created_at) }}</td>

                            <td>{{ isset($requestpayment->user) ? $requestpayment->user->first_name.' '.$requestpayment->user->last_name : "-" }}</td>

                            <td>{{ '+'.formatNumber($requestpayment->amount, $requestpayment->currency->id) }}</td>

                            <td>{{ ($requestpayment->accept_amount == 0) ?  "-" : '+'.formatNumber($requestpayment->accept_amount, $requestpayment->currency->id) }}</td>

                            <td>{{ $requestpayment->currency->code }}</td>

                            <td>
                                @if ($requestpayment->receiver)
                                    {{ $requestpayment->receiver->first_name.' '.$requestpayment->receiver->last_name }}
                                @elseif ($requestpayment->email)
                                    {{ $requestpayment->email }}
                                @elseif ($requestpayment->phone)
                                    {{ $requestpayment->phone }}
                                @else
                                    {{ '-' }}
                                @endif
                            </td>

                            <td>{{ (($requestpayment->status == 'Blocked') ? "Cancelled" :(($requestpayment->status == 'Refund') ? __('Refunded') : $requestpayment->status)) }}</td>
                        </tr>

                    @endforeach

                </table>
            </div>
        </div>
    </body>
</html>
