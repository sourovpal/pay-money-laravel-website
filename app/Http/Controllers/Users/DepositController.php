<?php

namespace App\Http\Controllers\Users;

use App\Repositories\{StripeRepository, CoinPaymentRepository};
use DB, Validator, Session, Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Helpers\Common;
use App\Models\{FeesLimit,
    CurrencyPaymentMethod,
    CoinpaymentLogTrx,
    MerchantPayment,
    PaymentMethod,
    Transaction,
    Currency,
    Merchant,
    Deposit,
    Wallet,
    Bank,
    File
};

class DepositController extends Controller
{
    protected $helper;
    protected $stripeRepository, $coinPayment;

    public function __construct()
    {
        $this->helper  = new Common();
        $this->deposit = new Deposit();
        $this->stripeRepository = new StripeRepository();
        $this->coinPayment = new CoinPaymentRepository();
    }

    public function create(Request $request)
    {
        //set the session for validate the action
        setActionSession();

        $data['menu']          = 'deposit';
        $data['content_title'] = 'Deposit';
        $data['icon']          = 'university';

        $activeCurrency             = Currency::where(['status' => 'Active'])->get(['id', 'code', 'type', 'status']);
        $feesLimitCurrency          = FeesLimit::where(['transaction_type_id' => Deposit, 'has_transaction' => 'Yes'])->get(['currency_id', 'has_transaction']);
        $data['activeCurrencyList'] = $this->currencyList($activeCurrency, $feesLimitCurrency);
        $data['defaultWallet']      = Wallet::where(['user_id' => auth()->user()->id, 'is_default' => 'Yes'])->first(['currency_id']);

        if ($request->isMethod('post'))
        {
            $rules = array(
                'amount'         => 'required|numeric',
                'currency_id'    => 'required|integer',
                'payment_method' => 'required|integer',
            );
            $fieldNames = array(
                'amount'         => __("Amount"),
                'currency_id'    => __("Currency"),
                'payment_method' => __("Payment Method"),
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);
            if ($validator->fails())
            {
                return back()->withErrors($validator)->withInput();
            }

            //backend validation ends
            $currency_id       = (int) $request->currency_id;
            $amount            = (double) $request->amount;

            Session::put('coinpaymentAmount', $amount);

            $data['active_currency']    = $activeCurrency    = Currency::where(['status' => 'Active'])->get(['id', 'code', 'type', 'status']);
            $feesLimitCurrency          = FeesLimit::where(['transaction_type_id' => Deposit, 'has_transaction' => 'Yes'])->get(['currency_id', 'has_transaction']);
            $data['activeCurrencyList'] = $this->currencyList($activeCurrency, $feesLimitCurrency);
            $data['walletList']         = $activeCurrency;
            $data['payment_met']        = PaymentMethod::where(['status' => 'Active'])->get(['id', 'name']);
            $currency                   = Currency::where(['id' => $currency_id, 'status' => 'Active'])->first(['symbol']);
            $request['currSymbol']      = $currency->symbol;
            $data['payMtd']             = $payMtd             = PaymentMethod::where(['id' => $request->payment_method, 'status' => 'Active'])->first(['name']);
            $request['payment_name']    = $payMtd->name;
            $calculatedFee              = $this->getDepositFeesLimit($request);
            $request['fee']             = $calculatedFee->getData()->success->totalFees;
            $request['totalAmount']     = $request['amount'] + $request['fee'];
            session(['transInfo' => $request->all()]);
            
            $data['transInfo']           = $transInfo           = $request->all();
            $data['transInfo']['wallet'] = $request->currency_id;
            Session::put('payment_method_id', $request->payment_method);
            Session::put('wallet_currency_id', $request->currency_id);

            //Code for FeesLimit starts here
            $feesDetails = $this->helper->getFeesLimitObject([], Deposit, $currency_id, $transInfo['payment_method'], 'Yes', ['min_limit', 'max_limit']);
            if (@$feesDetails->max_limit == null)
            {
                if ((@$amount < @$feesDetails->min_limit))
                {
                    $data['error'] = __('Minimum amount ') . formatNumber($feesDetails->min_limit);
                    return view('user_dashboard.deposit.create', $data);
                }
            }
            else
            {
                if ((@$amount < @$feesDetails->min_limit) || (@$amount > @$feesDetails->max_limit))
                {
                    $data['error'] = __('Minimum amount ') . formatNumber($feesDetails->min_limit) . __(' and Maximum amount ') . formatNumber($feesDetails->max_limit);
                    return view('user_dashboard.deposit.create', $data);
                }
            }
            //Code for FeesLimit ends here

            if ($payMtd->name == 'Bank')
            {
                $banks                  = Bank::where(['currency_id' => $currency_id])->get(['id', 'bank_name', 'is_default', 'account_name', 'account_number']);
                $currencyPaymentMethods = CurrencyPaymentMethod::where('currency_id', $request->currency_id)->where('activated_for', 'like', "%deposit%")->where('method_data', 'like', "%bank_id%")->get(['method_data']);
                $data['banks']          = $bankList          = $this->bankList($banks, $currencyPaymentMethods);
                if (empty($bankList))
                {
                    $this->helper->one_time_message('error', __('Banks Does Not Exist For Selected Currency!'));
                    return redirect('deposit');
                }
                return view('user_dashboard.deposit.bank_confirmation', $data);
            }
            if (config('mobilemoney.is_active') && $payMtd->name == 'MobileMoney') {
                $mobileMoneys = \App\Models\MobileMoney::where(['currency_id' => $currency_id])->get(['id', 'mobilemoney_name', 'mobilemoney_number', 'is_default', 'holder_name', 'merchant_code']);
                $currencyPaymentMethods = CurrencyPaymentMethod::where('currency_id', $request->currency_id)->where('activated_for', 'like', "%deposit%")->where('method_data', 'like', "%mobilemoney_id%")->get(['method_data']);
                $data['mobileMoneys'] = $mobileMoneyList = \App\Models\MobileMoney::getMobileMoneyLists($mobileMoneys, $currencyPaymentMethods);
                if (empty($mobileMoneyList)) {
                    (new Common())->one_time_message('error', __('Mobile Money is not active for this currency.'));
                    return redirect('deposit');
                }
                return view('user_dashboard.deposit.mobilemoney_confirmation', $data);
            }
            return view('user_dashboard.deposit.confirmation', $data);
        }
        return view('user_dashboard.deposit.create', $data);
    }

    /**
     * [Extended Function] - starts
     */
    public function currencyList($activeCurrency, $feesLimitCurrency)
    {
        $selectedCurrency = [];
        foreach ($activeCurrency as $aCurrency)
        {
            foreach ($feesLimitCurrency as $flCurrency)
            {
                if ($aCurrency->id == $flCurrency->currency_id && $aCurrency->status == 'Active' && $flCurrency->has_transaction == 'Yes')
                {
                    $selectedCurrency[$aCurrency->id]['id']   = $aCurrency->id;
                    $selectedCurrency[$aCurrency->id]['code'] = $aCurrency->code;
                    $selectedCurrency[$aCurrency->id]['type'] = $aCurrency->type;
                }
            }
        }
        return $selectedCurrency;
    }
    /**
     * [Extended Function] - ends
     */

    public function bankList($banks, $currencyPaymentMethods)
    {
        $selectedBanks = [];
        $i             = 0;
        foreach ($banks as $bank)
        {
            foreach ($currencyPaymentMethods as $cpm)
            {
                if ($bank->id == json_decode($cpm->method_data)->bank_id)
                {
                    $selectedBanks[$i]['id']             = $bank->id;
                    $selectedBanks[$i]['bank_name']      = $bank->bank_name;
                    $selectedBanks[$i]['is_default']     = $bank->is_default;
                    $selectedBanks[$i]['account_name']   = $bank->account_name;
                    $selectedBanks[$i]['account_number'] = $bank->account_number;
                    $i++;
                }
            }
        }
        return $selectedBanks;
    }

    public function getBankDetailOnChange(Request $request)
    {
        $bank = Bank::with('file:id,filename')->where(['id' => $request->bank])->first(['bank_name', 'account_name', 'account_number', 'file_id']);
        if ($bank)
        {
            $data['status'] = true;
            $data['bank']   = $bank;

            if (!empty($bank->file_id))
            {
                $data['bank_logo'] = $bank->file->filename;
            }
        }
        else
        {
            $data['status'] = false;
            $data['bank']   = "Bank Not FOund!";
        }
        return $data;
    }

