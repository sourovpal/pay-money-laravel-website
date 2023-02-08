<?php

namespace App\DataTables\Admin;

use Yajra\DataTables\Services\DataTable;
use App\Models\CurrencyExchange;
use Config, Auth, Common;

class CurrencyExchangesDataTable extends DataTable
{
    public function ajax()
    {
        $q = $this->query();
        return datatables()
            ->of($q)
            ->editColumn('created_at', function ($exchange) {
                return dateFormat($exchange->created_at);
            })
            ->editColumn('user_id', function ($exchange) {
                $sender = $exchange->first_name . ' ' . $exchange->last_name;

                return (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_user')) ? '<a href="' . url(Config::get('adminPrefix') . '/users/edit/' . $exchange->user_id) . '">' . $sender . '</a>' : $sender;
            })
            ->editColumn('amount', function ($exchange) {
                return moneyFormat($this->defaultCurrencySymbol, formatNumber($exchange->amount));
            })
            ->editColumn('fee', function ($exchange) {
                return ($exchange->fee == 0) ? '-' : formatNumber($exchange->fee);
            })
            ->addColumn('total', function ($exchange) {
                if ($exchange->type == 'Out') {
                    if (($exchange->fee + $exchange->amount) > 0) {
                        $total = '<span class="text-red">-' . moneyFormat($this->defaultCurrencySymbol, formatNumber($exchange->fee + $exchange->amount)) . '</span>';
                    } else {
                        $total = '<span class="text-green">+' . moneyFormat($this->defaultCurrencySymbol, formatNumber($exchange->fee + $exchange->amount)) . '</span>';
                    }
                } elseif ($exchange->type == 'In') {
                    if (($exchange->fee + $exchange->amount) > 0) {
                        $total = '<span class="text-green">+' . moneyFormat($exchange->tc_symbol, formatNumber($exchange->fee + $exchange->amount)) . '</span>';
                    } else {
                        $total = '<span class="text-red">' . moneyFormat($exchange->tc_symbol, formatNumber($exchange->fee + $exchange->amount)) . '</span>';
                    }
                }
                return $total;
            })
            ->editColumn('exchange_rate', function ($exchange) {
                return moneyFormat($exchange->tc_symbol, formatNumber($exchange->exchange_rate));
            })
            ->addColumn('fc_code', function ($exchange) {
                return $exchange->fc_code;
            })
            ->addColumn('tc_code', function ($exchange) {
                return $exchange->tc_code;
            })
            ->editColumn('status', function ($exchange) {
                return getStatusLabel($exchange->status);
            })
            ->addColumn('action', function ($exchange) {
                return (Common::has_permission(Auth::guard('admin')->user()->id, 'edit_exchange')) ? '<a href="' . url(Config::get('adminPrefix') . '/exchange/edit/' . $exchange->id) . '" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i></a>&nbsp;' : '';
            })
            ->rawColumns(['user_id', 'total', 'status', 'action', 'amount'])
            ->make(true);
    }

    public function query()
    {
        $status   = isset(request()->status) ? request()->status : 'all';
        $currency = isset(request()->currency) ? request()->currency : 'all';
        $user     = isset(request()->user_id) ? request()->user_id : null;
        $from     = isset(request()->from) ? setDateForDb(request()->from) : null;
        $to       = isset(request()->to) ? setDateForDb(request()->to) : null;
        $query    = (new CurrencyExchange())->getExchangesList($from, $to, $status, $currency, $user);

        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'id', 'title' => __('ID'), 'searchable' => false, 'visible' => false])
            ->addColumn(['data' => 'uuid', 'name' => 'uuid', 'title' => __('UUID'), 'visible' => false])
            ->addColumn(['data' => 'created_at', 'name' => 'created_at', 'title' => __('Date')])
            ->addColumn(['data' => 'user_id', 'name' => 'user_id', 'title' => __('User')])
            ->addColumn(['data' => 'amount', 'name' => 'amount', 'title' => __('Amount')])
            ->addColumn(['data' => 'fee', 'name' => 'fee', 'title' => __('Fees')])
            ->addColumn(['data' => 'total', 'name' => 'total', 'title' => __('Total')])
            ->addColumn(['data' => 'exchange_rate', 'name' => 'exchange_rate', 'title' => __('Rate')])
            ->addColumn(['data' => 'fc_code', 'name' => 'fc_code', 'title' => __('From')])
            ->addColumn(['data' => 'tc_code', 'name' => 'tc_code', 'title' => __('To')])
            ->addColumn(['data' => 'status', 'name' => 'status', 'title' => __('Status')])
            ->addColumn(['data' => 'action', 'name' => 'action', 'title' => __('Action'), 'orderable' => false, 'searchable' => false])
            ->parameters(dataTableOptions());
    }
}
