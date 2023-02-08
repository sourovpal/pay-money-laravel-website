<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use DB, Validator, Auth, Exception;
use Illuminate\Http\Request;
use App\Http\Helpers\Common;
use Illuminate\Support\Str;
use App\Models\{Wallet,
    MerchantPayment,
    MerchantGroup,
    Currency,
    Merchant,
    QrCode
};

class MerchantController extends Controller
{
    protected $helper;

    public function __construct()
    {
        $this->helper = new Common();
    }

    public function index()
    {
        $data['menu']          = 'merchant';
        $data['sub_menu']      = 'merchant';
        $data['content_title'] = 'Merchant';
        $data['icon']          = 'user';
        $data['list']          = Merchant::with(['appInfo', 'currency:id,code,type'])->where(['user_id' => Auth::user()->id])->orderBy('id', 'desc')->paginate(10);

        $data['defaultWallet'] = Wallet::where(['user_id' => auth()->user()->id, 'is_default' => 'Yes'])->first(['currency_id']);

        return view('user_dashboard.Merchant.list', $data);
    }

    public function add()
    {
        $data['menu']     = 'merchant';
        $data['sub_menu'] = 'merchant';

        $data['activeCurrencies'] = Currency::where(['status' => 'Active', 'type' => 'fiat'])->get(['id', 'code']);
        $data['defaultWallet']    = Wallet::where(['user_id' => auth()->user()->id, 'is_default' => 'Yes'])->first(['currency_id']);

        return view('user_dashboard.Merchant.add', $data);
    }

    public function store(Request $request)
    {
        $rules = array(
            'business_name' => 'required|unique:merchants,business_name',
            'site_url'      => 'required|url',
            'type'          => 'required',
            'note'          => 'required',
            'logo'          => 'mimes:png,jpg,jpeg,gif,bmp',
        );

        $fieldNames = array(
            'business_name' => 'Business Name',
            'site_url'      => 'Site url',
            'type'          => 'Type',
            'note'          => 'Note',
            'logo'          => 'The file must be an image (png, jpg, jpeg,gif,bmp)',
        );

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        else {
            try {
                DB::beginTransaction();

                $picture  = $request->logo;
                $fileName = null;

                if (isset($picture)) {
                    $response = uploadImage($picture, public_path("/user_dashboard/merchant/"),'100*80', null, '70*70');

                    if ($response['status'] === true) {
                        $fileName = $response['file_name'];
                    } else {
                        DB::rollBack();
                        $this->helper->one_time_message('error', $response['message']);
                        return back()->withErrors($validator)->withInput();
                    }
                }

                $merchantGroup               = MerchantGroup::where(['is_default' => 'Yes'])->select('id', 'fee')->first();
                $merchant                    = new Merchant();
                $merchant->user_id           = Auth::user()->id;
                $merchant->currency_id       = $request->currency_id;
                $merchant->merchant_group_id = isset($merchantGroup) ? $merchantGroup->id : null;
                $merchant->business_name     = $request->business_name;
                $merchant->site_url          = $request->site_url;
                $uuid                        = unique_code();
                $merchant->merchant_uuid     = $uuid;
                $merchant->type              = $request->type;
                $merchant->note              = $request->note;
                $merchant->logo              = $fileName != null ? $fileName : '';
                $merchant->fee               = isset($merchantGroup) ? $merchantGroup->fee : 0.00;
                $merchant->save();

                if (strtolower($request->type) == 'express') {
                    try {
                        $merchant->appInfo()->create([
                            'client_id'     => Str::random(30),
                            'client_secret' => Str::random(100),
                        ]);
                    } catch (Exception $ex) {
                        DB::rollBack();
                        $this->helper->one_time_message('error', __('Client id must be unique. Please try again!'));
                        return back();
                    }
                }

                DB::commit();
                $this->helper->one_time_message('success', __('Merchant Created Successfully!'));
                return redirect('merchants');
            }
            catch (Exception $e)
            {
                DB::rollBack();
                $this->helper->one_time_message('error', $e->getMessage());
                return redirect('merchants');
            }
        }
    }