    public function getDepositMatchedFeesLimitsCurrencyPaymentMethodsSettingsPaymentMethods(Request $request)
    {
        $success = [];
        $feesLimits = FeesLimit::with([
            'currency' => function ($query)
            {
                $query->where(['status' => 'Active']);
            },
            'payment_method' => function ($q)
            {
                $q->where(['status' => 'Active']);
            },
        ])
        ->where(['transaction_type_id' => $request->transaction_type_id, 'has_transaction' => 'Yes', 'currency_id' => $request->currency_id])
        ->get(['payment_method_id']);

        $currencyPaymentMethods = CurrencyPaymentMethod::where('currency_id', $request->currency_id)->where('activated_for', 'like', "%deposit%")->get(['method_id']);
        $currencyPaymentMethodFeesLimitCurrenciesList = $this->currencyPaymentMethodFeesLimitCurrencies($feesLimits, $currencyPaymentMethods);

        if (env('THEME') == 'default') {
            if (defined('MobileMoney')) {
                unset($currencyPaymentMethodFeesLimitCurrenciesList[MobileMoney]);
            }
            $success['paymentMethods'] = $currencyPaymentMethodFeesLimitCurrenciesList;
        } else {
            $success['paymentMethods'] = $currencyPaymentMethodFeesLimitCurrenciesList;
        }
        
        $currencyType = Currency::where('id', $request->currency_id)->value('type');
        $success['preference'] = ($currencyType == 'fiat') ? preference('decimal_format_amount', 2) : preference('decimal_format_amount_crypto', 8);
        return response()->json(['success' => $success]);
    }

    public function currencyPaymentMethodFeesLimitCurrencies($feesLimits, $currencyPaymentMethods)
    {
        $selectedCurrencies = [];
        foreach ($feesLimits as $feesLimit)
        {
            foreach ($currencyPaymentMethods as $currencyPaymentMethod)
            {
                if ($feesLimit->payment_method_id == $currencyPaymentMethod->method_id)
                {
                    $selectedCurrencies[$feesLimit->payment_method_id]['id']   = $feesLimit->payment_method_id;
                    $selectedCurrencies[$feesLimit->payment_method_id]['name'] = $feesLimit->payment_method->name;
                }
            }
        }
        return $selectedCurrencies;
    }

    //getDepositFeesLimit
    public function getDepositFeesLimit(Request $request)
    {
        $amount  = (double) $request->amount;
        $user_id = auth()->user()->id;
        if (is_null($request->payment_method_id)) {
            $request->payment_method_id = (int) $request->payment_method;
        }
        $feesDetails = $this->helper->getFeesLimitObject([], Deposit, $request->currency_id, $request->payment_method_id, null, ['min_limit', 'max_limit', 'charge_percentage', 'charge_fixed']);
        if (@$feesDetails->max_limit == null) {
            $success['status'] = 200;
            if ((@$amount < @$feesDetails->min_limit)) {
                $success['message'] = __('Minimum amount ') . formatNumber($feesDetails->min_limit, $request->currency_id);
                $success['status']  = '401';
            }
        } else {
            $success['status'] = 200;
            if ((@$amount < @$feesDetails->min_limit) || (@$amount > @$feesDetails->max_limit)) {
                $success['message'] = __('Minimum amount ') . formatNumber($feesDetails->min_limit, $request->currency_id) . __(' and Maximum amount ') . formatNumber($feesDetails->max_limit, $request->currency_id);
                $success['status']  = '401';
            }
        }
        //Code for Amount Limit ends here

        //Code for Fees Limit Starts here
        if (empty($feesDetails))
        {
            $feesPercentage            = 0;
            $feesFixed                 = 0;
            $totalFess                 = $feesPercentage + $feesFixed;
            $totalAmount               = $amount + $totalFess;
            $success['feesPercentage'] = $feesPercentage;
            $success['feesFixed']      = $feesFixed;
            $success['totalFees']      = $totalFess;
            $success['totalFeesHtml']  = formatNumber($totalFess, $request->currency_id);
            $success['totalAmount']    = $totalAmount;
            $success['pFees']          = $feesPercentage;
            $success['fFees']          = $feesFixed;
            $success['pFeesHtml']      = formatNumber($feesPercentage, $request->currency_id);
            $success['fFeesHtml']      = formatNumber($feesFixed, $request->currency_id);
            $success['min']            = 0;
            $success['max']            = 0;
            $success['balance']        = 0;
        }
        else
        {
            $feesPercentage            = $amount * ($feesDetails->charge_percentage / 100);
            $feesFixed                 = $feesDetails->charge_fixed;
            $totalFess                 = $feesPercentage + $feesFixed;
            $totalAmount               = $amount + $totalFess;
            $success['feesPercentage'] = $feesPercentage;
            $success['feesFixed']      = $feesFixed;
            $success['totalFees']      = $totalFess;
            $success['totalFeesHtml']  = formatNumber($totalFess, $request->currency_id);
            $success['totalAmount']    = $totalAmount;
            $success['pFeesHtml']      = formatNumber($feesDetails->charge_percentage, $request->currency_id);
            $success['fFeesHtml']      = formatNumber($feesDetails->charge_fixed, $request->currency_id);
            $success['min']            = $feesDetails->min_limit;
            $success['max']            = $feesDetails->max_limit;
            $wallet                    = Wallet::where(['currency_id' => $request->currency_id, 'user_id' => $user_id])->first(['balance']);
            $success['balance']        = @$wallet->balance ? @$wallet->balance : 0;
        }
        return response()->json(['success' => $success]);
    }

