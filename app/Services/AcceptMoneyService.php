<?php

/**
 * @package AcceptMoneyService
 * @author tehcvillage <support@techvill.org>
 * @contributor Md Abdur Rahaman <[abdur.techvill@gmail.com]>
 * @created 20-12-2022
 */

namespace App\Services;

use App\Http\Helpers\Common;
use App\Exceptions\Api\V2\{
    PaymentFailedException,
    AcceptMoneyException
};
use App\Services\Mail\{
    RequestMoneyReceiverCancelEmailService,
    RequestMoneyCreatorCancelEmailService,
};
use App\Services\Sms\{
    RequestMoneyCreatorCancelSmsService,
    RequestMoneyReceiverCancelSmsService,
};
use App\Models\{
    Currency, 
    RequestPayment,
    Transaction,
    FeesLimit,
    Wallet,
    User,
};

class AcceptMoneyService 
{
    protected $requestPayment;
    protected $helper;

    public function __construct()
    {
        $this->requestPayment = new RequestPayment();
        $this->helper         = new Common();
    }
    /**
     * Get details of a request payment to accept it
     *
     * @param int $id
     * @return RequestPayment
     * @throws AcceptMoneyException
     */
    public function details($id) : RequestPayment
    {
        if (empty($id) || is_null($id)) {
            throw new AcceptMoneyException(__(":x is required.", ["x" => __("Transaction reference number")]));
        }
        $requestPayment = RequestPayment::with(['currency:id,symbol,code'])->where('id', $id)
                                        ->first(['email', 'phone', 'amount', 'user_id', 'currency_id']);
        if (!$requestPayment) {
            throw new AcceptMoneyException(__("No transaction was found for this reference number."));
        }
        return $requestPayment;
    }

    /**
     * Check Maximum and minimum amount
     * Check wallet balance
     * @param double $amount
     * @param int $currency_id
     * @param int $user_id
     * @return array 
     * @throws AcceptMoneyException
     */
    public function checkAmountLimit($amount, $currency_id, $user_id) : array
    {
        $success = ['status' => true];
        $user    = User::find($user_id, ['id']);
        if (!$user) {
            throw new AcceptMoneyException(__('The :x does not exist.', ['x' => __('user')]));
        }
        $wallet = Wallet::where(['user_id' => $user_id, 'currency_id' => $currency_id])->first(['id', 'balance']);
        if (!$wallet) {
            throw new AcceptMoneyException(__("You don't have the requested currency."));
        }
        $feesDetails = FeesLimit::transactionType(Request_To)->where(['currency_id' => $currency_id])
                                  ->first(['charge_fixed', 'charge_percentage', 'min_limit', 'max_limit']);
        if ($feesDetails) {
            $feesPercentage      = $amount * ($feesDetails->charge_percentage / 100);
            $checkAmountWithFees = $amount + $feesDetails->charge_fixed + $feesPercentage;
            if (($checkAmountWithFees) > ($wallet->balance) || ($wallet->balance < 0)) {
                throw new AcceptMoneyException(__("Sorry, not enough funds to perform the operation."));
            }
            if (is_null($feesDetails->max_limit)) {
                if (($amount < $feesDetails->min_limit)) {
                    $success['status']   = false;
                    $success['reason']   = 'minLimit';
                    $success['minLimit'] = $feesDetails->min_limit;
                    $success['message']  = __('Minimum amount :x', ["x" => formatNumber($feesDetails->min_limit)]);
                } 
            } else {
                if (($amount < $feesDetails->min_limit) || ($amount > $feesDetails->max_limit)) {
                    $success['status']   = false;
                    $success['reason']   = 'minMaxLimit';
                    $success['minLimit'] = $feesDetails->min_limit;
                    $success['maxLimit'] = $feesDetails->max_limit;
                    $success['message']  = __('Minimum amount :x and Maximum amount :y', ["x" => formatNumber($feesDetails->min_limit), "y" => formatNumber($feesDetails->max_limit)]);
                } 
            }
        }
        return $success;
    }

    /**
     * Email validation for request payment
     *
     * @param int $user_id
     * @param int $trId
     * @return array $response
     * @throws AcceptMoneyException
     */
    public function checkRequestReceiverEmail($user_id, $trId) : array
    {
        $response = ['status' => true];
        $receiver = User::where(['id' => $user_id])->first(['email']);
        if (!$receiver) {
            throw new AcceptMoneyException(__('The :x does not exist.', ['x' => __('user')]));
        }
        $requestMoney = RequestPayment::find($trId, ['user_id', 'receiver_id']);
        if ($requestMoney->user_id == $user_id) {
            throw new AcceptMoneyException(__("You cannot request money to yourself."));
        }
        if ($requestMoney->receiver_id != $user_id) {
            throw new AcceptMoneyException(__("Receiver not matched."));
        }
        $user = User::find($requestMoney->user_id, ['email','status']);
        if ($user) {
            if ($user->email == $receiver->email) {
                $response['status']  = false;
                $response['reason']  = 'own-email';
                $response['message'] = __('You cannot request money to yourself.');
            }
            $status = strtolower($user->status);
            if ("active" != $status) {
                $response['status']     = false;
                $response['userStatus'] = $user->status;
                $response['message']    = __("The recipient is :x. ", ["x" => __($status)]);
            }
        }
        return $response;
    }

