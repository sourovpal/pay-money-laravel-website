<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        @if ($transaction->transaction_type_id == Crypto_Sent)
            <title>{{ __("Crypto Sent") }}</title>
        @else
            <title>{{ __("Crypto Received") }}</title>
        @endif
    </head>
    <link rel="stylesheet" type="text/css" href="{{ asset('Modules/BlockIo/Resources/assets/user/css/crypto_sent_received_pdf.min.css') }}">
    <body>
        <div class="logo-section">
            <table class="mb-40">
                <tr>
                    <td>
                        {!! getSystemLogo() !!}
                    </td>
                </tr>
            </table>
            @if ($transaction->transaction_type_id == Crypto_Sent)
                <table>
                    <tr>
                        <td>
                            <table class="mt-20">
                                @if (isset($receiverAddress))
                                    <tr>
                                        <td class="receiver-address-title">{{ __('Receiver Address') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="receiver-address">{{ $receiverAddress }}</td>
                                    </tr>
                                    <br><br>
                                @endif
                                @if (isset($confirmations))
                                    <tr>
                                        <td class="confirmations-title">{{ __('Confirmations') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="confirmations">{{ $confirmations }}</td>
                                    </tr>
                                    <br><br>
                                @endif
                                <tr>
                                    <td class="transaction-id-title">{{ __('Transaction ID') }}</td>
                                </tr>
                                <tr>
                                    <td class="transaction-id">{{ $transaction->uuid }}</td>
                                </tr>
                                <br><br>
                                <tr>
                                    <td class="transaction-date-title">{{ __('Transaction Date') }}</td>
                                </tr>
                                <tr>
                                    <td class="transaction-date">{{ dateFormat($transaction->created_at) }}</td>
                                </tr>
                                <br><br>
                                <tr>
                                    <td class="status-title">{{ __('status') }}</td>
                                </tr>
                                <tr>
                                    <td class="status">{{ __($transaction->status) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table class="table-width">
                                <tr>
                                    <td colspan="2" class="details-label">{{ __('Details') }}</td>
                                </tr>
                                <tr>
                                    
                                    <td class="transaction-label-text">{{ __('Sent Amount') }}</td>
                                    <td class="transaction-value-text">{{ moneyFormat(optional($transaction->currency)->symbol, formatNumber($transaction->subtotal, $transaction->currency_id)) }}</td>
                                </tr>
                                <tr class="pb-10">
                                    
                                    <td class="transaction-label-text">{{ __('Network Fee') }}</td>
                                    <td class="transaction-value-text">{{ moneyFormat(optional($transaction->currency)->symbol, formatNumber($network_fee, $transaction->currency_id)) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="hr-line"></td>
                                </tr>
                                <tr>
                                    <td class="total-text">{{ __('Total') }}</td>
                                    <td class="total-value">{{ moneyFormat(optional($transaction->currency)->symbol, formatNumber($transaction->total-$network_fee, $transaction->currency_id)) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            @else
                <table>
                    <tr>
                        <td>
                            <table class="mt-20">
                                @if (isset($senderAddress))
                                    <tr>
                                        
                                        <td class="crypto-received-tnx-label">{{ __('Sender Address') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="crypto-received-tnx-value">{{ $senderAddress }}</td>
                                    </tr>
                                    <br><br>
                                @endif
                                @if (isset($confirmations))
                                    <tr>
                                        
                                        <td class="crypto-received-tnx-label">{{ __('Confirmations') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="crypto-received-tnx-value">{{ $confirmations }}</td>
                                    </tr>
                                    <br><br>
                                @endif
                                <tr>
                                    <td class="crypto-received-tnx-label">{{ __('Transaction ID') }}</td>
                                </tr>
                                <tr>
                                    <td class="crypto-received-tnx-value">{{$transaction->uuid}}</td>
                                </tr>
                                <br><br>
                                <tr>
                                    <td class="crypto-received-tnx-label">{{ __('Transaction Date') }}</td>
                                </tr>
                                <tr>
                                    <td class="crypto-received-tnx-value">{{ dateFormat($transaction->created_at) }}</td>
                                </tr>
                                <br><br>
                                <tr>
                                    <td class="crypto-received-tnx-label">{{ __('Status') }}</td>
                                </tr>
                                <tr>
                                    <td class="crypto-received-tnx-value">{{ __($transaction->status) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table class="table-width">
                                <tr>
                                    <td colspan="2" class="details-label">{{ __('Details') }}</td>
                                </tr>
                                <tr>
                                    
                                    <td class="transaction-label-text">{{ __('Received Amount') }}</td>
                                    <td class="transaction-value-text">{{ moneyFormat(optional($transaction->currency)->symbol, formatNumber($transaction->subtotal, $transaction->currency_id)) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="hr-line"></td>
                                </tr>
                                <tr>
                                    <td class="total-text">{{ __('Total') }}</td>
                                    <td class="total-value">{{ moneyFormat(optional($transaction->currency)->symbol, formatNumber($transaction->subtotal, $transaction->currency_id)) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            @endif
        </div>
    </body>
</html>