    public function store(Request $request)
    {
        //to check action whether action is valid or not
        actionSessionCheck();

        $userid = auth()->user()->id;
        $rules  = [
            'amount' => 'required|numeric',
        ];
        $fieldNames = [
            'amount' => __('Amount'),
        ];
        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);
        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }

        $methodId              = $request->method;
        $amount                = $request->amount;
        $PaymentMethod         = PaymentMethod::find($methodId, ['id', 'name']);
        $method                = ucfirst(strtolower($PaymentMethod->name));
        $currencyPaymentMethod = CurrencyPaymentMethod::where(['currency_id' => session('wallet_currency_id'), 'method_id' => $methodId])->where('activated_for', 'like', "%deposit%")->first(['method_data']);
        $methodData            = json_decode($currencyPaymentMethod->method_data);
        if (empty($methodData))
        {
            $this->helper->one_time_message('error', __('Payment gateway credentials not found!'));
            return back();
        }
        Session::put('method', $method);
        Session::put('payment_method_id', $methodId);
        Session::put('amount', $amount);
        Session::save();

        $currencyId = session('wallet_currency_id');
        $currency   = Currency::find($currencyId, ['id', 'code', 'type']);
        if ($method == 'Paypal')
        {
            if (!isset($currency->code)) {
                $this->helper->one_time_message('error', __("You do not have the requested currency"));
                return redirect()->back();
            }
            if (!isset($methodData->client_id)) {
                $this->helper->one_time_message('error', __('Payment gateway credentials not found!'));
                return redirect()->back();
            }
            $sessionValue         = Session::get('transInfo');
            $data['clientId']     = $methodData->client_id;
            $data['amount']       = number_format($sessionValue['totalAmount'], 2);
            $data['currencyCode'] = $currency->code;
            return view('user_dashboard.deposit.paypal', $data);
        }
        else if ($method == 'Stripe')
        {
            $publishable = $methodData->publishable_key;
            Session::put('publishable', $publishable);
            return redirect('deposit/stripe_payment');
        }
        else if ($method == 'Payumoney')
        {
            $transInfo = Session::get('transInfo');
            $currencyId            = $transInfo['currency_id'];
            $currencyPaymentMethod = CurrencyPaymentMethod::where(['currency_id' => $currencyId, 'method_id' => $methodId])->where('activated_for', 'like', "%deposit%")->first(['method_data']);
            $methodData            = json_decode($currencyPaymentMethod->method_data);
            Session::put('mode', $methodData->mode);
            Session::put('key', $methodData->key);
            Session::put('salt', $methodData->salt);
            return redirect('deposit/payumoney_payment');
        }
        else if ($method == 'Coinpayments')
        {
            $data = [];
            $this->coinPayment->Setup($methodData->private_key, $methodData->public_key);

            $rates = $this->coinPayment->GetRates(0)['result'];
            
            if (empty($rates)) {
                $this->helper->one_time_message('error', __("It seems the credential provided is wrong. You may contact with the system administrator."));
                return redirect('deposit');
            }

            $rateofFiatCurrency = $rates[$currency->code]['rate_btc'];
            $rateAmount = $rateofFiatCurrency * $amount;
            $formattedCurrencyList = getFormatedCurrencyList($rates, $rateAmount);

            if ($currency->type == 'crypto') {
                
                $acceptedCoin = $formattedCurrencyList['coins_accept'];
                $acceptedCoinIso = array_column( $acceptedCoin, 'iso');

                if (! empty($currency->code) && in_array($currency->code, $acceptedCoinIso)) {

                    $uuid = unique_code();

                    $transactionData = [
                        'amount' => session('transInfo')['totalAmount'],
                        'currency1' => $currency->code,
                        'currency2' => $currency->code,
                        'buyer_email' => auth()->user()->email,
                        'buyer_name' => auth()->user()->first_name .' '. auth()->user()->last_name,
                        'item_name' => 'Deposit via coinpayment',
                        'custom' => $uuid,
                        'ipn_url' => url("coinpayment/check"),
                        'cancel_url' => url("deposit/coinpayments/cancel"),
                        'success_url' => url("deposit/payment_success"),
                    ];

                    $makeTransaction =  $this->coinPayment->CreateTransaction($transactionData);

                    if ( $makeTransaction['error'] !== 'ok' ) {
                        $this->helper->one_time_message('error', $makeTransaction['error']);
                        return redirect('deposit');
                    }

                    if (auth()->check()) {

                        $payload = ['type' => 'deposit', 'currency' => $currency->code];
                        $makeTransaction['payload'] = $payload;
                        $transactionInfo = $this->getCoinPaymentTransactionInfo($makeTransaction['result']['txn_id']);
                        
                        if ( $transactionInfo['error'] !== 'ok' ) {
                            $this->helper->one_time_message('error', $transactionInfo['error']);
                            return redirect('deposit');
                        }

                        if ($transactionInfo['error'] === 'ok') {

                            $transactionInfoData = $transactionInfo['result'];

                            $coinpaymentLogTrxes = [
                                'payment_id'         => $makeTransaction['result']['txn_id'],
                                'payment_address'    => $transactionInfoData['payment_address'],
                                'coin'               => $transactionInfoData['coin'],
                                'fiat'               => $payload['currency'],
                                'status_text'        => $transactionInfoData['status_text'],
                                'status'             => $transactionInfoData['status'],
                                'payment_created_at' => date('Y-m-d H:i:s', $transactionInfoData['time_created']),
                                'expired'            => date('Y-m-d H:i:s', $transactionInfoData['time_expires']),
                                'amount'             => $transactionInfoData['amountf'],
                                'confirms_needed'    => empty($makeTransaction['result']['confirms_needed']) ? 0 : $makeTransaction['result']['confirms_needed'],
                                'qrcode_url'         => empty($makeTransaction['result']['qrcode_url']) ? '' : $makeTransaction['result']['qrcode_url'],
                                'status_url'         => empty($makeTransaction['result']['status_url']) ? '' : $makeTransaction['result']['status_url'],
                            ];

                            if (isset($makeTransaction['payload']['type']) && $makeTransaction['payload']['type'] == "deposit") {
                                //insert into deposit
                                $payment_method_id = Session::get('payment_method_id');
                                $coinpaymentAmount = Session::get('coinpaymentAmount');

                                //charge percentage calculation
                                $currencyId = Currency::where('code', $makeTransaction['payload']['currency'])->value('id');
                                $feeInfo = FeesLimit::where(['transaction_type_id' => Deposit, 'currency_id' => $currencyId, 'payment_method_id' => $payment_method_id])->first(['charge_percentage', 'charge_fixed']);
                                $p_calc     = $coinpaymentAmount * (@$feeInfo->charge_percentage / 100);

                                try
                                {
                                    DB::beginTransaction();

                                    // Deposit
                                    $deposit                    = new Deposit();
                                    $deposit->uuid              = $uuid;
                                    $deposit->charge_percentage = @$feeInfo->charge_percentage ? $p_calc : 0;
                                    $deposit->charge_fixed      = @$feeInfo->charge_fixed ? @$feeInfo->charge_fixed : 0;
                                    $deposit->amount            = $coinpaymentAmount;
                                    $deposit->status            = 'Pending';
                                    $deposit->user_id           = auth()->user()->id;
                                    $deposit->currency_id       = $currencyId;
                                    $deposit->payment_method_id = $payment_method_id;
                                    $deposit->save();

                                    // Transaction
                                    $transaction                           = new Transaction();
                                    $transaction->user_id                  = auth()->user()->id;
                                    $transaction->currency_id              = $currencyId;
                                    $transaction->payment_method_id        = $payment_method_id;
                                    $transaction->uuid                     = $uuid;
                                    $transaction->transaction_reference_id = $deposit->id;
                                    $transaction->transaction_type_id      = Deposit;
                                    $transaction->subtotal                 = $coinpaymentAmount;
                                    $transaction->percentage               = @$feeInfo->charge_percentage;
                                    $transaction->charge_percentage        = $deposit->charge_percentage;
                                    $transaction->charge_fixed             = $deposit->charge_fixed;
                                    $transaction->total                    = $coinpaymentAmount + $deposit->charge_percentage + $deposit->charge_fixed;
                                    $transaction->status                   = 'Pending';
                                    $transaction->save();

                                    // Wallet creation if request currency wallet does not exist
                                    $wallet = Wallet::where(['user_id' => auth()->user()->id, 'currency_id' => $currencyId])->first(['id']);

                                    if (empty($wallet)) {
                                        $wallet              = new Wallet();
                                        $wallet->user_id     = auth()->user()->id;
                                        $wallet->currency_id = $currencyId;
                                        $wallet->balance     = 0;
                                        $wallet->is_default  = 'No';
                                        $wallet->save();
                                    }

                                    $payload['deposit_id']     = $deposit->id;
                                    $payload['transaction_id'] = $transaction->id;
                                    $payload['uuid']           = $uuid;
                                    $payload['receivedf']      = $transactionInfoData['receivedf'];
                                    $payload['time_expires']   = $transactionInfoData['time_expires'];
                                    $payload                   = json_encode($payload);

                                    $coinpaymentLogTrxes['payload'] = $payload;
                                    auth()->user()->coinpayment_transactions()->create($coinpaymentLogTrxes);

                                    DB::commit();


                                    // Mail To Admin
                                    $response = $this->helper->sendTransactionNotificationToAdmin('deposit', ['data' => $deposit]);

                                    session()->forget(['coinPaymentTransaction', 'wallet_currency_id', 'method', 'payment_method_id', 'amount', 'transInfo']);
                                    clearActionSession();

                                    return view('user_dashboard.deposit.coinpayment_summery', ['transactionDetails' => $makeTransaction, 'transactionInfo' =>  $transactionInfo]);
                                }
                                catch (Exception $e)
                                {
                                    DB::rollBack();
                                    session()->forget(['coinPaymentTransaction','wallet_currency_id', 'method', 'payment_method_id', 'amount', 'transInfo']);
                                    clearActionSession();
                                    $exception = [];
                                    $exception['error'] = json_encode($e->getMessage());
                                    return $exception;
                                }
                            }
                        }
                    }

                } else {
                    $this->helper->one_time_message('error', __('Please select a crypto coin.'));
                    return redirect('deposit');
                }
            }

            $coinPaymentTransaction['coinList'] = $formattedCurrencyList['coins_accept'];
            $coinPaymentTransaction['currencyCode'] = $currency->code;
            $coinPaymentTransaction['type'] = 'deposit';
            Session::put('coinPaymentTransaction', $coinPaymentTransaction);

            $data = ['coins' => $formattedCurrencyList['coins'], 'coin_accept' => $formattedCurrencyList['coins_accept'], 'encoded_coin_accept' => json_encode($formattedCurrencyList['coins_accept']), 'fiat' => $formattedCurrencyList['fiat'], 'aliases' => $formattedCurrencyList['aliases']];

            $data['amount'] = $amount;
            $data['currencyCode'] = $currency->code;

            return view('user_dashboard.deposit.coinpayment', $data);
        }
        else if ($method == 'Payeer')
        {
            $transInfo             = Session::get('transInfo');
            $currencyId            = $transInfo['currency_id'];
            $currencyPaymentMethod = CurrencyPaymentMethod::where(['currency_id' => $currencyId, 'method_id' => $methodId])->where('activated_for', 'like', "%deposit%")->first(['method_data']);
            $payeer                = json_decode($currencyPaymentMethod->method_data);
            Session::put('payeer_merchant_id', $payeer->merchant_id);
            Session::put('payeer_secret_key', $payeer->secret_key);
            Session::put('payeer_encryption_key', $payeer->encryption_key);
            Session::put('payeer_merchant_domain', $payeer->merchant_domain);
            return redirect('deposit/payeer/payment');
        }
        else
        {
            $this->helper->one_time_message('error', __('Please check your payment method!'));
        }
        return redirect()->back();
    }

    /* Start of Stripe */
    /**
     * Showing Stripe view Page
     */
    public function stripePayment()
    {
        $data['menu']              = 'deposit';
        $data['amount']            = Session::get('amount');
        $data['payment_method_id'] = $method_id = Session::get('payment_method_id');
        $data['content_title']     = 'Deposit';
        $data['icon']              = 'university';
        $sessionValue              = session('transInfo');
        $currencyId                = $sessionValue['currency_id'];
        $currencyPaymentMethod     = CurrencyPaymentMethod::where(['currency_id' => $currencyId, 'method_id' => $method_id])->where('activated_for', 'like', "%deposit%")->first(['method_data']);
        $methodData                = json_decode($currencyPaymentMethod->method_data);
        $data['publishable']       = $methodData->publishable_key;
        $data['secretKey']         = $methodData->secret_key;
        if (!isset($data['publishable']) || !isset($data['secretKey'])) {
            $msg = __("Payment gateway credentials not found!");
            $this->helper->one_time_message('error', $msg);
        }
        return view('user_dashboard.deposit.stripe', $data);
    }

    public function stripeMakePayment(Request $request)
    {
        $data = [];
        $data['status']  = 200;
        $data['message'] = "Success";
        $validation = Validator::make($request->all(), [
            'cardNumber' => 'required',
            'month'      => 'required|digits_between:1,12|numeric',
            'year'       => 'required|numeric',
            'cvc'        => 'required|numeric',
        ]);
        if ($validation->fails()) {
            $data['message'] = $validation->errors()->first();
            $data['status']  = 401;
            return response()->json([
                'data' => $data
            ]);
        }
        $sessionValue      = session('transInfo');
        $amount            = (double) $sessionValue['totalAmount'];
        $payment_method_id = $method_id = Session::get('payment_method_id');
        $currencyId        = (int) $sessionValue['currency_id'];
        $currency          = Currency::find($currencyId, ["id", "code"]);
        $currencyPaymentMethod     = CurrencyPaymentMethod::where(['currency_id' => $currencyId, 'method_id' => $method_id])->where('activated_for', 'like', "%deposit%")->first(['method_data']);
        $methodData        = json_decode($currencyPaymentMethod->method_data);
        $secretKey         = $methodData->secret_key;
        if (!isset($secretKey)) {
            $data['message']  = __("Payment gateway credentials not found!");
            return response()->json([
                'data' => $data
            ]);
        }
        $response = $this->stripeRepository->makePayment($secretKey, round($amount, 2), strtolower($currency->code), $request->cardNumber, $request->month, $request->year, $request->cvc);
        if ($response->getData()->status != 200) {
            $data['status']  = $response->getData()->status;
            $data['message'] = $response->getData()->message;
        } else {
            $data['paymentIntendId'] = $response->getData()->paymentIntendId;
            $data['paymentMethodId'] = $response->getData()->paymentMethodId;
        }
        return response()->json([
            'data' => $data
        ]);
    }

    public function stripeConfirm(Request $request)
    {
        $data = [];
        $data['status']  = 401;
        $data['message'] = "Fail";
        try {
            DB::beginTransaction();
            $validation = Validator::make($request->all(), [
                'paymentIntendId'  => 'required',
                'paymentMethodId'  => 'required',
            ]);
            if ($validation->fails()) {
                $data['message'] = $validation->errors()->first();
                return response()->json([
                    'data' => $data
                ]);
            }
            $sessionValue      = session('transInfo');
            $amount            = (double) $sessionValue['totalAmount'];
            $payment_method_id = $method_id                 = Session::get('payment_method_id');
            $currencyId        = (int) $sessionValue['currency_id'];
            $currency          = Currency::find($currencyId, ["id", "code"]);
            $currencyPaymentMethod     = CurrencyPaymentMethod::where(['currency_id' => $currencyId, 'method_id' => $method_id])->where('activated_for', 'like', "%deposit%")->first(['method_data']);
            $methodData        = json_decode($currencyPaymentMethod->method_data);
            if (!isset($methodData->secret_key)) {
                $data['message']  = __("Payment gateway credentials not found!");
                return response()->json([
                    'data' => $data
                ]);
            }
            $secretKey = $methodData->secret_key;
            $response  = $this->stripeRepository->paymentConfirm($secretKey, $request->paymentIntendId, $request->paymentMethodId);
            if ($response->getData()->status != 200) {
                $data['message'] = $response->getData()->message;
                return response()->json([
                    'data' => $data
                ]);
            }
            $user_id           = auth()->user()->id;
            $wallet            = Wallet::where(['currency_id' => $sessionValue['currency_id'], 'user_id' => $user_id])->first(['id', 'currency_id']);
            if (empty($wallet)) {
                $walletInstance = Wallet::createWallet($user_id, $sessionValue['currency_id']);
            }
            $currencyId = isset($wallet->currency_id) ? $wallet->currency_id : $walletInstance->currency_id;
            $currency   = Currency::find($currencyId, ['id', 'code']);

            $depositConfirm      = Deposit::success($currencyId, $payment_method_id, $user_id, $sessionValue);
            $data['status']      = 200;
            $data['message']     = "Success";
            $data['transaction'] = $depositConfirm['transaction'];
            Session::put('transaction', $depositConfirm['transaction']);

            if (config('referral.is_active')) {
                $refAwardData                    = [];
                $refAwardData['userId']          = $user_id;
                $refAwardData['currencyId']      = $currencyId;
                $refAwardData['currencyCode']    = $currency->code;
                $refAwardData['presentAmount']   = $depositConfirm['transaction']->subtotal;
                $refAwardData['paymentMethodId'] = $payment_method_id;
                $awardResponse = (new \App\Models\ReferralAward)->checkReferralAward($refAwardData);
            }

            DB::commit();

            // Send referralaward email/sms to users
            if (config('referral.is_active') && !empty($awardResponse)) {
                (new \App\Models\ReferralAward)->sendReferralAwardNotification($awardResponse);
            }
            
            // Send deposit email/sms to admin
            $response = $this->helper->sendTransactionNotificationToAdmin('deposit', ['data' => $depositConfirm['deposit']]);
            return response()->json(['data' => $data]);
        } catch (Exception $e) {
            DB::rollBack();
            Session::forget(['coinpaymentAmount', 'wallet_currency_id', 'method', 'payment_method_id', 'amount', 'publishable', 'transInfo']);
            $data['message'] =  $e->getMessage();
            // $data['transaction'] = $transaction;
            return response()->json([
                'data' => $data
            ]);
        }
    }

    public function stripePaymentSuccess()
    {
        if (empty(session('transaction'))) {
            return redirect('deposit');
        } else {
            $data['transaction'] = session('transaction');
            //clearing session
            session()->forget(['transaction', 'coinpaymentAmount', 'wallet_currency_id', 'method', 'payment_method_id', 'amount', 'publishable', 'transInfo', 'data']);
            clearActionSession();
            return view('user_dashboard.deposit.success', $data);
        }
    }

    /* End of Stripe */

    /* Start of PayPal */

    public function paypalDepositPaymentSuccess($amount)
    {
        try {
            DB::beginTransaction();
            actionSessionCheck();
            if (empty(session('transInfo'))) {
                return redirect('deposit');
            }
            $sessionValue      = session('transInfo');
            // $sessionValue['amount'] = (double) base64_decode($amount);
            $payment_method_id = (int) $sessionValue['payment_method'];
            $user_id           = auth()->user()->id;
            $currencyId        = (int) $sessionValue['currency_id'];
            $wallet            = Wallet::where(['currency_id' => $currencyId, 'user_id' => $user_id])->first(['id', 'currency_id']);
            if (empty($wallet)) {
                $walletInstance = Wallet::createWallet($user_id, $sessionValue['currency_id']);
            }
            $currencyId = isset($wallet->currency_id) ? $wallet->currency_id : $walletInstance->currency_id;
            $currency   = Currency::find($currencyId, ['id', 'code']);
            if (!isset($currency->code)) {
                $this->helper->one_time_message("error", __("You do not have the requested currency"));
                return redirect()->back();
            }
            $depositConfirm      = Deposit::success($currencyId, $payment_method_id, $user_id, $sessionValue);
            $data['transaction'] = $depositConfirm['transaction'];
            
            if (config('referral.is_active')) {
                $refAwardData                    = [];
                $refAwardData['userId']          = $user_id;
                $refAwardData['currencyId']      = $sessionValue['currency_id'];
                $refAwardData['currencyCode']    = $currency->code;
                $refAwardData['presentAmount']   = $depositConfirm['transaction']->subtotal;
                $refAwardData['paymentMethodId'] = $payment_method_id;
                $awardResponse = (new \App\Models\ReferralAward)->checkReferralAward($refAwardData);
            }
            
            DB::commit();
            session()->forget(['coinpaymentAmount', 'wallet_currency_id', 'method', 'payment_method_id', 'amount', 'transInfo', 'data']);
       
            // Send referralaward email/sms to users
            if (config('referral.is_active') && !empty($awardResponse)) {
                (new \App\Models\ReferralAward)->sendReferralAwardNotification($awardResponse);
            }
            
            // Send deposit email or sms notification to admin
            $response = $this->helper->sendTransactionNotificationToAdmin('deposit', ['data' => $depositConfirm['deposit']]);
            return view('user_dashboard.deposit.success', $data);
        } catch (Exception $e) {
            DB::rollBack();
            session()->forget(['coinpaymentAmount', 'wallet_currency_id', 'method', 'payment_method_id', 'amount', 'transInfo']);
            clearActionSession();
            $this->helper->one_time_message('error', $e->getMessage());
            return redirect('deposit');
        }
    }

    public function paymentCancel()
    {
        clearActionSession();
        $this->helper->one_time_message('error', __('You have cancelled your payment'));
        return back();
    }
    /* End of PayPal */

    /* Start of Payumoney */
    public function payumoneyPayment()
    {
        $data['menu'] = 'deposit';

        //Check Currency Code - starts - pm_v2.3
        $currency_id  = session('transInfo')['currency_id'];
        $currencyCode = Currency::where(['id' => $currency_id])->first(['code'])->code;
        if ($currencyCode !== 'INR')
        {
            session()->forget(['coinpaymentAmount', 'wallet_currency_id', 'method', 'payment_method_id', 'amount', 'transInfo']);
            clearActionSession();
            $this->helper->one_time_message('error', __('PayUMoney only supports Indian Rupee(INR)'));
            return redirect('deposit');
        }
        $amount = session('transInfo')['amount'];             //fixed - was getting total - should get amount
        $data['amount'] = number_format((float) $amount, 2, '.', ''); //Payumoney accepts 2 decimal places only - if not rounded to 2 decimal places, Payumoney will throw.
        $data['mode'] = Session::get('mode');
        $data['key'] = Session::get('key');
        $data['salt'] = Session::get('salt');
        $data['email'] = auth()->user()->email;
        $data['txnid'] = unique_code();
        $data['firstname'] = auth()->user()->first_name;
        $data['productinfo'] = 'Account Deposit';
        $data['service_provider'] = 'payu_paisa';
        $data['surl'] = url('/deposit/payumoney_confirm');
        $data['furl'] = url('/deposit/payumoney_fail');

        $hashSequence = $data['key'] . '|' . $data['txnid'] . '|' . $data['amount'] . '|' . $data['productinfo'] . '|' . $data['firstname'] . '|' . $data['email'] . '|||||||||||' . $data['salt'];

        $data['hash'] = hash("sha512", $hashSequence);

        if ($data['mode'] == 'sandbox') {
            $data['action'] = "https://sandboxsecure.payu.in/_payment";
        } else {
            $data['action'] = "https://secure.payu.in/_payment";
        }

        return view('user_dashboard.deposit.payumoney', $data);
    }

    public function payumoneyPaymentConfirm()
    {
        actionSessionCheck();

        $sessionValue = session('transInfo');
        $user_id      = auth()->user()->id;
        $amount       = Session::get('amount');
        $uuid         = unique_code();

        if ($_POST['status'] == 'success')
        {
            $feeInfo    = $this->helper->getFeesLimitObject([], Deposit, $sessionValue['currency_id'], $sessionValue['payment_method'], null, ['charge_percentage', 'charge_fixed']);
            $p_calc     = $sessionValue['amount'] * (@$feeInfo->charge_percentage / 100);
            $total_fees = $p_calc+@$feeInfo->charge_fixed;

            try
            {
                DB::beginTransaction();

                //Deposit
                $deposit                    = new Deposit();
                $deposit->user_id           = $user_id;
                $deposit->currency_id       = $sessionValue['currency_id'];
                $deposit->payment_method_id = Session::get('payment_method_id');
                $deposit->uuid              = $uuid;
                $deposit->charge_percentage = @$feeInfo->charge_percentage ? $p_calc : 0;
                $deposit->charge_fixed      = @$feeInfo->charge_fixed ? @$feeInfo->charge_fixed : 0;
                $deposit->amount            = $present_amount            = $amount - $total_fees;
                $deposit->status            = 'Success';
                $deposit->save();

                //Transaction
                $transaction                           = new Transaction();
                $transaction->user_id                  = $user_id;
                $transaction->currency_id              = $sessionValue['currency_id'];
                $transaction->payment_method_id        = Session::get('payment_method_id');
                $transaction->transaction_reference_id = $deposit->id;
                $transaction->transaction_type_id      = Deposit;
                $transaction->uuid                     = $uuid;
                $transaction->subtotal                 = $present_amount;
                $transaction->percentage               = @$feeInfo->charge_percentage ? @$feeInfo->charge_percentage : 0;
                $transaction->charge_percentage        = $deposit->charge_percentage;
                $transaction->charge_fixed             = $deposit->charge_fixed;
                $transaction->total                    = $sessionValue['amount'] + $total_fees;
                $transaction->status                   = 'Success';
                $transaction->save();

                //Wallet
                $chkWallet = Wallet::where(['user_id' => $user_id, 'currency_id' => $sessionValue['currency_id']])->first(['id', 'balance']);
                if (empty($chkWallet))
                {
                    $wallet              = new Wallet();
                    $wallet->user_id     = $user_id;
                    $wallet->currency_id = $sessionValue['currency_id'];
                    $wallet->balance     = $present_amount;
                    $wallet->is_default  = 'No';
                    $wallet->save();
                }
                else
                {
                    $chkWallet->balance = ($chkWallet->balance + $present_amount);
                    $chkWallet->save();
                }

                if (config('referral.is_active')) {
                    $refAwardData                    = [];
                    $refAwardData['userId']          = $user_id;
                    $refAwardData['currencyId']      = $sessionValue['currency_id'];
                    $refAwardData['currencyCode']    = $wallet->currency->code;
                    $refAwardData['presentAmount']   = $sessionValue['amount'];
                    $refAwardData['paymentMethodId'] = $sessionValue['payment_method'];
                    $this->helper->checkReferralAward($refAwardData);
                    $awardResponse = (new \App\Models\ReferralAward)->checkReferralAward($refAwardData);
                }

                DB::commit();
                session()->forget(['coinpaymentAmount', 'wallet_currency_id', 'method', 'payment_method_id', 'amount', 'mode', 'key', 'salt', 'transInfo', 'data']);
       
                // Send referralaward email/sms to users
                if (config('referral.is_active') && !empty($awardResponse)) {
                    (new \App\Models\ReferralAward)->sendReferralAwardNotification($awardResponse);
                }
                
                // Send deposit email or sms notification to admin
                $response = $this->helper->sendTransactionNotificationToAdmin('deposit', ['data' => $deposit]);

                $data['transaction'] = $transaction;
                //clearing session
                clearActionSession();
                return view('user_dashboard.deposit.success', $data);
            }
            catch (Exception $e)
            {
                DB::rollBack();
                session()->forget(['coinpaymentAmount', 'wallet_currency_id', 'method', 'payment_method_id', 'amount', 'mode', 'key', 'salt', 'transInfo']);
                clearActionSession();
                $this->helper->one_time_message('error', $e->getMessage());
                return redirect('deposit');
            }
        }
    }

    public function payumoneyPaymentFail(Request $request)
    {
        if ($_POST['status'] == 'failure')
        {
            clearActionSession();
            $this->helper->one_time_message('error', __('You have cancelled your payment'));
            return redirect('deposit');
        }
    }
    /* End of Payumoney */

    /* Start of CoinPayment */
    public function makeCoinPaymentTransaction(Request $request)
    {
        actionSessionCheck();

        $acceptedCoin = Session::get('coinPaymentTransaction')['coinList'];
        $acceptedCoinIso = array_column( $acceptedCoin, 'iso');

        if (empty($request->selected_coin) || !in_array($request->selected_coin, $acceptedCoinIso)) {
            $this->helper->one_time_message('error', __('Please select a crypto coin.'));
            return redirect('deposit');
        }

        // Payment method
        $currencyPaymentMethod = CurrencyPaymentMethod::where(['currency_id' => session('wallet_currency_id'), 'method_id' => Session::get('payment_method_id')])->where('activated_for', 'like', "%deposit%")->first(['method_data']);

        if (! empty($currencyPaymentMethod)) {
            $methodData = json_decode($currencyPaymentMethod->method_data);
        } else {
            $this->helper->one_time_message('error', __('Payment method not found.'));
            return redirect('deposit');
        }

        $this->coinPayment->Setup($methodData->private_key, $methodData->public_key);

        $uuid = unique_code();

        $transactionData = [
            'amount' => session('transInfo')['totalAmount'],
            'currency1' => Session::get('coinPaymentTransaction')['currencyCode'],
            'currency2' => $request->selected_coin,
            'buyer_email' => auth()->user()->email,
            'buyer_name' => auth()->user()->first_name .' '. auth()->user()->last_name,
            'item_name' => 'Deposit via coinpayment',
            'custom' => $uuid,
            'ipn_url' => url("coinpayment/check"),
            'cancel_url' => url("deposit/coinpayments/cancel"),
            'success_url' => url("deposit/payment_success"),
        ];

        $makeTransaction =  $this->coinPayment->CreateTransaction($transactionData);

        if ( $makeTransaction['error'] !== 'ok' ) {
            $this->helper->one_time_message('error', $makeTransaction['error']);
            return redirect('deposit');
        }

        $makeTransaction['payload'] = ['type' => Session::get('coinPaymentTransaction')['type'], 'currency' => Session::get('coinPaymentTransaction')['currencyCode']];

        $transactionInfo = $this->getCoinPaymentTransactionInfo($makeTransaction['result']['txn_id']);

        if ( $transactionInfo['error'] !== 'ok' ) {
			$this->helper->one_time_message('error', $transactionInfo['error']);
			return redirect('deposit');
		}

        Session::put('transactionDetails', $makeTransaction);
        Session::put('transactionInfo', $transactionInfo);

        if (auth()->check()) {

            $user = auth()->user();

            if ($transactionInfo['error'] == 'ok') {

                $data    = $transactionInfo['result'];
                $payload = $makeTransaction['payload'];

                $saved = [
                    'payment_id'         => $makeTransaction['result']['txn_id'],
                    'payment_address'    => $data['payment_address'],
                    'coin'               => $data['coin'],
                    'fiat'               => $payload['currency'],
                    'status_text'        => $data['status_text'],
                    'status'             => $data['status'],
                    'payment_created_at' => date('Y-m-d H:i:s', $data['time_created']),
                    'expired'            => date('Y-m-d H:i:s', $data['time_expires']),
                    'amount'             => $data['amountf'],
                    'confirms_needed'    => empty($makeTransaction['result']['confirms_needed']) ? 0 : $makeTransaction['result']['confirms_needed'],
                    'qrcode_url'         => empty($makeTransaction['result']['qrcode_url']) ? '' : $makeTransaction['result']['qrcode_url'],
                    'status_url'         => empty($makeTransaction['result']['status_url']) ? '' : $makeTransaction['result']['status_url'],
                ];

                if (isset($makeTransaction['payload']['type']) && $makeTransaction['payload']['type'] == "deposit")
                {
                    //insert into deposit
                    $payment_method_id = Session::get('payment_method_id');
                    $coinpaymentAmount = Session::get('coinpaymentAmount');

                    //charge percentage calculation
                    $curr       = Currency::where('code', $makeTransaction['payload']['currency'])->first(['id']);
                    $currencyId = $curr->id;
                    $feeInfo    = FeesLimit::where(['transaction_type_id' => Deposit, 'currency_id' => $currencyId, 'payment_method_id' => $payment_method_id])->first(['charge_percentage', 'charge_fixed']);

                    $p_calc     = $coinpaymentAmount * (@$feeInfo->charge_percentage / 100);

                    try
                    {
                        DB::beginTransaction();
                        //Deposit
                        $deposit                    = new Deposit();
                        $deposit->uuid              = $uuid;
                        $deposit->charge_percentage = @$feeInfo->charge_percentage ? $p_calc : 0;
                        $deposit->charge_fixed      = @$feeInfo->charge_fixed ? @$feeInfo->charge_fixed : 0;
                        $deposit->amount            = $coinpaymentAmount;
                        $deposit->status            = 'Pending';
                        $deposit->user_id           = auth()->user()->id;
                        $deposit->currency_id       = $currencyId;
                        $deposit->payment_method_id = $payment_method_id;
                        $deposit->save();

                        //Transaction
                        $transaction                           = new Transaction();
                        $transaction->user_id                  = auth()->user()->id;
                        $transaction->currency_id              = $currencyId;
                        $transaction->payment_method_id        = $payment_method_id;
                        $transaction->uuid                     = $uuid;
                        $transaction->transaction_reference_id = $deposit->id;
                        $transaction->transaction_type_id      = Deposit;
                        $transaction->subtotal                 = $coinpaymentAmount;
                        $transaction->percentage               = @$feeInfo->charge_percentage;
                        $transaction->charge_percentage        = $deposit->charge_percentage;
                        $transaction->charge_fixed             = $deposit->charge_fixed;
                        $transaction->total                    = $coinpaymentAmount + $deposit->charge_percentage + $deposit->charge_fixed;
                        $transaction->status                   = 'Pending';
                        $transaction->save();

                        //Wallet creation if request currency wallet does not exist
                        $wallet = Wallet::where(['user_id' => auth()->user()->id, 'currency_id' => $currencyId])->first(['id']);
                        if (empty($wallet))
                        {
                            $wallet              = new Wallet();
                            $wallet->user_id     = auth()->user()->id;
                            $wallet->currency_id = $currencyId;
                            $wallet->balance     = 0;
                            $wallet->is_default  = 'No';
                            $wallet->save();
                        }

                        $payload                   = empty($makeTransaction['payload']) ? [] : $makeTransaction['payload'];
                        $payload['deposit_id']     = $deposit->id;
                        $payload['transaction_id'] = $transaction->id;
                        $payload['uuid']           = $uuid;
                        $payload['receivedf']      = $data['receivedf'];
                        $payload['time_expires']   = $data['time_expires'];
                        $payload                   = json_encode($payload);
                        $saved['payload']          = $payload;
                        $user->coinpayment_transactions()->create($saved);

                        DB::commit();


                        // Mail To Admin
                        $response = $this->helper->sendTransactionNotificationToAdmin('deposit', ['data' => $deposit]);

                        session()->forget(['coinPaymentTransaction', 'wallet_currency_id', 'method', 'payment_method_id', 'amount', 'transInfo']);
                        clearActionSession();

                        return redirect('deposit/coinpayment-transaction-info');


                    }
                    catch (\Exception $e)
                    {
                        DB::rollBack();
                        session()->forget(['coinPaymentTransaction','wallet_currency_id', 'method', 'payment_method_id', 'amount', 'transInfo']);
                        clearActionSession();
                        $exception          = [];
                        $exception['error'] = json_encode($e->getMessage());
                        return $exception;
                    }
                }
            }
        }
    }

    public function getCoinPaymentTransactionInfo($txn_id)
    {
        return  $this->coinPayment->getTransactionInfo(['txid' => $txn_id]);
    }

    public function viewCoinpaymentTransactionInfo()
    {
        $data['transactionDetails'] = Session::get('transactionDetails');
        $data['transactionInfo'] = Session::get('transactionInfo');

        session()->forget(['transactionDetails', 'transactionInfo']);

        return view('user_dashboard.deposit.coinpayment_summery', $data);
    }

    public function coinpaymentCheckStatus(Request $request)
    {
        $responseArray = $request->all();

        if (htmlspecialchars($responseArray['ipn_type']) == 'api') {

            $txn_id = htmlspecialchars($responseArray['txn_id']);
            $custom_uuid = htmlspecialchars($responseArray['custom']);
            $status = htmlspecialchars(intval($responseArray['status']));
            $status_text = htmlspecialchars($responseArray['status_text']);

            $coinLog = CoinpaymentLogTrx::where(['status' => 0, 'payment_id' => $txn_id])->whereRaw('payload REGEXP ?', ['[[:<:]]' . $custom_uuid . '[[:>:]]'])->first(['id', 'payload', 'payment_id', 'status_text', 'status', 'confirmation_at']);

            $coinLogResponse = isset($coinLog->payload) ? json_decode($coinLog->payload) : null;

            if (!is_null($coinLogResponse) && isset($coinLogResponse->type)) {

                if (isset($coinLogResponse->deposit_id) && $coinLogResponse->type == 'deposit') {

                    $deposit = Deposit::where(['uuid' => $coinLogResponse->uuid])->first();

                    if ($status == 100 && $status_text == 'Complete') {

                        try {
                            DB::beginTransaction();

                            $coinLog->status_text     = $status_text;
                            $coinLog->status          = $status;
                            $coinLog->confirmation_at = ((INT) $status === 100) ? date('Y-m-d H:i:s', time()) : null;
                            $coinLog->save();

                            if (! empty($deposit)) {
                                $deposit->status = "Success";
                                $deposit->save();
                            }

                            $transaction = Transaction::where(['uuid' => $coinLogResponse->uuid, 'transaction_type_id' => Deposit])->first(['id', 'status']);

                            if (! empty($transaction)) {
                                $transaction->status = "Success";
                                $transaction->save();
                            }

                            $wallet = Wallet::where(['user_id' => $deposit->user_id, 'currency_id' => $deposit->currency_id])->first(['id', 'balance']);
                            $wallet->balance = ($wallet->balance + $deposit->amount);
                            $wallet->save();

                            if (config('referral.is_active')) {
                                $refAwardData                    = [];
                                $refAwardData['userId']          = $deposit->user_id;
                                $refAwardData['currencyId']      = $deposit->currency_id;
                                $refAwardData['currencyCode']    = $wallet->currency->code;
                                $refAwardData['presentAmount']   = $deposit->amount;
                                $refAwardData['paymentMethodId'] = $deposit->payment_method_id;
                                $this->helper->checkReferralAward($refAwardData);
                                $awardResponse = (new \App\Models\ReferralAward)->checkReferralAward($refAwardData);
                            }

                            DB::commit();

                            // Send referralaward email/sms to users
                            if (config('referral.is_active') && !empty($awardResponse)) {
                                (new \App\Models\ReferralAward)->sendReferralAwardNotification($awardResponse);
                            }

                            // Send deposit email or sms notificaion to admin
                            $response = $this->helper->sendTransactionNotificationToAdmin('deposit', ['data' => $deposit]);

                        } catch (Exception $e) {
                            DB::rollBack();
                            $this->helper->one_time_message('error', $e->getMessage());
                        }
                    }
                } else if (isset($coinLogResponse->merchant_payment_id) && ($coinLogResponse->type) == 'merchant') {
                    
                    $merchantPayment = MerchantPayment::where(['id' => $coinLogResponse->merchant_payment_id, 'gateway_reference' => $txn_id, 'uuid' => $custom_uuid])->first();

                    if ($status == 100 && $status_text == 'Complete') {

                        try {
                            DB::beginTransaction();

                            $coinLog->status_text     = $status_text;
                            $coinLog->status          = $status;
                            $coinLog->confirmation_at = ((INT) $status === 100) ? date('Y-m-d H:i:s', time()) : null;
                            $coinLog->save();

                            if (! empty($merchantPayment)) {
                                $merchantPayment->status = "Success";
                                $merchantPayment->save();
                            }

                            $merchantInfo = Merchant::find($merchantPayment->merchant_id, ['id', 'user_id', 'fee']);

                            if (! empty($merchantInfo)) {
                                $transaction = Transaction::where(['transaction_reference_id' => $coinLogResponse->merchant_payment_id, 'transaction_type_id' => Payment_Received, 'uuid' => $custom_uuid])->first(['id', 'status']);

                                if (! empty($transaction)) {
                                    $transaction->status = "Success";
                                    $transaction->save();
                                }
                            }

                            $merchantWallet = Wallet::where(['user_id' => $merchantInfo->user_id, 'currency_id' => $merchantPayment->currency_id])->first(['id', 'balance']);

                            if (! empty($merchantWallet)) {
                                $merchantWallet->balance = ($merchantWallet->balance + $merchantPayment->amount);
                                $merchantWallet->save();
                            }
                            DB::commit();

                            // Send mail to admin
                            $response = $this->helper->sendTransactionNotificationToAdmin('deposit', ['data' => $merchantPayment]);

                        } catch (Exception $e) {
                            DB::rollBack();
                            $this->helper->one_time_message('error', $e->getMessage());
                        }
                    }
                }
            }
        }
    }
    /* End of CoinPayment */

    /* Start of Payeer */
    public function payeerPayement()
    {
        $data['menu']       = 'deposit';
        $amount             = Session::get('amount');
        $transInfo          = Session::get('transInfo');
        $currency           = Currency::where(['id' => $transInfo['currency_id']])->first(['code']);
        $payeer_merchant_id = Session::get('payeer_merchant_id');
        $data['m_shop']     = $m_shop     = $payeer_merchant_id;
        $data['m_orderid']  = $m_orderid  = six_digit_random_number();
        $data['m_amount'] = $m_amount = number_format((float) $amount, 2, '.', ''); //Payeer might throw error, if 2 decimal place amount is not sent to Payeer server

        // $data['m_amount'] = $m_amount = "0.01"; // for test purpose

        $data['m_curr']             = $m_curr             = $currency->code;
        $data['form_currency_code'] = $form_currency_code = $currency->code;
        $data['m_desc']             = $m_desc             = base64_encode('Deposit');
        $payeer_secret_key          = Session::get('payeer_secret_key');
        $m_key                      = $payeer_secret_key;
        $arHash                     = array(
            $m_shop,
            $m_orderid,
            $m_amount,
            $m_curr,
            $m_desc,
        );
        $merchantDomain = Session::get('payeer_merchant_domain');
        $arParams       = array(
            'success_url' => url('/') . '/deposit/payeer/payment/confirm',
            'status_url'  => url('/') . '/deposit/payeer/payment/status',
            'fail_url'    => url('/') . '/deposit/payeer/payment/fail',
            'reference'   => array(
                'email' => auth()->user()->email,
                'name'  => auth()->user()->first_name . ' ' . auth()->user()->last_name,
            )
            
        );
        $cipher                = 'AES-256-CBC';
        $merchantEncryptionKey = Session::get('payeer_encryption_key');
        $key                   = md5($merchantEncryptionKey . $m_orderid);                                                            //key from (payeer.com->merchant settings->Key for encryption additional parameters)
        $m_params              = @urlencode(base64_encode(openssl_encrypt(json_encode($arParams), $cipher, $key, OPENSSL_RAW_DATA))); // this throws error if '@' symbol is not used
        $arHash[]              = $data['m_params']              = $m_params;
        $arHash[]              = $m_key;
        $data['sign']          = strtoupper(hash('sha256', implode(":", $arHash)));
        return view('user_dashboard.deposit.payeer', $data);

        // return redirect('deposit/payeer/payment/confirm');
    }

    public function payeerPayementConfirm(Request $request)
    {
        if (isset($request['m_operation_id']) && isset($request['m_sign']))
        {
            $payeer_secret_key = Session::get('payeer_secret_key');

            $m_key  = $payeer_secret_key;
            $arHash = array(
                $request['m_operation_id'],
                $request['m_operation_ps'],
                $request['m_operation_date'],
                $request['m_operation_pay_date'],
                $request['m_shop'],
                $request['m_orderid'],
                $request['m_amount'],
                $request['m_curr'],
                $request['m_desc'],
                $request['m_status'],
            );

            //additional parameters
            if (isset($request['m_params']))
            {
                $arHash[] = $request['m_params'];
            }

            $arHash[]  = $m_key;
            $sign_hash = strtoupper(hash('sha256', implode(':', $arHash)));

            if ($request['m_sign'] == $sign_hash && $request['m_status'] == 'success')
            {
                actionSessionCheck();
                $sessionValue = session('transInfo');

                $user_id           = auth()->user()->id;
                $uuid              = unique_code();
                $feeInfo           = $this->helper->getFeesLimitObject([], Deposit, $sessionValue['currency_id'], $sessionValue['payment_method'], null, ['charge_percentage', 'charge_fixed']);
                $p_calc            = $sessionValue['amount'] * (@$feeInfo->charge_percentage / 100);
                $total_fees        = $p_calc+@$feeInfo->charge_fixed;
                $payment_method_id = $sessionValue['payment_method'];
                $sessionAmount     = Session::get('amount');
                $amount            = $sessionAmount;

                try
                {
                    DB::beginTransaction();
                    //Deposit
                    $deposit                    = new Deposit();
                    $deposit->user_id           = auth()->user()->id;
                    $deposit->currency_id       = $sessionValue['currency_id'];
                    $deposit->payment_method_id = $payment_method_id;
                    $deposit->uuid              = $uuid;
                    $deposit->charge_percentage = @$feeInfo->charge_percentage ? $p_calc : 0;
                    $deposit->charge_fixed      = @$feeInfo->charge_fixed ? @$feeInfo->charge_fixed : 0;
                    $deposit->amount            = $present_amount            = ($amount - ($p_calc + (@$feeInfo->charge_fixed)));
                    $deposit->status            = 'Success';
                    $deposit->save();

                    //Transaction
                    $transaction                           = new Transaction();
                    $transaction->user_id                  = auth()->user()->id;
                    $transaction->currency_id              = $sessionValue['currency_id'];
                    $transaction->payment_method_id        = $payment_method_id;
                    $transaction->transaction_reference_id = $deposit->id;
                    $transaction->transaction_type_id      = Deposit;
                    $transaction->uuid                     = $uuid;
                    $transaction->subtotal                 = $present_amount;
                    $transaction->percentage               = @$feeInfo->charge_percentage ? @$feeInfo->charge_percentage : 0;
                    $transaction->charge_percentage        = $deposit->charge_percentage;
                    $transaction->charge_fixed             = $deposit->charge_fixed;
                    $transaction->total                    = $sessionValue['amount'] + $total_fees;
                    $transaction->status                   = 'Success';
                    $transaction->save();

                    //Wallet
                    $chkWallet = Wallet::where(['user_id' => auth()->user()->id, 'currency_id' => $sessionValue['currency_id']])->first(['id', 'balance']);
                    if (empty($chkWallet))
                    {
                        //if wallet does not exist, create it
                        $wallet              = new Wallet();
                        $wallet->user_id     = auth()->user()->id;
                        $wallet->currency_id = $sessionValue['currency_id'];
                        $wallet->balance     = $deposit->amount;
                        $wallet->is_default  = 'No';
                        $wallet->save();
                    }
                    else
                    {
                        //add deposit amount to existing wallet
                        $chkWallet->balance = ($chkWallet->balance + $deposit->amount);
                        $chkWallet->save();
                    }

                    if (config('referral.is_active')) {
                        $refAwardData                    = [];
                        $refAwardData['userId']          = $user_id;
                        $refAwardData['currencyId']      = $sessionValue['currency_id'];
                        $refAwardData['currencyCode']    = $wallet->currency->code;
                        $refAwardData['presentAmount']   = $present_amount;
                        $refAwardData['paymentMethodId'] = $payment_method_id;
                        $awardResponse = (new \App\Models\ReferralAward)->checkReferralAward($refAwardData);
                    }

                    DB::commit();

                    // Send referralaward email/sms to users
                    if (config('referral.is_active') && !empty($awardResponse)) {
                        (new \App\Models\ReferralAward)->sendReferralAwardNotification($awardResponse);
                    }

                    // Send deposit email or sms notification to admin
                    $response = $this->helper->sendTransactionNotificationToAdmin('deposit', ['data' => $deposit]);

                    $data['transaction'] = $transaction;

                    return \Redirect::route('deposit.payeer.success')->with(['data' => $data]);
                }
                catch (Exception $e)
                {
                    DB::rollBack();
                    session()->forget(['coinpaymentAmount', 'wallet_currency_id', 'method', 'payment_method_id', 'amount', 'payeer_merchant_id', 'payeer_secret_key',
                    'payeer_encryption_key', 'payeer_merchant_domain','transInfo']);
                    $this->helper->one_time_message('error', $e->getMessage());
                    return redirect('deposit');
                }
            }
            else
            {
                session()->forget(['coinpaymentAmount', 'wallet_currency_id', 'method', 'payment_method_id', 'amount', 'payeer_merchant_id', 'payeer_secret_key',
                'payeer_encryption_key', 'payeer_merchant_domain','transInfo']);
                clearActionSession();
                $this->helper->one_time_message('error', __('Please try again later!'));
                return back();
            }
        }
    }

    public function payeerPayementSuccess()
    {
        if (empty(session('data')))
        {
            return redirect('deposit');
        }
        else
        {
            $data['transaction'] = session('data')['transaction'];

            //clearing session
            session()->forget(['coinpaymentAmount', 'wallet_currency_id', 'method', 'payment_method_id', 'amount', 'payeer_merchant_id', 'payeer_secret_key',
                'payeer_encryption_key', 'payeer_merchant_domain','transInfo', 'data']);
            clearActionSession();
            return view('user_dashboard.deposit.success', $data);
        }
    }

    public function payeerPayementStatus(Request $request)
    {
        return 'Payeer Status Page =>'.$request->all();
    }

    public function payeerPayementFail()
    {
        $this->helper->one_time_message('error', __('You have cancelled your payment'));
        return redirect('deposit');
    }
    /* End of Payeer */

    /* Start of Bank Payment Method */
    public function bankPaymentConfirm(Request $request)
    {
        actionSessionCheck();
        $sessionValue = session('transInfo');
        if (empty(session('transInfo'))) {
            return redirect('deposit');
        }
        try {
            DB::beginTransaction();
            if ($request->hasFile('attached_file')) {
                $fileName     = $request->file('attached_file');
                $originalName = $fileName->getClientOriginalName();
                $uniqueName   = strtolower(time() . '.' . $fileName->getClientOriginalExtension());
                $file_extn    = strtolower($fileName->getClientOriginalExtension());
                $path         = 'uploads/files/bank_attached_files';
                $uploadPath   = public_path($path);
                $fileName->move($uploadPath, $uniqueName);

                $file               = new File();
                $file->user_id      = auth()->user()->id;
                $file->filename     = $uniqueName;
                $file->originalname = $originalName;
                $file->type         = $file_extn;
                $file->save();
            }
            $depositConfirm = Deposit::success($sessionValue['currency_id'], $sessionValue['payment_method'], auth()->user()->id, $sessionValue, "Pending", "bank", $file->id, $request->bank);
            $wallet = Wallet::where(['user_id' => auth()->user()->id, 'currency_id' => $sessionValue['currency_id']])->first(['id']);
            if (empty($wallet)) {
                $wallet = Wallet::createWallet(auth()->user()->id, $sessionValue['currency_id']);
            }
            DB::commit();
            $response = $this->helper->sendTransactionNotificationToAdmin('deposit', ['data' => $depositConfirm['deposit']]);
            $data['transaction'] = $depositConfirm['transaction'];
            return \Redirect::route('deposit.bank.success')->with(['data' => $data]);
        } catch (Exception $e) {
            DB::rollBack();
            session()->forget(['coinpaymentAmount', 'wallet_currency_id', 'method', 'payment_method_id', 'amount', 'transInfo']);
            clearActionSession();
            $this->helper->one_time_message('error', $e->getMessage());
            return redirect('deposit');
        }
    }

    public function bankPaymentSuccess()
    {
        if (empty(session('data')))
        {
            return redirect('deposit');
        }
        else
        {
            $data['transaction'] = session('data')['transaction'];

            //clearing session
            session()->forget(['coinpaymentAmount', 'wallet_currency_id', 'method', 'payment_method_id', 'amount', 'transInfo', 'data']);
            clearActionSession();
            return view('user_dashboard.deposit.success', $data);
        }
    }
    /* End of Bank Payment Method */


    // Mobile Money Start
    public function getMobileMoneyDetailOnChange(Request $request)
    {
        $mobileMoney = \App\Models\MobileMoney::with('file:id,filename')->where(['id' => $request->mobilemoney])->first(['mobilemoney_name', 'holder_name', 'merchant_code', 'file_id']);

        if ($mobileMoney) {
            $data['status'] = true;
            $data['mobileMoney'] = $mobileMoney;

            if (!empty($mobileMoney->file_id)) {
                $data['mobilemoney_logo'] = $mobileMoney->file->filename;
            }
        } else {
            $data['status'] = false;
            $data['mobileMoney'] = __('MobileMoney not found.');
        }
        return $data;
    }

    public function mobileMoneyPaymentConfirm(Request $request)
    {
        actionSessionCheck();

        $sessionValue = session('transInfo');
        $feeInfo = (new Common())->getFeesLimitObject([], Deposit, $sessionValue['currency_id'], $sessionValue['payment_method'], null, ['charge_percentage', 'charge_fixed']);
        $uuid = unique_code();
        $p_calc = $sessionValue['amount'] * ($feeInfo->charge_percentage / 100);

        try {
            DB::beginTransaction();

            //File
            if ($request->hasFile('attached_file')) {

                $fileName = $request->file('attached_file');
                $uniqueName = uploadImage($fileName, 'public/uploads/files/mobilemoney_attached_files/');

                //File
                $file               = new File();
                $file->user_id      = auth()->user()->id;
                $file->filename     = $uniqueName;
                $file->originalname = $fileName->getClientOriginalName();
                $file->type         = strtolower($fileName->getClientOriginalExtension());
                $file->save();
            }

            //Deposit
            $deposit                    = new Deposit();
            $deposit->user_id           = auth()->user()->id;
            $deposit->currency_id       = $sessionValue['currency_id'];
            $deposit->payment_method_id = $sessionValue['payment_method'];
            $deposit->mobilemoney_id    = $request->mobilemoney;
            $deposit->file_id           = $file->id;
            $deposit->uuid              = $uuid;
            $deposit->charge_percentage = $feeInfo->charge_percentage ? $p_calc : 0;
            $deposit->charge_fixed      = $feeInfo->charge_fixed ? $feeInfo->charge_fixed : 0;
            $deposit->amount            = $sessionValue['amount'];
            $deposit->status            = 'Pending';
            $deposit->save();

            //Transaction
            $transaction                           = new Transaction();
            $transaction->user_id                  = auth()->user()->id;
            $transaction->currency_id              = $sessionValue['currency_id'];
            $transaction->payment_method_id        = $sessionValue['payment_method'];
            $transaction->mobilemoney_id           = $request->mobilemoney;
            $transaction->file_id                  = $file->id;
            $transaction->uuid                     = $uuid;
            $transaction->transaction_reference_id = $deposit->id;
            $transaction->transaction_type_id      = Deposit;
            $transaction->subtotal                 = $deposit->amount;
            $transaction->percentage               = $feeInfo->charge_percentage ? $feeInfo->charge_percentage : 0;
            $transaction->charge_percentage        = $deposit->charge_percentage;
            $transaction->charge_fixed             = $deposit->charge_fixed;
            $transaction->total                    = $sessionValue['amount'] + $deposit->charge_percentage + $deposit->charge_fixed;
            $transaction->status                   = 'Pending';
            $transaction->save();

            //Wallet
            $wallet = Wallet::where(['user_id' => auth()->user()->id, 'currency_id' => $sessionValue['currency_id']])->first(['id']);
            if (empty($wallet)) {
                $wallet              = new Wallet();
                $wallet->user_id     = auth()->user()->id;
                $wallet->currency_id = $sessionValue['currency_id'];
                $wallet->balance     = 0;
                $wallet->is_default  = 'No';
                $wallet->save();
            }
            DB::commit();

            // Send mail to admin
            $response = (new Common())->sendTransactionNotificationToAdmin('deposit', ['data' => $deposit]);
            $data['transaction'] = $transaction;

            return \Redirect::route('deposit.mobilemoney.success')->with(['data' => $data]);
        } catch (Exception $e) {
            DB::rollBack();
            session()->forget(['coinpaymentAmount', 'wallet_currency_id', 'method', 'payment_method_id', 'amount', 'transInfo']);
            clearActionSession();
            (new Common())->one_time_message('error', $e->getMessage());
            return redirect('deposit');
        }
    }

    public function mobileMoneyPaymentSuccess()
    {
        if (empty(session('data'))) {
            return redirect('deposit');
        } else {
            $data['transaction'] = session('data')['transaction'];

            //clearing session
            session()->forget(['coinpaymentAmount', 'wallet_currency_id', 'method', 'payment_method_id', 'amount', 'transInfo', 'data']);
            clearActionSession();
            return view('user_dashboard.deposit.success', $data);
        }
    }

    public function depositPrintPdf($trans_id)
    {
        $data['transactionDetails'] = Transaction::with(['payment_method:id,name', 'currency:id,symbol,code'])
            ->where(['id' => $trans_id])
            ->first(['uuid', 'created_at', 'status', 'currency_id', 'payment_method_id', 'subtotal', 'charge_percentage', 'charge_fixed', 'total']);

        $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp']);
        $mpdf = new \Mpdf\Mpdf([
            'mode'        => 'utf-8',
            'format'      => 'A3',
            'orientation' => 'P',
        ]);
        $mpdf->autoScriptToLang         = true;
        $mpdf->autoLangToFont           = true;
        $mpdf->allow_charset_conversion = false;
        $mpdf->SetJS('this.print();');
        $mpdf->WriteHTML(view('user_dashboard.deposit.depositPaymentPdf', $data));
        $mpdf->Output('sendMoney_' . time() . '.pdf', 'I');
    }
}
