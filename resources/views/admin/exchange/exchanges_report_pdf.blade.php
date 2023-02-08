<!DOCTYPE html>
<html>
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
        <title>
            {{ __('Exchanges') }}
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
                        <td>{{ __('Amount') }}{{ __('') }}</td>
                        <td>{{ __('Fees') }}</td>
                        <td>{{ __('Total') }}</td>
                        <td>{{ __('Rate') }}</td>
                        <td>{{ __('From') }}</td>
                        <td>{{ __('To') }}</td>
                        <td>{{ __('Status') }}</td>
                    </tr>

                    @foreach($exchanges as $exchange)

                    <tr style="background-color:#fff; text-align:center; font-size:12px; font-weight:normal;">

                        <td>{{ dateFormat($exchange->created_at) }}</td>

                        <td>{{ $exchange->first_name.' '.$exchange->last_name }}</td>

                        <td>
                            @if($exchange->type == 'Out')
                                @if ($exchange->amount > 0)
                                    {{ formatNumber($exchange->amount) }}
                                @endif
                            @elseif($exchange->type == 'In')
                                @if ($exchange->amount > 0)
                                    {{ formatNumber($exchange->amount) }}
                                @endif
                            @endif
                        </td>

                        <td>{{ ($exchange->fee == 0) ? '-' : formatNumber($exchange->fee) }}</td>

                        @php
                            $total = $exchange->fee + $exchange->amount;
                        @endphp

                        <td>
                          @if($exchange->type == 'Out')
                            @if ($total > 0)
                                {{ '-'.formatNumber($total) }}
                            @endif
                          @elseif($exchange->type == 'In')
                            @if ($total > 0)
                                {{ '-'.formatNumber($total) }}
                            @endif
                          @endif
                        </td>
                        <td>{{ moneyFormat($exchange->tc_symbol, (float)($exchange->exchange_rate)) }}</td>

                        @if($exchange->type == 'Out')
                            <td>{{$exchange->fc_code}}</td>
                        @else
                            <td>{{$exchange->fc_code}} </td>
                        @endif

                        @if($exchange->type == 'In')
                            <td>{{$exchange->tc_code}}</td>
                        @else
                            <td>{{$exchange->tc_code}}</td>
                        @endif

                        <td>{{ ($exchange->status == 'Blocked') ? 'Cancelled' : $exchange->status }}</td>

                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </body>
</html>
