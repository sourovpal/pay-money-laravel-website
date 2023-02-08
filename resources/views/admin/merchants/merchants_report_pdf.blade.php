<!DOCTYPE html>
<html>
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
        <title>
            {{ __('Merchants') }}
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
                        <td>{{ __('ID') }}</td>
                        <td>{{ __('Type') }}</td>
                        <td>{{ __('Business Name') }}</td>
                        <td>{{ __('User') }}</td>
                        <td>{{ __('Url') }}</td>
                        <td>{{ __('Group') }}</td>
                        <td>{{ __('Logo') }}</td>
                        <td>{{ __('Status') }}</td>
                    </tr>

                    @foreach($merchants as $merchant)

                    <tr style="background-color:#fff; text-align:center; font-size:12px; font-weight:normal;">

                        <td>{{ dateFormat($merchant->created_at) }}</td>

                        <td>{{ (isset($merchant->merchant_uuid)) ? $merchant->merchant_uuid : '-' }}</td>

                        <td>{{ ucfirst($merchant->type) }}</td>

                        <td>{{ $merchant->business_name }}</td>

                        <td>{{ isset($merchant->user) ? $merchant->user->first_name.' '.$merchant->user->last_name :"-" }}</td>

                        <td>{{ $merchant->site_url }}</td>

                        <td>{{ isset($merchant->merchant_group) ? $merchant->merchant_group->name: "-" }}</td>

                        @if (isset($merchant->logo))
                            <td>{{ $merchant->logo }}</td>
                        @else
                            <td>-</td>
                        @endif

                        <td>{{ $merchant->status }}</td>
                    </tr>

                    @endforeach

                </table>
            </div>
        </div>
    </body>
</html>