    /**
     * Phone number validation for request payment
     *
     * @param int $user_id
     * @param int $trId
     * @return array
     */
    public function checkRequestReceiverPhone($user_id, $trId) : array
    {
        $response = ['status' => true];
        $receiver = User::where(['id' => $user_id])->first(['formattedPhone']);
        if (!$receiver) {
            throw new AcceptMoneyException(__('The :x does not exist.', ['x' => __('user')]));
        }
        $requestMoney = RequestPayment::find($trId, ['user_id', 'receiver_id']);
        if ($requestMoney->user_id == $user_id) {
            $response['status']  = false;
            $response['reason']  = 'own-email';
            $response['message'] = __('You cannot request money to yourself.');
        }
        if (empty($receiver->formattedPhone)) {
            throw new AcceptMoneyException(__("Please set your phone number first."));
        }
        $user = User::find($requestMoney->user_id, ['email','status']);
        if ($user->formattedPhone == $receiver->formattedPhone) {
            $response['status']  = false;
            $response['reason']  = 'own-phone';
            $response['message'] = __('You cannot request money to yourself.');
        }
        $status = strtolower($user->status);
        if ("active" != $status) {
            $response['status']          = false;
            $response['recipientStatus'] = $user->status;
            $response['message']         = __("The recipient is :x. ", ["x" =>__($status)]);
        }
        return $response;
    }


    /**
     * Get Fees
     * 
     * @param double $amount
     * @param int $currency_id
     * @return array(status, message)
     */
    public function getFees($amount, $currency_id) : array
    {
        $success     = ['status' => true];
        $totalFess   = 0;
        $feesDetails = FeesLimit::transactionType(Request_To)->where(['currency_id' => $currency_id])
                                ->first(['charge_percentage', 'charge_fixed']);
        if ($feesDetails) {
            $success['charge_percentage'] = $feesDetails->charge_percentage;
            $success['charge_fixed']      = $feesDetails->charge_fixed;
            $success['feesPercentage']    = $feesPercentage = $amount * ($feesDetails->charge_percentage / 100);
            $totalFess                    = $feesPercentage + ($feesDetails->charge_fixed);
        }
        $currency = Currency::where(['id' => $currency_id])->first(['symbol', 'code']);
        if (!$currency) {
            $success['status']  = false;
            $success['message'] = __('The :x does not exist.', ['x' => __('Currency')]);
            return $success;
        }
        $success['totalAmount'] = $amount + $totalFess;
        $success['totalFees']   = $totalFess;
        $success['currSymbol']  = $currency->symbol;
        $success['currCode']    = $currency->code;
        return $success;
    }

    /**
     * Store aceept money request
     *
     * @param int $trId
     * @param double $amount
     * @param int $userId
     * @param int $currencyId
     * @param string $emailOrPhone
     * @param string $processedBy
     * @return array 
     */
    public function store($trId, $amount, $userId, $currencyId, $emailOrPhone, $processedBy) : array
    {
        $response = ['status' => true];
        switch ($processedBy) {
            case 'phone':
                $response = $this->checkRequestReceiverPhone($userId, $trId);
                break;
            case 'email_or_phone':
                if (false !== strpos($emailOrPhone, '@')) {
                    $response = $this->checkRequestReceiverEmail($userId, $trId);
                } else {
                    $response = $this->checkRequestReceiverPhone($userId, $trId);
                }
                break;
            default:
                $response = $this->checkRequestReceiverEmail($userId, $trId);
                break;
        }
        if (!$response['status']) {
            return $response;
        }
        $status = RequestPayment::where('id', $trId)->value('status');
        if ("success" == strtolower($status)) {
            $response['status'] = false;
            $response['message'] = __("You already accepted the request.");
            return $response;
        }
        $fees = $this->getFees($amount, $currencyId);
        if (!$fees['status']) {
            return $fees;
        }
        $emailFilterValidate = $this->helper->validateEmailInput($emailOrPhone);
        $phoneRegex          = $this->helper->validatePhoneInput($emailOrPhone);
        $arr = [
            'unauthorisedStatus'  => null,
            'emailFilterValidate' => $emailFilterValidate,
            'phoneRegex'          => $phoneRegex,
            'processedBy'         => $processedBy,
            'requestPaymentId'    => $trId,
            'currency_id'         => $currencyId,
            'user_id'             => $userId,
            'accept_amount'       => $amount,
            'charge_percentage'   => $fees['charge_percentage'],
            'fixed_fee'           => $fees['charge_fixed'],
            'percentage_fee'      => $fees['feesPercentage'],
            'fee'                 => $fees['totalFees'],
            'total'               => $fees['totalAmount'],
        ];
        $response = $this->requestPayment->processRequestAcceptConfirmation($arr, 'mobile');
        if (200 != $response['status']) {
            if (empty($response['reqPayment'])) {
                throw new PaymentFailedException([
                    'status'  => false,
                    'message' => $response['ex']['message'],
                ]);
            }
            throw new PaymentFailedException([
                'status'  => true,
                'message' => $response['ex']['message'],
            ]);
        }
        return [
            'status' => true,
        ];
    }

