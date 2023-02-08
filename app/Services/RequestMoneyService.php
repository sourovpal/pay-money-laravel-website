<?php

/**
 * @package RequestMoneyService
 * @author tehcvillage <support@techvill.org>
 * @contributor Md Abdur Rahaman <[abdur.techvill@gmail.com]>
 * @created 20-12-2022
 */

namespace App\Services;

use App\Http\Helpers\Common;
use App\Exceptions\Api\V2\{
    RequestMoneyException,
    PaymentFailedException,
    CurrencyException,
};
use App\Models\{
    RequestPayment,
    Currency,
    User,
};

class RequestMoneyService 
{
    /**
     * Email validation for request payment
     *
     * @param email $userId
     * @param string $receiverEmail
     * @return bool
     * @throws RequestMoneyException
     */
    public function checkRequestSenderEmail($userId, $receiverEmail) : bool
    {
        $user     = User::where('id', '=', $userId)->first(['email']);
        if (!$user) {
            throw new RequestMoneyException(__('The :x does not exist.', ['x' => __('user')]));
        }
        $receiver = User::where('email', '=', $receiverEmail)->first(['email','status']);
        if ($receiver) {
            if ($user->email == $receiver->email) {
                throw new RequestMoneyException(__('You cannot request money to yourself.'));
            }
            $status = strtolower($receiver->status);
            if ("active" != $status) {
                throw new RequestMoneyException(__("The recipient is :x .", ["x" => $receiver->status]));
            }
        }
        return true;
    }

    /**
     * Phone number validation for request payment
     *
     * @param int $userId
     * @param string $receiverPhone
     * @return bool
     * @throws RequestMoneyException
     */
    public function checkRequestSenderPhone($userId, $receiverPhone) : bool
    {
        $user     = User::where('id', '=', $userId)->first(['formattedPhone']);
        if (!$user) {
            throw new RequestMoneyException(__("User doesn't exists."));
        }
        $receiver = User::where('formattedPhone', '=', $receiverPhone)->first(['formattedPhone','status']);
        if (empty($user->formattedPhone)) {
            throw new RequestMoneyException(__('Please set your phone number first!'));
        }
        if ($receiver) {
            if ($user->formattedPhone == $receiver->formattedPhone) {
                throw new RequestMoneyException(__('You Cannot Request Money To Yourself.'));
            }
            $status = strtolower($receiver->status);
            if ("active" != $status) {
                throw new RequestMoneyException(__("The recipient is :x .", ["x" => $receiver->status]));
            }
        }
        return true;
    }

    /**
     * Get available currencies for request money
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCurrencies() : \Illuminate\Database\Eloquent\Collection
    {
        $currencies = Currency::whereHas('fees_limit', function($query) {
                                    $query->transactionType(Request_To)->where(['has_transaction' => 'Yes']);
                                })->active()->fiat()->get(['id', 'code', 'symbol', 'type']);
        return $currencies;
    }


    /**
     * Store request money
     *
     * @param double $amount
     * @param int $currency_id
     * @param string $note
     * @param int $uid
     * @param string $processedBy
     * @return array
     * @throws CurrencyException
     * @throws PaymentFailedException
     */
    public function store($emailOrPhone, $amount, $currency_id, $note, $uid, $processedBy) : array
    {
        switch ($processedBy) {
            case 'phone':
                $response = $this->checkRequestSenderPhone($uid, $emailOrPhone);
                break;
            case 'email_or_phone':
                if (false !== strpos($emailOrPhone, '@')) {
                    $response = $this->checkRequestSenderEmail($uid, $emailOrPhone);
                } else {
                    $response = $this->checkRequestSenderPhone($uid, $emailOrPhone);
                }
                break;
            default:
                $response = $this->checkRequestSenderEmail($uid, $emailOrPhone);
                break;
        }
        $currency = Currency::find($currency_id, ['id']);
        if (!$currency) {
            throw new CurrencyException(__("Currency does not exist in the system."));
        }
        $emailFilterValidate = (new Common())->validateEmailInput(trim($emailOrPhone));
        $phoneRegex          = (new Common())->validatePhoneInput(trim($emailOrPhone));
        $senderInfo          = User::where(['id' => $uid])->first(['email']);
        $userInfo            = (new Common())->getEmailPhoneValidatedUserInfo($emailFilterValidate, $phoneRegex, trim($emailOrPhone));
        $receiverName        = $userInfo->full_name ?? "";
        $arr                 = [
            'unauthorisedStatus'  => 401,
            'emailFilterValidate' => $emailFilterValidate,
            'phoneRegex'          => $phoneRegex,
            'processedBy'         => $processedBy,
            'user_id'             => $uid,
            'userInfo'            => $userInfo,
            'currency_id'         => $currency_id,
            'uuid'                => unique_code(),
            'amount'              => $amount,
            'receiver'            => $emailOrPhone,
            'note'                => $note,
            'receiverName'        => $receiverName,
            'senderEmail'         => $senderInfo->email,
        ];
        $response = (new RequestPayment())->processRequestCreateConfirmation($arr, 'web'); // BUG:: Here the parameter and it's logic should be change from the model
        if (200 != $response['status']) {
            if (empty($response['transactionOrReqPaymentId'])) {
                throw new PaymentFailedException([
                    'status'  => false,
                    'message' => $response['ex']['message'],
                ]);
            }
            throw new PaymentFailedException([
                'status'  => false,
                'ref_id'  => $response['transactionOrReqPaymentId'],
                'message' => $response['ex']['message'],
                "receiverName" => $receiverName
            ]);
        }
        return [
            'status' => true,
            'ref_id' => $response['transactionOrReqPaymentId'],
            "receiverName" => $receiverName
        ];
    }

}
