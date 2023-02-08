<?php

namespace App\DataTables\Admin;

use Yajra\DataTables\Services\DataTable;
use App\Models\Transaction;
use Auth, Config, Common;
class TransactionsDataTable extends DataTable
{

    public function ajax()
    {
        $columns = request()->columns;
        return datatables()
            ->eloquent($this->query())
            ->editColumn('created_at', function ($transaction) {
                return dateFormat($transaction->created_at);
            })
            ->addColumn('sender', function ($transaction) {
                $senderWithLink = '-';
                switch ($transaction->transaction_type_id) {
                    case Deposit:
                    case Transferred:
                    case Exchange_From:
                    case Exchange_To:
                    case Request_From:
                    case Withdrawal:
                    case (module('BlockIo') ? Crypto_Sent : false):
                    case (module('CryptoExchange') ? Crypto_Buy : false):
                    case (module('CryptoExchange') ? Crypto_Sell : false):
                    case (module('CryptoExchange') ? Crypto_Swap : false):
                        if (isset($transaction->user->first_name) && !empty($transaction->user->first_name)) {
                            $sender = $transaction->user->first_name . ' ' . $transaction->user->last_name;
                            $senderWithLink = (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_user')) ? '<a href="' . url(Config::get('adminPrefix') . '/users/edit/' . $transaction->user_id) . '">' . $sender . '</a>' : $sender;
                        } elseif (module('CryptoExchange') && isset($transaction->crypto_exchange) && !empty($transaction->crypto_exchange) ) {
                           $senderWithLink = (isset($transaction->crypto_exchange->email_phone) && !empty($transaction->crypto_exchange->email_phone)) ?  $transaction->crypto_exchange->email_phone : '-' ;
                        }
                        break;
                    case Payment_Sent:
                    case (config('referral.is_active') ? Referral_Award : false):
                        if (isset($transaction->user->first_name) && !empty($transaction->user->first_name)) {
                            $sender = $transaction->user->first_name . ' ' . $transaction->user->last_name;
                            $senderWithLink = (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_user')) ? '<a href="' . url(Config::get('adminPrefix') . '/users/edit/' . $transaction->user_id) . '">' . $sender . '</a>' : $sender;
                        }
                        break;
                    case Received:
                    case Request_To:
                    case Payment_Received:
                    case (module('BlockIo') ? Crypto_Received : false):
                        if (isset($transaction->end_user->first_name) && !empty($transaction->end_user->first_name)) {
                            $sender = $transaction->end_user->first_name . ' ' . $transaction->end_user->last_name;
                            $senderWithLink = (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_user')) ? '<a href="' . url(Config::get('adminPrefix') . '/users/edit/' . $transaction->end_user_id) . '">' . $sender . '</a>' : $sender;
                        }
                        break;
                    case (module('Investment') ? Investment : false):

                       if (isset($transaction->user->first_name) && !empty($transaction->user->first_name)) {
                            $sender = $transaction->user->first_name . ' ' . $transaction->user->last_name;
                            $senderWithLink = (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_user')) ? '<a href="' . url(Config::get('adminPrefix') . '/users/edit/' . $transaction->user_id) . '">' . $sender . '</a>' : $sender;
                        }
                        break;
                }
                return $senderWithLink;
            })
            ->addColumn('receiver', function ($transaction) {
                $receiverWithLink = '-';
                switch ($transaction->transaction_type_id) {
                    case Deposit:
                    case Exchange_From:
                    case Exchange_To:
                    case Withdrawal:
                    case Payment_Sent:
                    case (module('BlockIo') ? Crypto_Sent : false):
                        if (isset($transaction->end_user->first_name) && !empty($transaction->end_user->first_name)) {
                            $receiver = $transaction->end_user->first_name . ' ' . $transaction->end_user->last_name;
                            $receiverWithLink = (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_user')) ? '<a href="' . url(Config::get('adminPrefix') . '/users/edit/' . $transaction->end_user_id) . '">' . $receiver . '</a>' : $receiver;
                        }
                        break;
                    case Transferred:
                        if (isset($transaction->end_user->first_name) && !empty($transaction->end_user->first_name)) {
                            $receiver = $transaction->end_user->first_name . ' ' . $transaction->end_user->last_name;
                            $receiverWithLink = (Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_user')) ? '<a href="' . url(Config::get('adminPrefix') . '/users/edit/' . $transaction->end_user_id) . '">' . $receiver . '</a>' : $receiver;
                        } else {
                            if (isset($transaction->transfer->email) && !empty($transaction->transfer->email)) {
                                $receiverWithLink = $transaction->transfer->email;
                            } elseif (isset($transaction->transfer->phone) && !empty($transaction->transfer->phone)) {
                                $receiverWithLink = $transaction->transfer->phone;
                            }
                        }
                        break;
                    case Received:
                        if (isset($transaction->user->first_name) && !empty($transaction->user->first_name)) {
                            $receiver = $transaction->user->first_name . ' ' . $transaction->user->last_name;
                            $receiverWithLink = (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_user')) ? '<a href="' . url(Config::get('adminPrefix') . '/users/edit/' . $transaction->user_id) . '">' . $receiver . '</a>' : $receiver;
                        } else {
                            if (isset($transaction->transfer->email) && !empty($transaction->transfer->email)) {
                                $receiverWithLink = $transaction->transfer->email;
                            } elseif (isset($transaction->transfer->phone) && !empty($transaction->transfer->phone)) {
                                $receiverWithLink = $transaction->transfer->phone;
                            }
                        }
                        break;
                    case Request_From:
                        if (isset($transaction->end_user->first_name) && !empty($transaction->end_user->first_name)) {
                            $receiver = $transaction->end_user->first_name . ' ' . $transaction->end_user->last_name;
                            $receiverWithLink = (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_user')) ? '<a href="' . url(Config::get('adminPrefix') . '/users/edit/' . $transaction->end_user_id) . '">' . $receiver . '</a>' : $receiver;
                        } else {
                            if (isset($transaction->request_payment->email) && !empty($transaction->request_payment->email)) {
                                $receiverWithLink = $transaction->request_payment->email;
                            } elseif (isset( $transaction->request_payment->phone) && !empty($transaction->request_payment->phone)) {
                                $receiverWithLink = $transaction->request_payment->phone;
                            }
                        }
                        break;
                    case Request_To:
                        if (isset($transaction->user->first_name) && !empty( $transaction->user->first_name)) {
                            $receiver = $transaction->user->first_name . ' ' . $transaction->user->last_name;
                            $receiverWithLink = (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_user')) ? '<a href="' . url(Config::get('adminPrefix') . '/users/edit/' . $transaction->user_id) . '">' . $receiver . '</a>' : $receiver;
                        } else {
                            if (isset($transaction->request_payment->email) && !empty($transaction->request_payment->email)) {
                                $receiverWithLink = $transaction->request_payment->email;
                            } elseif (isset($transaction->request_payment->phone) && !empty($transaction->request_payment->phone)) {
                                $receiverWithLink = $transaction->request_payment->phone;
                            }
                        }
                        break;
                    case Payment_Received:
                    case (module('BlockIo') ? Crypto_Received : false):
                        if (!empty($transaction->user)) {
                            $receiver = $transaction->user->first_name . ' ' . $transaction->user->last_name;
                            $receiverWithLink = (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_user')) ? '<a href="' . url(Config::get('adminPrefix') . '/users/edit/' . $transaction->user_id) . '">' . $receiver . '</a>' : $receiver;
                        }
                        break;
                }
                return $receiverWithLink;
            })
            ->editColumn('transaction_type_id', function ($transaction) {
                return (isset($transaction->transaction_type->name) && !empty($transaction->transaction_type->name)) ? str_replace('_', ' ', $transaction->transaction_type->name) : '-';
            })
            ->editColumn('subtotal', function ($transaction) {
                return formatNumber($transaction->subtotal, $transaction->currency_id);
            })
            ->addColumn('fees', function ($transaction) {
                return (($transaction->charge_percentage == 0) && ($transaction->charge_fixed == 0)) ? '-' : formatNumber($transaction->charge_percentage + $transaction->charge_fixed, $transaction->currency_id);
            })
            ->editColumn('total', function ($transaction) {
                if (module('BlockIo') && optional($transaction->payment_method)->id == BlockIo && optional($transaction->transaction_type)->id == Crypto_Sent) {
                    return '<td><span class="text-'. (($transaction->total > 0) ? 'green">+' : 'red">')  . formatNumber($transaction->total - json_decode($transaction->cryptoAssetApiLog->payload)->network_fee, $transaction->currency_id) . '</span></td>';
                }
                return '<td><span class="text-'. (($transaction->total > 0) ? 'green">+' : 'red">')  . formatNumber($transaction->total, $transaction->currency_id) . '</span></td>';
            })
            ->editColumn('currency_id', function ($transaction) {
                return isset($transaction->currency->code) && !empty($transaction->currency->code) ? $transaction->currency->code : '-';
            })
            ->editColumn('status', function ($transaction) {
                return getStatusLabel($transaction->status);
            })
            ->addColumn('action', function ($transaction) {
                return (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_transaction')) ?
                    '<a href="' . url(Config::get('adminPrefix') . '/transactions/edit/' . $transaction->id) . '" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i></a>&nbsp;' : '';
            })
            ->rawColumns(['sender', 'receiver', 'total', 'status', 'action'])
            ->make(true);
    }

    public function query()
    {
        $status   = isset(request()->status) ? request()->status : 'all';
        $currency = isset(request()->currency) ? request()->currency : 'all';
        $user     = isset(request()->user_id) ? request()->user_id : null;
        $type     = isset(request()->type) ? request()->type : 'all';
        $from     = isset(request()->from) ? setDateForDb(request()->from) : null;
        $to       = isset(request()->to) ? setDateForDb(request()->to) : null;
        $query    = (new Transaction())->getTransactionsList($from, $to, $status, $currency, $type, $user);

        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'transactions.id', 'title' => __('ID'), 'searchable' => false, 'visible' => false])
            ->addColumn(['data' => 'uuid', 'name' => 'transactions.uuid', 'title' => __('UUID'), 'visible' => false])
            ->addColumn(['data' => 'created_at', 'name' => 'transactions.created_at', 'title' => __('Date')])
            //sender
            ->addColumn(['data' => 'sender', 'name' => 'user.last_name', 'title' => __('User'), 'visible' => false])
            ->addColumn(['data' => 'sender', 'name' => 'user.first_name', 'title' => __('User')])
            //transaction_type
            ->addColumn(['data' => 'transaction_type_id', 'name' => 'transaction_type.name', 'title' => __('Type')])
            ->addColumn(['data' => 'subtotal', 'name' => 'transactions.subtotal', 'title' => __('Amount')])
            ->addColumn(['data' => 'fees', 'name' => 'fees', 'title' => __('Fees')])
            ->addColumn(['data' => 'total', 'name' => 'transactions.total', 'title' => __('Total')])
            //currency
            ->addColumn(['data' => 'currency_id', 'name' => 'currency.code', 'title' => __('Currency')])
            //receiver
            ->addColumn(['data' => 'receiver', 'name' => 'end_user.last_name', 'title' => __('Receiver'), 'visible' => false])
            ->addColumn(['data' => 'receiver', 'name' => 'end_user.first_name', 'title' => __('Receiver')])
            ->addColumn(['data' => 'status', 'name' => 'transactions.status', 'title' => __('Status')])
            ->addColumn(['data' => 'action', 'name' => 'action', 'title' => __('Action'), 'orderable' => false, 'searchable' => false])
            ->parameters(dataTableOptions());
    }
}
