<?php

namespace App\Http\Controllers\Admin;

use Hash, Validator, Session, DB, Config, Exception;
use App\DataTables\Admin\{AdminsDataTable, 
    EachUserTransactionsDataTable,
    UsersDataTable
};
use App\Http\Controllers\Users\EmailController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Helpers\Common;
use Illuminate\Support\Str;
use App\Models\{ActivityLog,
    CryptoProvider,
    EmailTemplate,
    PaymentMethod,
    Transaction,
    Withdrawal,
    FeesLimit,
    Currency,
    RoleUser,
    Dispute,
    Deposit,
    Wallet,
    Ticket,
    QrCode,
    Admin,
    User,
    Role
};

class UserController extends Controller
{
    protected $helper;
    protected $email;
    protected $currency;
    protected $user;

    public function __construct()
    {
        $this->helper = new Common();
        $this->email = new EmailController();
        $this->currency = new Currency();
        $this->user = new User();
    }

    public function index(UsersDataTable $dataTable)
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'users_list';
        return $dataTable->render('admin.users.index', $data);
    }

    public function create()
    {
        $data['menu'] = 'users';
        $data['sub_menu'] = 'users_list';
        $data['roles'] = Role::select('id', 'display_name')->where('user_type', "User")->get();

        return view('admin.users.create', $data);
    }

    public function store(Request $request)
    {
        if ($request->isMethod('post')) {
            $rules = array(
                'first_name'            => 'required|max:30|regex:/^[a-zA-Z\s]*$/',
                'last_name'             => 'required|max:30|regex:/^[a-zA-Z\s]*$/',
                'email'                 => 'required|unique:users,email',
                'password'              => 'required|min:6|confirmed',
                'password_confirmation' => 'required|min:6',
                'status'                => 'required',
            );

            $fieldNames = array(
                'first_name'            => 'First Name',
                'last_name'             => 'Last Name',
                'email'                 => 'Email',
                'password'              => 'Password',
                'password_confirmation' => 'Confirm Password',
                'status'                => 'Status',
            );
            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            } else {

                try {
                    DB::beginTransaction();

                    // Create user
                    $user = $this->user->createNewUser($request, 'admin');

                    // Assigning user_type and role id to new user
                    RoleUser::insert(['user_id' => $user->id, 'role_id' => $user->role_id, 'user_type' => 'User']);

                    // Create user detail
                    $this->user->createUserDetail($user->id);

                    // Create user's default wallet
                    $this->user->createUserDefaultWallet($user->id, settings('default_currency'));

                    // Create wallets that are allowed by admin
                    if (settings('allowed_wallets') != 'none') {
                        $this->user->createUserAllowedWallets($user->id, settings('allowed_wallets'));
                    }

                    if (isActive('BlockIo') && CryptoProvider::getStatus('BlockIo') == 'Active' && $user->status == 'Active') {
                        $generateUserCryptoWalletAddress = $this->user->generateUserBlockIoWalletAddress($user);
                        if ($generateUserCryptoWalletAddress['status'] == 401) {
                            DB::rollBack();
                            $this->helper->one_time_message('error', $generateUserCryptoWalletAddress['message']);
                            return redirect(Config::get('adminPrefix').'/users');
                        }
                    }

                    //Entry for User's QrCode Generation - starts
                    $this->saveUserQrCode($user);

                    $userEmail          = $user->email;
                    $userFormattedPhone = $user->formattedPhone;

                    // Process Registered User Transfers
                    $this->user->processUnregisteredUserTransfers($userEmail, $userFormattedPhone, $user, settings('default_currency'));

                    // Process Registered User Request Payments
                    $this->user->processUnregisteredUserRequestPayments($userEmail, $userFormattedPhone, $user, settings('default_currency'));

                    // Email verification
                    if (!$user->user_detail->email_verification) {
                        if (preference('verification_mail') == "Enabled") {
                            if (checkAppMailEnvironment()) {
                                $emainVerificationArr = $this->user->processUserEmailVerification($user);
                                try {
                                    $this->email->sendEmail($emainVerificationArr['email'], $emainVerificationArr['subject'], $emainVerificationArr['message']);

                                    DB::commit();
                                    $this->helper->one_time_message('success', __('An email has been sent to :x with verification code.', ['x' =>  $user->email]));
                                    return redirect(Config::get('adminPrefix').'/users');
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    $this->helper->one_time_message('error', $e->getMessage());
                                    return redirect(Config::get('adminPrefix').'/users');
                                }
                            }
                        }
                    }
                    DB::commit();
                    $this->helper->one_time_message('success', __('The :x has been successfully saved.', ['x' => __('user')]));
                    return redirect(Config::get('adminPrefix').'/users');
                } catch (Exception $e) {
                    DB::rollBack();
                    $this->helper->one_time_message('error', $e->getMessage());
                    return redirect(Config::get('adminPrefix').'/users');
                }
            }
        }
    }

    public function edit($id)
    {
        $data['menu'] = 'users';
        $data['sub_menu'] = 'users_list';
        $data['user_tab_menu'] = 'user_profile';

        $data['users'] = User::find($id);

        $data['roles'] = Role::select('id', 'display_name')->where('user_type', "User")->get();
        if(!g_c_v() && a_u_c_v()) {
            Session::flush();
            return view('vendor.installer.errors.admin');
        }

        return view('admin.users.edit', $data);
    }

    public function update(Request $request)
    {
        if ($request->isMethod('post')) {
            $rules = array(
                'first_name' => 'required|max:30|regex:/^[a-zA-Z\s]*$/',
                'last_name' => 'required|max:30|regex:/^[a-zA-Z\s]*$/',
                'email' => 'required|email|unique:users,email,' . $request->id,
                'password' => 'nullable|min:6|confirmed',
                'password_confirmation' => 'nullable|min:6',
                'status' => 'required',
            );

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            } else {

                try {
                    DB::beginTransaction();
                    $user = User::find($request->id);
                    $user->first_name = $request->first_name;
                    $user->last_name  = $request->last_name;
                    $user->email      = $request->email;
                    $user->role_id    = $request->role;

                    $formattedPhone = ltrim($request->phone, '0');
                    if (!empty($request->phone)) {
                        $user->phone          = preg_replace("/[\s-]+/", "", $formattedPhone);
                        $user->defaultCountry = $request->user_defaultCountry;
                        $user->carrierCode    = $request->user_carrierCode;
                        $user->formattedPhone = $request->formattedPhone;
                        /**/
                    } else {
                        $user->phone          = null;
                        $user->defaultCountry = null;
                        $user->carrierCode    = null;
                        $user->formattedPhone = null;
                    }

                    if (!is_null($request->password) && !is_null($request->password_confirmation)) {
                        $user->password = \Hash::make($request->password);
                    }

                    // Send mail to user for Status change
                    if ($request->status != $user->status) {
                        //update user status
                        $user->status = $request->status;

                        $englishSenderLanginfo = EmailTemplate::where(['temp_id' => 29, 'lang' => 'en', 'type' => 'email'])->select('subject', 'body')->first();
                        $sender_info = EmailTemplate::where(['temp_id' => 29, 'language_id' => settings('default_language'), 'type' => 'email'])->select('subject', 'body')->first();

                        if (!empty($sender_info->subject) && !empty($sender_info->body)) {
                            $sender_subject = $sender_info->subject;
                            $sender_msg = $sender_info->body;
                        } else {
                            $sender_subject = $englishSenderLanginfo->subject;
                            $sender_msg = $englishSenderLanginfo->body;
                        }

                        $sender_msg = str_replace(
                            ['{user}', '{status}', '{soft_name}'],
                            [getColumnValue($user), $user->status, settings('name')], 
                            $sender_info->body
                        );

                        if (checkAppMailEnvironment()) {
                            try {
                                $this->email->sendEmail($user->email, $sender_subject, $sender_msg);
                            } catch (Exception $e) {
                                DB::rollBack();
                                $this->helper->one_time_message('error', $e->getMessage());
                                return redirect(Config::get('adminPrefix').'/users');
                            }
                        }
                    }
                    $user->save();

                    RoleUser::where(['user_id' => $request->id, 'user_type' => 'User'])->update(['role_id' => $request->role]); //by tuhin

                    DB::commit();

                    $this->helper->one_time_message('success', __('The :x has been successfully saved.', ['x' => __('user')]));
                    return redirect(Config::get('adminPrefix').'/users');
                } catch (Exception $e) {
                    DB::rollBack();
                    $this->helper->one_time_message('error', $e->getMessage());
                    return redirect(Config::get('adminPrefix').'/users');
                }
            }
        }
    }

    /* Start of Admin Depsosit */
    public function eachUserDeposit($id, Request $request)
    {
        setActionSession();

        $data['menu']     = 'users';
        $data['sub_menu'] = 'users_list';
        $data['user_tab_menu'] = 'user_profile';

        $data['users'] = User::find($id, ['id', 'first_name', 'last_name']);
        $data['payment_met']     = $payment_met     = PaymentMethod::where(['name' => 'Mts', 'status' => 'Active'])->first(['id', 'name']);
        $data['active_currency'] = $activeCurrency = Currency::where(['status' => 'Active'])->get(['id', 'status', 'code', 'type']);
        $feesLimitCurrency       = FeesLimit::where(['transaction_type_id' => Deposit, 'payment_method_id' => $payment_met->id, 'has_transaction' => 'Yes'])->get(['currency_id', 'has_transaction']);
        $data['activeCurrencyList'] = $this->currencyList($activeCurrency, $feesLimitCurrency);

        if ($request->isMethod('post')) {

            $userStatus = User::where('id', $id)->value('status');
            if ($userStatus == 'Inactive' || $userStatus == 'Suspended') {
                $this->helper->one_time_message('error', __('The user is :x.', ['x' => $userStatus]));
                return redirect(Config::get('adminPrefix') . '/users/deposit/create/' . $id);
            }
            
            $currency = Currency::where(['id' => $request->currency_id, 'status' => 'Active'])->first(['symbol']);
            $request['currSymbol'] = $currency->symbol;
            $amount = $request->amount;
            $request['totalAmount'] = $amount + $request->fee;
            session(['transInfo' => $request->all()]);
            $data['transInfo'] = $transInfo = $request->all();

            //check amount and limit
            $feesDetails = FeesLimit::where(['transaction_type_id' => Deposit, 'currency_id' => $request->currency_id, 'payment_method_id' => $transInfo['payment_method'], 'has_transaction' => 'Yes'])
                ->first(['min_limit', 'max_limit']);

            if ($feesDetails->max_limit == null) {
                if (($amount < $feesDetails->min_limit)) {
                    $data['error'] = 'Minimum amount ' . formatNumber($feesDetails->min_limit);
                    $this->helper->one_time_message('error', $data['error']);
                    return view('admin.users.deposit.create', $data);
                }
            } else {
                if (($amount < $feesDetails->min_limit) || ($amount > $feesDetails->max_limit)) {
                    $data['error'] = __('Minimum amount :x and Maximum amount :y', ['x' => formatNumber($feesDetails->min_limit), 'y' => formatNumber($feesDetails->max_limit)]);
                    $this->helper->one_time_message('error', $data['error']);
                    return view('admin.users.deposit.create', $data);
                }
            }
            return view('admin.users.deposit.confirmation', $data);
        }
        return view('admin.users.deposit.create', $data);
    }

    //Extended function below - deposit
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
    /* End of Admin Depsosit */

    public function eachUserDepositSuccess(Request $request)
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'users_list';
        $data['user_tab_menu'] = 'user_profile';

        $user_id = $request->user_id;

        //Check Session - starts
        $sessionValue = session('transInfo');
        if (empty($sessionValue))
        {
            return redirect(Config::get('adminPrefix')."/users/deposit/create/" . $user_id);
        }
        //Check Session - ends

        actionSessionCheck();

        $amount  = $sessionValue['amount'];
        $uuid    = unique_code();
        $feeInfo = FeesLimit::where(['transaction_type_id' => Deposit, 'currency_id' => $sessionValue['currency_id'], 'payment_method_id' => $sessionValue['payment_method']])
            ->first(['charge_percentage', 'charge_fixed']);
        //charge percentage calculation
        $p_calc = (($amount) * (@$feeInfo->charge_percentage) / 100);

        try
        {
            DB::beginTransaction();
            //Deposit
            $deposit                    = new Deposit();
            $deposit->user_id           = $user_id;
            $deposit->currency_id       = $sessionValue['currency_id'];
            $deposit->payment_method_id = $sessionValue['payment_method'];
            $deposit->uuid              = $uuid;
            $deposit->charge_percentage = @$feeInfo->charge_percentage ? $p_calc : 0;
            $deposit->charge_fixed      = @$feeInfo->charge_fixed ? @$feeInfo->charge_fixed : 0;
            $deposit->amount            = $amount;
            $deposit->status            = 'Success';
            $deposit->save();

            //Transaction
            $transaction                           = new Transaction();
            $transaction->user_id                  = $user_id;
            $transaction->currency_id              = $sessionValue['currency_id'];
            $transaction->payment_method_id        = $sessionValue['payment_method'];
            $transaction->transaction_reference_id = $deposit->id;
            $transaction->transaction_type_id      = Deposit;
            $transaction->uuid                     = $uuid;
            $transaction->subtotal                 = $amount;
            $transaction->percentage               = @$feeInfo->charge_percentage ? @$feeInfo->charge_percentage : 0;
            $transaction->charge_percentage        = $deposit->charge_percentage;
            $transaction->charge_fixed             = $deposit->charge_fixed;
            $transaction->total                    = $amount + $deposit->charge_percentage + $deposit->charge_fixed;
            $transaction->status                   = 'Success';
            $transaction->save();

            //Wallet
            $wallet = Wallet::where(['user_id' => $user_id, 'currency_id' => $sessionValue['currency_id']])->first(['id', 'balance']);
            if (empty($wallet))
            {
                $createWallet              = new Wallet();
                $createWallet->user_id     = $user_id;
                $createWallet->currency_id = $sessionValue['currency_id'];
                $createWallet->balance     = $amount;
                $createWallet->is_default  = 'No';
                $createWallet->save();
            }
            else
            {
                $wallet->balance = ($wallet->balance + $amount);
                $wallet->save();
            }

            if (config('referral.is_active')) {
                $refAwardData                    = [];
                $refAwardData['userId']          = $deposit->user_id;
                $refAwardData['currencyId']      = $deposit->currency_id;
                $refAwardData['currencyCode']    = $deposit->currency->code;
                $refAwardData['presentAmount']   = $deposit->amount;
                $refAwardData['paymentMethodId'] = $deposit->payment_method_id;
                $awardResponse = (new \App\Models\ReferralAward)->checkReferralAward($refAwardData);
            }

            DB::commit();

            // Send referralaward email/sms to users
            if (config('referral.is_active') && !empty($awardResponse)) {
                (new \App\Models\ReferralAward)->sendReferralAwardNotification($awardResponse);
            }

            // Send deposit email to admin
            if (checkAppMailEnvironment())
            {
                $english_deposit_email_temp = EmailTemplate::where(['temp_id' => 30, 'lang' => 'en', 'type' => 'email'])->select('subject', 'body')->first();
                $deposit_email_temp         = EmailTemplate::where([
                    'temp_id'     => 30,
                    'language_id' => Session::get('default_language'),
                    'type'        => 'email',
                ])->select('subject', 'body')->first();

                if (!empty($english_deposit_email_temp->subject) && !empty($english_deposit_email_temp->body))
                {
                    $d_success_sub = str_replace('{uuid}', $deposit->uuid, $english_deposit_email_temp->subject);
                    $d_success_msg = str_replace('{user_id}', $deposit->user->first_name . ' ' . $deposit->user->last_name, $english_deposit_email_temp->body);
                }
                else
                {
                    $d_success_sub = str_replace('{uuid}', $deposit->uuid, $deposit_email_temp->subject);
                    $d_success_msg = str_replace('{user_id}', $deposit->user->first_name . ' ' . $deposit->user->last_name, $deposit_email_temp->body);
                }

                $d_success_msg = str_replace('{amount}', moneyFormat(optional($deposit->currency)->symbol, formatNumber($deposit->amount)), $d_success_msg);
                $d_success_msg = str_replace('{created_at}', dateFormat($deposit->created_at, $deposit->user_id), $d_success_msg);
                $d_success_msg = str_replace('{uuid}', $deposit->uuid, $d_success_msg);
                $d_success_msg = str_replace('{code}', $deposit->currency->code, $d_success_msg);
                $d_success_msg = str_replace('{amount}', moneyFormat(optional($deposit->currency)->symbol, formatNumber($deposit->amount)), $d_success_msg);
                $d_success_msg = str_replace('{fee}', moneyFormat(optional($deposit->currency)->symbol, formatNumber($deposit->charge_fixed + $deposit->charge_percentage)), $d_success_msg);
                $d_success_msg = str_replace('{soft_name}', settings('name'), $d_success_msg);

                if (checkAppMailEnvironment())
                {
                    $this->email->sendEmail($deposit->user->email, $d_success_sub, $d_success_msg);
                }
            }
            
            // Send deposit sms to admin
            if (checkAppSmsEnvironment())
            {
                $payoutMessage = 'Amount ' . moneyFormat(optional($deposit->currency)->symbol, formatNumber($deposit->amount)) . ' was deposited by System Administrator.';
                if (!empty($deposit->user->formattedPhone)) {
                    sendSMS($deposit->user->formattedPhone, $payoutMessage);
                }
            }

            $data['transInfo']['currency_id'] = $sessionValue['currency_id'];
            $data['transInfo']['currSymbol'] = $transaction->currency->symbol;
            $data['transInfo']['subtotal'] = $transaction->subtotal;
            $data['transInfo']['id'] = $transaction->id;
            $data['users'] = User::find($user_id, ['id']);
            $data['name'] = $sessionValue['fullname'];

            Session::forget('transInfo');
            clearActionSession();
            return view('admin.users.deposit.success', $data);
        }
        catch (Exception $e)
        {
            DB::rollBack();
            Session::forget('transInfo');
            clearActionSession();
            $this->helper->one_time_message('error', $e->getMessage());
            return redirect(Config::get('adminPrefix')."/users/deposit/create/" . $user_id);
        }
    }

    public function eachUserdepositPrintPdf($transaction_id)
    {
        $data['transactionDetails'] = Transaction::with(['payment_method:id,name', 'currency:id,symbol'])
            ->where(['id' => $transaction_id])
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
        $mpdf->WriteHTML(view('admin.users.deposit.depositPrintPdf', $data));
        $mpdf->Output('deposit_' . time() . '.pdf', 'I');
    }

    /* Start of Admin Withdraw */
    public function eachUserWithdraw($id, Request $request)
    {
        setActionSession();

        $data['menu']     = 'users';
        $data['sub_menu'] = 'users_list';
        $data['user_tab_menu'] = 'user_profile';

        $data['users'] = $users = User::find($id, ['id', 'first_name', 'last_name']);

        if ($request->isMethod('post')) {

            $userStatus = User::where('id', $id)->value('status');
            if ($userStatus == 'Inactive' || $userStatus == 'Suspended') {
                $this->helper->one_time_message('error', __('The user is :x.', ['x' => $userStatus]));
                return redirect(Config::get('adminPrefix') . '/users/withdraw/create/' . $id);
            }

            $amount = $request->amount;
            $currency = Currency::where(['id' => $request->currency_id])->first(['symbol']);
            $request['currSymbol'] = $currency->symbol;
            $request['totalAmount'] = $request->amount + $request->fee;
            session(['transInfo' => $request->all()]);
            $data['transInfo'] = $request->all();

            //backend validation starts
            $request['transaction_type_id'] = Withdrawal;
            $request['currency_id']         = $request->currency_id;
            $request['payment_method_id']   = $request->payment_method;
            $amountFeesLimitCheck           = $this->amountFeesLimitCheck($request);

            if ($amountFeesLimitCheck) {
                if ($amountFeesLimitCheck->getData()->success->status == 200) {
                    if ($amountFeesLimitCheck->getData()->success->totalAmount > $amountFeesLimitCheck->getData()->success->balance) {
                        $data['error'] = "Insufficient Balance!";
                        $this->helper->one_time_message('error', $data['error']);
                        return view('admin.users.withdraw.create', $data);
                    }
                } elseif ($amountFeesLimitCheck->getData()->success->status == 401) {
                    $data['error'] = $amountFeesLimitCheck->getData()->success->message;
                    $this->helper->one_time_message('error', $data['error']);
                    return view('admin.users.withdraw.create', $data);
                }
            }
            //backend valdation ends
            return view('admin.users.withdraw.confirmation', $data);
        }

        $data['payment_met'] = $paymentMethod = PaymentMethod::where(['name' => 'Mts'])->first(['id', 'name']);
        $paymentMethodId = $paymentMethod->id;
        $data['wallets'] = $users->wallets()->whereHas('active_currency', function ($q) use ($paymentMethodId)
        {
            $q->whereHas('fees_limit', function ($query) use ($paymentMethodId)
            {
                $query->where('has_transaction', 'Yes')->where('transaction_type_id', Withdrawal)->where('payment_method_id', $paymentMethodId);
            });
        })
        ->with(['active_currency:id,code,type', 'active_currency.fees_limit:id,currency_id'])
        ->get(['id', 'currency_id']);

        return view('admin.users.withdraw.create', $data);
    }

    public function amountFeesLimitCheck(Request $request)
    {
        // dd($request->currency_id);
        $amount = $request->amount;
        $feesDetails = FeesLimit::where(['transaction_type_id' => $request->transaction_type_id, 'currency_id' => $request->currency_id, 'payment_method_id' => $request->payment_method_id])
            ->first(['min_limit', 'max_limit', 'charge_percentage', 'charge_fixed']);
        $wallet = Wallet::where(['currency_id' => $request->currency_id, 'user_id' => $request->user_id])->first(['balance']);

        if ($request->transaction_type_id == Withdrawal) {
            //Wallet Balance Limit Check Starts here
            $checkAmount = $amount + $feesDetails->charge_fixed + (($amount * $feesDetails->charge_percentage) / 100);
            if (@$wallet)
            {
                if ((@$checkAmount) > (@$wallet->balance) || (@$wallet->balance < 0))
                {
                    $success['message'] = "Insufficient Balance!";
                    $success['status']  = '401';
                    return response()->json(['success' => $success]);
                }
            }
            //Wallet Balance Limit Check Ends here
        }

        //Amount Limit Check Starts here
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
            $success['pFeesHtml']      = formatNumber($feesPercentage, $request->currency_id);
            $success['fFees']          = $feesFixed;
            $success['fFeesHtml']      = formatNumber($feesFixed, $request->currency_id);
            $success['min']            = 0;
            $success['max']            = 0;
            $success['balance']        = 0;
        }
        else
        {
            if (@$feesDetails->max_limit == null)
            {
                if ((@$amount < @$feesDetails->min_limit))
                {
                    $success['message'] = __('Minimum amount :x', ['x' => formatNumber($feesDetails->min_limit, $request->currency_id)]);
                    $success['status']  = '401';
                }
                else
                {
                    $success['status'] = 200;
                }
            }
            else
            {
                if ((@$amount < @$feesDetails->min_limit) || (@$amount > @$feesDetails->max_limit))
                {
                    $success['message'] = 'Minimum amount ' . formatNumber($feesDetails->min_limit, $request->currency_id) . ' and Maximum amount ' . formatNumber($feesDetails->max_limit, $request->currency_id);
                    $success['status']  = '401';
                }
                else
                {
                    $success['status'] = 200;
                }
            }
            $feesPercentage            = $amount * ($feesDetails->charge_percentage / 100);
            $feesFixed                 = $feesDetails->charge_fixed;
            $totalFess                 = $feesPercentage + $feesFixed;
            $totalAmount               = $amount + $totalFess;
            $success['feesPercentage'] = $feesPercentage;
            $success['feesFixed']      = $feesFixed;
            $success['totalFees']      = $totalFess;
            $success['totalFeesHtml']  = formatNumber($totalFess, $request->currency_id);
            $success['totalAmount']    = $totalAmount;
            $success['pFees']          = $feesDetails->charge_percentage;
            $success['pFeesHtml']      = formatNumber($feesDetails->charge_percentage, $request->currency_id);
            $success['fFees']          = $feesDetails->charge_fixed;
            $success['fFeesHtml']      = formatNumber($feesDetails->charge_fixed, $request->currency_id);
            $success['min']            = $feesDetails->min_limit;
            $success['max']            = $feesDetails->max_limit;
            $success['balance']        = @$wallet->balance ? @$wallet->balance : 0;
        }
        //Amount Limit Check Ends here
        return response()->json(['success' => $success]);
    }

    public function eachUserWithdrawSuccess(Request $request)
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'users_list';
        $data['user_tab_menu'] = 'user_profile';
        $user_id = $request->user_id;

        //Check Session - starts
        $sessionValue = session('transInfo');
        if (empty($sessionValue)) {
            return redirect(Config::get('adminPrefix')."/users/withdraw/create/" . $user_id);
        }
        //Check Session - ends

        actionSessionCheck();

        $uuid = unique_code();
        $feeInfo = FeesLimit::where(['transaction_type_id' => Withdrawal, 'currency_id' => $sessionValue['currency_id'], 'payment_method_id' => $sessionValue['payment_method']])
            ->first(['charge_percentage', 'charge_fixed']);
        $p_calc = (($sessionValue['amount']) * (@$feeInfo->charge_percentage) / 100); //charge percentage calculation

        try
        {
            DB::beginTransaction();
            //Withdrawal
            $withdrawal                    = new Withdrawal();
            $withdrawal->user_id           = $user_id;
            $withdrawal->currency_id       = $sessionValue['currency_id'];
            $withdrawal->payment_method_id = $sessionValue['payment_method'];
            $withdrawal->uuid              = $uuid;
            $withdrawal->charge_percentage = @$feeInfo->charge_percentage ? $p_calc : 0;
            $withdrawal->charge_fixed      = @$feeInfo->charge_fixed ? @$feeInfo->charge_fixed : 0;
            $withdrawal->subtotal          = ($sessionValue['amount'] - ($p_calc + (@$feeInfo->charge_fixed)));
            $withdrawal->amount            = $sessionValue['amount'];
            $withdrawal->status            = 'Success';
            $withdrawal->save();

            //Transaction
            $transaction                           = new Transaction();
            $transaction->user_id                  = $user_id;
            $transaction->currency_id              = $sessionValue['currency_id'];
            $transaction->payment_method_id        = $sessionValue['payment_method'];
            $transaction->transaction_reference_id = $withdrawal->id;
            $transaction->transaction_type_id      = Withdrawal;
            $transaction->uuid                     = $uuid;
            $transaction->subtotal                 = $withdrawal->amount;
            $transaction->percentage               = @$feeInfo->charge_percentage ? @$feeInfo->charge_percentage : 0;
            $transaction->charge_percentage        = $withdrawal->charge_percentage;
            $transaction->charge_fixed             = $withdrawal->charge_fixed;
            $transaction->total                    = '-' . ($withdrawal->amount + $withdrawal->charge_percentage + $withdrawal->charge_fixed);
            $transaction->status                   = 'Success';
            $transaction->save();

            //Wallet
            $wallet = Wallet::where(['user_id' => $user_id, 'currency_id' => $sessionValue['currency_id']])->first();
            if (!empty($wallet)) {
                $wallet->balance = ($wallet->balance - ($withdrawal->amount + $withdrawal->charge_percentage + $withdrawal->charge_fixed));
                $wallet->save();
            }

            DB::commit();

            if (checkAppMailEnvironment()) {
                $english_withdrawal_email_temp = EmailTemplate::where(['temp_id' => 31, 'lang' => 'en', 'type' => 'email'])->select('subject', 'body')->first();
                $withdrawal_email_temp         = EmailTemplate::where([
                    'temp_id'     => 31,
                    'language_id' => Session::get('default_language'),
                    'type'        => 'email',
                ])->select('subject', 'body')->first();

                if (!empty($english_withdrawal_email_temp->subject) && !empty($english_withdrawal_email_temp->body)) {
                    $w_success_sub = str_replace('{uuid}', $withdrawal->uuid, $english_withdrawal_email_temp->subject);
                    $w_success_msg = str_replace('{user_id}', $withdrawal->user->first_name . ' ' . $withdrawal->user->last_name, $english_withdrawal_email_temp->body);
                } else {
                    $w_success_sub = str_replace('{uuid}', $withdrawal->uuid, $withdrawal_email_temp->subject);
                    $w_success_msg = str_replace('{user_id}', $withdrawal->user->first_name . ' ' . $withdrawal->user->last_name, $withdrawal_email_temp->body);
                }

                $w_success_msg = str_replace('{amount}', moneyFormat(optional($withdrawal->currency)->symbol, formatNumber($withdrawal->amount)), $w_success_msg);
                $w_success_msg = str_replace('{created_at}', dateFormat($withdrawal->created_at, $withdrawal->user_id), $w_success_msg);
                $w_success_msg = str_replace('{uuid}', $withdrawal->uuid, $w_success_msg);
                $w_success_msg = str_replace('{code}', $withdrawal->currency->code, $w_success_msg);
                $w_success_msg = str_replace('{amount}', moneyFormat(optional($withdrawal->currency)->symbol, formatNumber($withdrawal->amount)), $w_success_msg);
                $w_success_msg = str_replace('{fee}', moneyFormat(optional($withdrawal->currency)->symbol, formatNumber($withdrawal->charge_fixed + $withdrawal->charge_percentage)), $w_success_msg);
                $w_success_msg = str_replace('{soft_name}', settings('name'), $w_success_msg);

                $this->email->sendEmail($withdrawal->user->email, $w_success_sub, $w_success_msg);
            }
            
            if (checkAppSmsEnvironment()) {
                $payoutMessage = 'Amount ' . moneyFormat(optional($withdrawal->currency)->symbol, formatNumber($withdrawal->amount)) . ' was withdrawn by System Administrator.';
                if (!empty($withdrawal->user->formattedPhone)) {
                    sendSMS($withdrawal->user->formattedPhone, $payoutMessage);
                }
            }

            $data['transInfo']['currency_id'] = $transaction->currency->id;
            $data['transInfo']['currSymbol'] = $transaction->currency->symbol;
            $data['transInfo']['subtotal'] = $transaction->subtotal;
            $data['transInfo']['id'] = $transaction->id;
            $data['users'] = User::find($user_id, ['id']);
            $data['name'] = $sessionValue['fullname'];

            Session::forget('transInfo');
            clearActionSession();
            return view('admin.users.withdraw.success', $data);
        }
        catch (Exception $e)
        {
            DB::rollBack();
            Session::forget('transInfo');
            clearActionSession();
            $this->helper->one_time_message('error', $e->getMessage());
            return redirect("users/withdraw/create/" . $user_id);
        }
    }

    public function eachUserWithdrawPrintPdf($trans_id)
    {
        $data['transactionDetails'] = Transaction::with(['payment_method:id,name', 'currency:id,symbol'])
            ->where(['id' => $trans_id])->first(['uuid', 'created_at', 'status', 'currency_id', 'payment_method_id', 'subtotal', 'charge_percentage', 'charge_fixed', 'total']);

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
        $mpdf->WriteHTML(view('admin.users.withdraw.withdrawalPrintPdf', $data));
        $mpdf->Output('payout_' . time() . '.pdf', 'I');
    }
    /* End of Admin Withdraw */

    public function eachUserTransaction($id, EachUserTransactionsDataTable $dataTable)
    {
        $data['menu'] = 'users';
        $data['sub_menu'] = 'users_list';
        $data['user_tab_menu'] = 'user_transactions';

        $data['users'] = User::find($id, ['id', 'first_name', 'last_name']);
        $data['transactionStatus'] = (new Transaction)->eachUserTransactionGroupBy('status', $id);
        $data['transactionCurrency'] = (new Transaction)->eachUserTransactionGroupBy('currency_id', $id);
        $data['transactionType'] = (new Transaction)->eachUserTransactionGroupBy('transaction_type_id', $id);

        $data['from'] = isset(request()->from) ? setDateForDb(request()->from) : null;
        $data['to'] = isset(request()->to ) ? setDateForDb(request()->to) : null;
        $data['status'] = isset(request()->status) ? request()->status : 'all';
        $data['currency'] = isset(request()->currency) ? request()->currency : 'all';
        $data['type'] = isset(request()->type) ? request()->type : 'all';

        return $dataTable->with('user_id', $id)->render('admin.users.eachusertransaction', $data); //passing $id to dataTable ass user_id
    }

    public function eachUserWallet($id)
    {
        $data['menu'] = 'users';
        $data['sub_menu'] = 'users_list';
        $data['user_tab_menu'] = 'user_wallets';

        $data['wallets'] = Wallet::with('currency:id,type,code')->where(['user_id' => $id])->orderBy('id', 'desc')->get();
        $data['users']   = User::find($id, ['id', 'first_name', 'last_name']);
        return view('admin.users.eachuserwallet', $data);
    }

    public function eachUserTicket($id)
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'users_list';
        $data['user_tab_menu'] = 'user_tickets';

        $data['tickets'] = Ticket::where(['user_id' => $id])->orderBy('id', 'desc')->get();
        $data['users']   = User::find($id, ['id', 'first_name', 'last_name']);
        if(!g_c_v() && a_ut_c_v()) {
            Session::flush();
            return view('vendor.installer.errors.admin');
        }
        return view('admin.users.eachuserticket', $data);
    }

    public function eachUserDispute($id)
    {
        $data['menu'] = 'users';
        $data['sub_menu'] = 'users_list';
        $data['user_tab_menu'] = 'user_disputes';

        $data['disputes'] = Dispute::where(['claimant_id' => $id])->orWhere(['defendant_id' => $id])->orderBy('id', 'desc')->get();
        $data['users'] = User::find($id, ['id', 'first_name', 'last_name']);

        return view('admin.users.eachuserdispute', $data);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if ($user) {
            try {
                DB::beginTransaction();
                // Deleting Non-Relational Table Entries

                ActivityLog::where(['user_id' => $id])->delete();
                RoleUser::where(['user_id' => $id, 'user_type' => 'User'])->delete();

                $user->delete();

                DB::commit();

                $this->helper->one_time_message('success', __('The :x has been successfully deleted.', ['x' => __('user')]));
                return redirect(Config::get('adminPrefix').'/users');
            } catch (Exception $e) {
                DB::rollBack();
                $this->helper->one_time_message('error', $e->getMessage());
                return redirect(Config::get('adminPrefix').'/users');
            }
        }
    }

    public function postEmailCheck(Request $request)
    {

        if (isset($request->admin_id) || isset($request->user_id))
        {
            if (isset($request->type) && $request->type == "admin-email")
            {
                $req_id = $request->admin_id;
                $email  = Admin::where(['email' => $request->email])->where(function ($query) use ($req_id)
                {
                    $query->where('id', '!=', $req_id);
                })->exists();
            }
            else
            {
                $req_id = $request->user_id;
                $email  = User::where(['email' => $request->email])->where(function ($query) use ($req_id)
                {
                    $query->where('id', '!=', $req_id);
                })->exists();
            }
        }
        else
        {
            if (isset($request->type) && $request->type == "admin-email")
            {
                $email = Admin::where(['email' => $request->email])->exists();
            }
            else
            {
                $email = User::where(['email' => $request->email])->exists();
            }
        }

        if ($email)
        {
            $data['status'] = true;
            $data['fail']   = __('The :x is already exist.', ['x' => __('email')]);
        }
        else
        {
            $data['status']  = false;
            $data['success'] = __('The :x is available.', ['x' => __('email')]);
        }
        return json_encode($data);
    }

    public function duplicatePhoneNumberCheck(Request $request)
    {
        $req_id = $request->id;

        if (isset($req_id))
        {
            $user = User::where(['phone' => preg_replace("/[\s-]+/", "", $request->phone), 'carrierCode' => $request->carrierCode])->where(function ($query) use ($req_id)
            {
                $query->where('id', '!=', $req_id);
            })->first(['phone', 'carrierCode']);
        }
        else
        {
            $user = User::where(['phone' => preg_replace("/[\s-]+/", "", $request->phone), 'carrierCode' => $request->carrierCode])->first(['phone', 'carrierCode']);
        }

        if (!empty($user->phone) && !empty($user->carrierCode))
        {
            $data['status'] = true;
            $data['fail']   = __('The :x is already exist.', ['x' => __('phone number')]);
        }
        else
        {
            $data['status']  = false;
            $data['success'] = __('The :x is available.', ['x' => __('phone number')]);
        }
        return json_encode($data);
    }

    public function adminList(AdminsDataTable $dataTable)
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'admin_users_list';

        return $dataTable->render('admin.users.adminList', $data);
    }

    public function adminCreate()
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'admin_users_list';

        $data['roles'] = Role::select('id', 'display_name')->where('user_type', 'Admin')->get();

        return view('admin.users.adminCreate', $data);
    }

    public function adminStore(Request $request)
    {

        $rules = array(
            'first_name'            => 'required|max:30|regex:/^[a-zA-Z\s]*$/',
            'last_name'             => 'required|max:30|regex:/^[a-zA-Z\s]*$/',
            'email'                 => 'required|unique:admins,email',
            'password'              => 'required|confirmed',
            'password_confirmation' => 'required',
        );

        $fieldNames = array(
            'first_name'            => 'First Name',
            'last_name'             => 'Last Name',
            'email'                 => 'Email',
            'password'              => 'Password',
            'password_confirmation' => 'Confirm Password',
        );
        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        else
        {
            $admin             = new Admin();
            $admin->first_name = $request->first_name;
            $admin->last_name  = $request->last_name;
            $admin->email      = $request->email;
            $admin->password   = Hash::make($request->password);
            $admin->role_id    = $request->role;
            $admin->save();
            RoleUser::insert(['user_id' => $admin->id, 'role_id' => $request->role, 'user_type' => 'Admin']);
        }

        //condition because same function used in installer for create admin
        if (!isset($request->from_installer))
        {
            $this->helper->one_time_message('success', __('The :x has been successfully saved.', ['x' => __('admin')]));
            return redirect()->intended(Config::get('adminPrefix')."/admin_users");
        }
    }

    public function adminEdit($id)
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'admin_users_list';

        $data['admin'] = Admin::find($id);
        $data['roles'] = Role::select('id', 'display_name')->where('user_type', "Admin")->get();
        return view('admin.users.adminEdit', $data);
    }

    public function adminUpdate(Request $request)
    {

        $rules = array(
            'first_name' => 'required|max:30|regex:/^[a-zA-Z\s]*$/',
            'last_name'  => 'required|max:30|regex:/^[a-zA-Z\s]*$/',
            'email'      => 'required|email|unique:admins,email,' . $request->admin_id,
        );

        $fieldNames = array(
            'first_name' => 'First Name',
            'last_name'  => 'Last Name',
            'email'      => 'Email',
        );
        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);
        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        else
        {
            $admin             = Admin::find($request->admin_id);
            $admin->first_name = $request->first_name;
            $admin->last_name  = $request->last_name;
            $admin->email      = $request->email;
            $admin->role_id    = $request->role;
            $admin->save();
            RoleUser::where(['user_id' => $admin->id, 'user_type' => 'Admin'])->update(['role_id' => $request->role]);
            $this->helper->one_time_message('success', __('The :x has been successfully saved.', ['x' => __('admin')]));
            return redirect()->intended(Config::get('adminPrefix')."/admin_users");
        }
    }

    public function adminDestroy($id)
    {
        $admin = Admin::find($id);
        if ($admin)
        {
            $admin->delete();

            // Deleting Non-Relational Table Entries
            ActivityLog::where(['user_id' => $id])->delete();
            RoleUser::where(['user_id' => $id, 'user_type' => 'Admin'])->delete();

            $this->helper->one_time_message('success', __('The :x has been successfully deleted.', ['x' => __('admin')]));
            return redirect()->intended(Config::get('adminPrefix')."/admin_users");
        }
    }

    protected function saveUserQrCode($user)
    {
        $qrCode = QrCode::where(['object_id' => $user->id, 'object_type' => 'user', 'status' => 'Active'])->first(['id']);
        if (empty($qrCode))
        {
            $createInstanceOfQrCode              = new QrCode();
            $createInstanceOfQrCode->object_id   = $user->id;
            $createInstanceOfQrCode->object_type = 'user';
            if (!empty($user->formattedPhone))
            {
                $createInstanceOfQrCode->secret = convert_string('encrypt', $createInstanceOfQrCode->object_type . '-' . $user->email . '-' . $user->formattedPhone . '-' . Str::random(6));
            }
            else
            {
                $createInstanceOfQrCode->secret = convert_string('encrypt', $createInstanceOfQrCode->object_type . '-' . $user->email . '-' . Str::random(6));
            }
            $createInstanceOfQrCode->status = 'Active';
            $createInstanceOfQrCode->save();
        }
    }

    public function eachUserReferralAwards($id, \App\DataTables\Admin\EachUserReferralAwardsDataTable $dataTable)
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'users_list';
        $data['users'] = $users = User::find($id);
        $referralAward = new \App\Models\ReferralAward();

        $referralAwardsLevels = $referralAward->with(['referral_level:id,currency_id'])->where(['awarded_user_id' => $id])->groupBy('referral_level_id')->get(['referral_level_id']);

        if ($referralAwardsLevels->count() > 0) {
            foreach ($referralAwardsLevels as $referralAwardsLevel) {
                $data['referralAwardsLevels'] = $referralAwardsLevel->referral_level()->groupBy('currency_id')->get(['currency_id']);
            }
        }

        //For Each Currency Referral Award
        $data['currency'] = $currency = isset(request()->currency) ? request()->currency : 'all';
        $data['from'] = $from = isset(request()->from) && !empty(request()->from) ? setDateForDb(request()->from) : null;
        $data['to'] = $to = isset(request()->to) && !empty(request()->to) ? setDateForDb(request()->to) : null;

        $getReferralAwardsForEachCurrency = $referralAward->getReferralAwardsList($from, $to, $currency, $users->id)->get();

        $counter        = 0;
        $array_map      = [];
        $array_final    = [];
        if ($getReferralAwardsForEachCurrency->count() > 0) {
            foreach ($getReferralAwardsForEachCurrency as $value) {
                if (isset($value->referral_level->currency->code) && !empty($value->referral_level->currency->code)) {
                    $array_map[$value->referral_level->currency->code][$counter++] = $value->awarded_amount;
                }
            }
            if ($array_map) {
                foreach ($array_map as $key => $res) {
                    $array_final[$key] = array_sum($res);
                }
            }
            $data['referralAwardsEachCurrency'] = $array_final;
        } else {
            $data['referralAwardsEachCurrency'] = [];
        }
        return $dataTable->with('user_id', $id)->render('admin.users.eachUserReferralAward', $data); //passing $id to dataTable ass user_id
    }
}