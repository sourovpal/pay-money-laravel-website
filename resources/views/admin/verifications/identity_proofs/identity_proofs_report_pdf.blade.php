<!DOCTYPE html>
<html>
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
        <title>
            {{ __('identityProofs') }}
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
                        <td>{{ __('Identity Type') }}</td>
                        <td>{{ __('Identity Number') }}</td>
                        <td>{{ __('Status') }}</td>
                    </tr>

                    @foreach($identityProofs as $identityProof)

                    <tr style="background-color:#fff; text-align:center; font-size:12px; font-weight:normal;">

                        <td>{{ dateFormat($identityProof->created_at) }}</td>

                        <td>{{ getColumnValue($identityProof->user) }}</td>

                        <td>{{ str_replace('_', ' ', ucfirst($identityProof->identity_type)) }}</td>

                        <td>{{ $identityProof->identity_number }}</td>

                        <td>{{ ucfirst($identityProof->status) }}</td>
                    </tr>
                    @endforeach

                </table>
            </div>
        </div>
    </body>
</html>