    public function edit($id)
    {
        $data['menu']             = 'merchant';
        $data['sub_menu']         = 'merchant';
        $data['content_title']    = 'Merchant';
        $data['icon']             = 'user';
        $data['activeCurrencies'] = Currency::where(['status' => 'Active', 'type' => 'fiat'])->get(['id', 'code']);
        $data['merchant']         = $merchant = Merchant::with('currency:id,code')->find($id);
        $data['defaultWallet']    = Wallet::with(['currency:id,code'])->where(['user_id' => $merchant->user->id, 'is_default' => 'Yes'])->first(['currency_id']);

        if (!isset($merchant) || $merchant->user_id != Auth::user()->id)
        {
            abort(404);
        }
        return view('user_dashboard.Merchant.edit', $data);
    }

    public function update(Request $request)
    {
        $rules = array(
            'business_name' => 'required|unique:merchants,business_name,' . $request->id,
            'site_url'      => 'required|url',
            'note'          => 'required',
            'logo'          => 'mimes:png,jpg,jpeg,gif,bmp',
        );

        $fieldNames = array(
            'business_name' => 'Business Name',
            'site_url'      => 'Site url',
            'note'          => 'Note',
            'logo'          => 'The file must be an image (png, jpg, jpeg, gif,bmp)',
        );

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        } else {
            $picture  = $request->logo;
            $fileName = null;

            try {
                DB::beginTransaction();

                $merchant = Merchant::find($request->id, ['id', 'currency_id', 'business_name', 'site_url', 'note', 'logo']);
                
                if (isset($picture)) {
                    $response = uploadImage($picture, public_path("/user_dashboard/merchant/"),'100*80', $merchant->logo, '70*70');
                    if ($response['status'] === true) {
                        $fileName = $response['file_name'];
                    } else {
                        DB::rollBack();
                        $this->helper->one_time_message('error', $response['message']);
                        return back()->withErrors($validator)->withInput();
                    }
                }

                $merchant->business_name = $request->business_name;
                $merchant->site_url      = $request->site_url;
                $merchant->note          = $request->note;
                if ($fileName != null) {
                    $merchant->logo = $fileName;
                }
                
                if ($merchant->currency_id != $request->currency_id) {
                    $merchant->status = 'Moderation';
                }
                $merchant->currency_id   = $request->currency_id;
                $merchant->save();

                DB::commit();
                $this->helper->one_time_message('success', __('Merchant Updated Successfully!'));
                return redirect('merchants');
            } catch (Exception $e) {
                DB::rollBack();
                $this->helper->one_time_message('error', $e->getMessage());
                return redirect('merchants');
            }
        }
    }

    public function detail($id)
    {
        $data['menu']          = 'merchant';
        $data['sub_menu']      = 'merchant';
        $data['content_title'] = 'Merchant';
        $data['icon']          = 'user';
        $data['merchant']      = $merchant      = Merchant::find($id);
        $data['defaultWallet'] = Wallet::with(['currency:id,code'])->where(['user_id' => $merchant->user->id, 'is_default' => 'Yes'])->first(['currency_id']); //new
        if (!isset($merchant) || $merchant->user_id != Auth::user()->id)
        {
            abort(404);
        }
        return view('user_dashboard.Merchant.detail', $data);
    }

    public function payments()
    {
        $data['menu']              = 'merchant_payment';
        $data['sub_menu']          = 'merchant_payment';
        $data['content_title']     = 'Merchant payments';
        $data['icon']              = 'user';
        $merchant                  = Merchant::where('user_id', Auth::user()->id)->pluck('id')->toArray();
        $data['merchant_payments'] = MerchantPayment::with(['merchant:id,business_name', 'payment_method:id,name', 'currency:id,code'])->whereIn('merchant_id', $merchant)
            ->select('id', 'created_at', 'merchant_id', 'payment_method_id', 'order_no', 'amount', 'charge_percentage', 'charge_fixed', 'total', 'currency_id', 'status')
            ->orderBy('id', 'desc')->paginate(15);

        return view('user_dashboard.Merchant.payments', $data);
    }

    //Standard Merchant QrCode - starts
    public function generateStandardMerchantPaymentQrCode(Request $request) 
    {
        $qrCode           = QrCode::where(['object_id' => $request->merchantId, 'object_type' => 'standard_merchant', 'status' => 'Active'])->first(['id', 'secret']);
        $merchantCurrency = Currency::where('id', $request->merchantDefaultCurrency)->first(['code']);
        if (empty($qrCode)) {
            $createMerchantQrCode              = new QrCode();
            $createMerchantQrCode->object_id   = $request->merchantId;
            $createMerchantQrCode->object_type = 'standard_merchant';
            $createMerchantQrCode->secret      = convert_string('encrypt', $createMerchantQrCode->object_type . '-' . $request->merchantId . '-' . $merchantCurrency->code . '-' . $request->paymentAmount . '-' . Str::random(6));
            $createMerchantQrCode->status      = 'Active';
            $createMerchantQrCode->save();
            return response()->json([
                'status' => true,
                'secret' => urlencode($createMerchantQrCode->secret),
            ]);
        } else {
            $qrCode->status = 'Inactive';
            $qrCode->save();

            $createMerchantQrCode              = new QrCode();
            $createMerchantQrCode->object_id   = $request->merchantId;
            $createMerchantQrCode->object_type = 'standard_merchant';
            $createMerchantQrCode->secret      = convert_string('encrypt', $createMerchantQrCode->object_type . '-' . $request->merchantId . '-' . $merchantCurrency->code . '-' . $request->paymentAmount . '-' . Str::random(6));
            $createMerchantQrCode->status      = 'Active';
            $createMerchantQrCode->save();
            return response()->json([
                'status' => true,
                'secret' => urlencode($createMerchantQrCode->secret),
            ]);
        }
    }

    public function generateExpressMerchantQrCode(Request $request) 
    {
        $qrCode           = QrCode::where(['object_id' => $request->merchantId, 'object_type' => 'express_merchant', 'status' => 'Active'])->first(['id', 'secret']);
        $merchantCurrency = Currency::where('id', $request->merchantDefaultCurrencyId)->first(['code']);
        if (empty($qrCode)) {
            $createMerchantQrCode              = new QrCode();
            $createMerchantQrCode->object_id   = $request->merchantId;
            $createMerchantQrCode->object_type = 'express_merchant';
            $createMerchantQrCode->secret      = convert_string('encrypt', $createMerchantQrCode->object_type . '-' . $request->merchantId . '-' . $merchantCurrency->code . '-' . $request->clientId . Str::random(6));
            $createMerchantQrCode->status      = 'Active';
            $createMerchantQrCode->save();
            return response()->json([
                'status' => true,
                'secret' => urlencode($createMerchantQrCode->secret),
            ]);
        } else {
            return response()->json([
                'status' => true,
                'secret' => urlencode($qrCode->secret),
            ]);
        }
    }

    public function updateExpressMerchantQrCode(Request $request) 
    {
        $qrCode = QrCode::where(['object_id' => $request->merchantId, 'object_type' => 'express_merchant', 'status' => 'Active'])->first(['id', 'secret']);

        $merchantCurrency = Currency::where('id', $request->merchantDefaultCurrencyId)->first(['code']);
        if (empty($qrCode)) {
            $createMerchantQrCode              = new QrCode();
            $createMerchantQrCode->object_id   = $request->merchantId;
            $createMerchantQrCode->object_type = 'express_merchant';
            $createMerchantQrCode->secret      = convert_string('encrypt', $createMerchantQrCode->object_type . '-' . $request->merchantId . '-' . $merchantCurrency->code . '-' . $request->clientId . Str::random(6));
            $createMerchantQrCode->status      = 'Active';
            $createMerchantQrCode->save();
            return response()->json([
                'status' => true,
                'secret' => urlencode($createMerchantQrCode->secret),
            ]);
        } else {
            $qrCode->status = 'Inactive';
            $qrCode->save();

            //create a new qr-code entry on each update, after making status 'Inactive'
            $createMerchantQrCode              = new QrCode();
            $createMerchantQrCode->object_id   = $request->merchantId;
            $createMerchantQrCode->object_type = 'express_merchant';
            $createMerchantQrCode->secret      = convert_string('encrypt', $createMerchantQrCode->object_type . '-' . $request->merchantId . '-' . $merchantCurrency->code . '-' . $request->clientId . Str::random(6));
            $createMerchantQrCode->status      = 'Active';
            $createMerchantQrCode->save();
            return response()->json([
                'status' => true,
                'secret' => urlencode($createMerchantQrCode->secret),
            ]);
        }
    }

    public function printMerchantQrCode($merchantId, $objectType) {
        $this->helper->printQrCode($merchantId, $objectType);
    }
}