    /**
     * Cancel a request payment by any party [creator or receiver]
     * @param int $trId
     * @param int $user_id
     * @return array
     */
    public function cancel($trId, $user_id) : array
    {
        $response     = ['status' => true, 'requestPayment' => NULL];
        $transactionA = Transaction::where(['id' => $trId, 'user_id' => $user_id])->first(['id', 'status', 'transaction_type_id', 'transaction_reference_id']);
        if (!$transactionA) { 
            $response['status']  = false;
            $response['message'] = __('The :x does not exist.', ['x' => __('transaction')]);
            return $response;
        }
        $transactionA->status = "Blocked";
        $transactionA->save();
        $transaction_type_id  = $transactionA->transaction_type_id == Request_To ? Request_From : Request_To;
        $transactionB         = Transaction::where(['transaction_reference_id' => $transactionA->transaction_reference_id, 'transaction_type_id' => $transaction_type_id])
                                            ->first(['id', 'status']);
        if (!$transactionB) {
            $response['status']  = false;
            $response['message'] = __('The :x does not exist.', ['x' => __('transaction')]);
            return $response;
        }
        $transactionB->status = "Blocked";
        $transactionB->save();
        $requestPayment = RequestPayment::find($transactionA->transaction_reference_id);
        if (!$requestPayment) {
            $response['status']  = false;
            $response['message'] = __('The :x does not exist.', ['x' => __('transaction')]);
            return $response;
        }
        $requestPayment->status = "Blocked";
        $requestPayment->save();
        $response['requestPayment'] = $requestPayment;
        return $response;
    }

    /**
     * Request Creator canceled the request
     * Send Cancel Notification to receiver
     * @param object $requestPayment
     * @return array
     */
    public function sendCancelNotificationToReceiver($requestPayment) : array
    {
        $processedBy         = preference('processed_by');
        $phoneRegex          = false;
        $emailFilterValidate = false;
        if (!empty($requestPayment->email)) {
            $emailFilterValidate = filter_var($requestPayment->email, FILTER_VALIDATE_EMAIL);
        }
        if (!empty($requestPayment->phone)) {
            $phoneRegex = preg_match('%^(?:(?:\(?(?:00|\+)([1-4]\d\d|[1-9]\d?)\)?)?[\-\.\ \\\/]?)?((?:\(?\d{1,}\)?[\-\.\ \\\/]?){0,})(?:[\-\.\ \\\/]?(?:#|ext\.?|extension|x)[\-\.\ \\\/]?(\d+))?$%i', $requestPayment->phone);
        }
        if ($emailFilterValidate && "email" == $processedBy) {
            return (new RequestMoneyReceiverCancelEmailService())->send($requestPayment);
        } elseif ($phoneRegex && "phone" == $processedBy) {
            return (new RequestMoneyReceiverCancelSmsService())->send($requestPayment);
        } elseif ("email_or_phone" == $processedBy) {
            if ($emailFilterValidate) {
                return (new RequestMoneyReceiverCancelEmailService())->send($requestPayment);
            } elseif ($phoneRegex) {
                return (new RequestMoneyReceiverCancelSmsService())->send($requestPayment);
            }
        }
    }

    /**
     * Request receiver canceled the request
     * Send Cancel Notification to creator
     * @param object $requestPayment
     * @return array
     */
    public function sendCancelNotificationToCreator($requestPayment) : array
    {
        $processedBy         = preference('processed_by');
        $phoneRegex          = false;
        $emailFilterValidate = false;
        if (!empty($requestPayment->email)) {
            $emailFilterValidate = filter_var($requestPayment->email, FILTER_VALIDATE_EMAIL);
        }
        if (!empty($requestPayment->phone)) {
            $phoneRegex = preg_match('%^(?:(?:\(?(?:00|\+)([1-4]\d\d|[1-9]\d?)\)?)?[\-\.\ \\\/]?)?((?:\(?\d{1,}\)?[\-\.\ \\\/]?){0,})(?:[\-\.\ \\\/]?(?:#|ext\.?|extension|x)[\-\.\ \\\/]?(\d+))?$%i', $requestPayment->phone);
        }
        if ($emailFilterValidate && "email" == $processedBy) {
            return (new RequestMoneyCreatorCancelEmailService())->send($requestPayment);
        } elseif ($phoneRegex && "phone" == $processedBy) {
            return (new RequestMoneyCreatorCancelSmsService())->send($requestPayment);
        } elseif ("email_or_phone" == $processedBy) {
            if ($emailFilterValidate) {
                return (new RequestMoneyCreatorCancelEmailService())->send($requestPayment);
            } elseif ($phoneRegex) {
                return (new RequestMoneyCreatorCancelSmsService())->send($requestPayment);
            }
        }
    }
    
}
