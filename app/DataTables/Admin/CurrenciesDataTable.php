<?php

namespace App\DataTables\Admin;

use Yajra\DataTables\Services\DataTable;
use App\Http\Helpers\Common;
use App\Models\Currency;
use Config;

class CurrenciesDataTable extends DataTable
{
    public function ajax()
    {
        return datatables()
            ->eloquent($this->query())

            ->editColumn('name', function ($currency) {
                return (isset($currency->cryptoAssetSetting) && !empty($currency->cryptoAssetSetting)) ? $currency->name . '<br>(' . $currency->cryptoAssetSetting->cryptoProvider->name . ')' : $currency->name;
            })
            ->editColumn('type', function ($currency) {
                return str_contains($currency->type, '_') ? ucwords(str_replace('_', ' ', $currency->type)) : ucfirst($currency->type);
            })
            ->editColumn('rate', function ($currency) {
                return $currency->rate != 0  ? (float) ($currency->rate) : '-';
            })
            ->editColumn('logo', function ($currency) {
                if (!empty($currency->logo) && file_exists('public/uploads/currency_logos/' . $currency->logo)) {
                    $logo = '<td><img src="' . url('public/uploads/currency_logos/' . $currency->logo) . '" width="64" height="64"></td>';
                } else {
                    $logo = '<td><img src="' . url('public/user_dashboard/images/favicon.png') . '" width="64" height="64"></td>';
                }
                return $logo;
            })
            ->editColumn('status', function ($currency) {
                if ($currency->default == 1) {
                    return '<span class="label label-warning">Default Currency</span>';
                } else {
                    return getStatusLabel($currency->status);
                }
            })
            ->addColumn('action', function ($currency) {
                $edit = $delete = $feesLimit = $pm = '';
                $type = $currency->type == 'fiat' ? 'stripe' : 'coinpayments';

                if ($currency->type == 'crypto_asset') {
                    $edit = (Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_currency')) ? '<a href="' . url(Config::get('adminPrefix').'/blockio/edit/' . encrypt($currency->code)) . '" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i></a>&nbsp;' : '';
                } else {
                    $edit = (Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_currency')) ? '<a href="' . url(Config::get('adminPrefix').'/settings/edit_currency/' . $currency->id) . '" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i></a>&nbsp;' : '';
                }

                $delete = (Common::has_permission(\Auth::guard('admin')->user()->id, 'delete_currency')) && (($currency->type == 'fiat') || ($currency->type == 'crypto'))  ? '<a href="' . url(Config::get('adminPrefix').'/settings/delete_currency/' . $currency->id) . '" class="btn btn-xs btn-danger delete-warning"><i class="fa fa-trash"></i></a>&nbsp;' : '';

                $feesLimit = (Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_currency') && $currency->status == 'Active') && (($currency->type == 'fiat') || ($currency->type == 'crypto')) ? '<a href="' . url(Config::get('adminPrefix').'/settings/feeslimit/deposit/' . $currency->id) . '" class="btn btn-xs btn-success">

                <span class="glyphicon glyphicon-view">Fees</i></a>&nbsp;' : '';
                $pm = (Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_currency')) && $currency->status == 'Active' && (($currency->type == 'fiat') || ($currency->type == 'crypto')) ? '<a href="' . url(Config::get('adminPrefix').'/settings/payment-methods/'.$type.'/' . $currency->id) . '" class="btn btn-xs btn-primary">
                    <span class="glyphicon glyphicon-view">Payment-Methods</i></a>&nbsp;' : '';

                return $edit . $delete . $feesLimit . $pm;
            })
            ->rawColumns(['name', 'logo', 'status', 'action'])
            ->make(true);
    }

    public function query()
    {
        $query = Currency::with('cryptoAssetSetting:id,currency_id,crypto_provider_id', 'cryptoAssetSetting.cryptoProvider:id,name')->select('id', 'type', 'name', 'code', 'symbol', 'rate', 'logo', 'status', 'default')->orderBy('id', 'desc');
        return $this->applyScopes($query);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'currencies.id', 'title' => __('ID'), 'searchable' => false, 'visible' => false])
            ->addColumn(['data' => 'name', 'name' => 'currencies.name', 'title' => __('Name')])
            ->addColumn(['data' => 'name', 'name' => 'cryptoAssetSetting.cryptoProvider.name', 'title' => __('Name'), 'visible' => false])
            ->addColumn(['data' => 'code', 'name' => 'currencies.code', 'title' => __('Code')])
            ->addColumn(['data' => 'symbol', 'name' => 'currencies.symbol', 'title' => __('Symbol')])
            ->addColumn(['data' => 'type', 'name' => 'currencies.type', 'title' => __('Type')])
            ->addColumn(['data' => 'rate', 'name' => 'currencies.rate', 'title' => __('Rate')])
            ->addColumn(['data' => 'logo', 'name' => 'currencies.logo', 'title' => __('Logo')])
            ->addColumn(['data' => 'status', 'name' => 'currencies.status', 'title' => __('Status')])
            ->addColumn(['data' => 'action', 'name' => 'action', 'title' => __('Action'), 'orderable' => false, 'searchable' => false])
            ->parameters(dataTableOptions([
                "columnDefs" => [
                    [
                        "className" => "dt-center",
                        "targets" => "_all"
                    ]
                ]
            ]));
    }
}
