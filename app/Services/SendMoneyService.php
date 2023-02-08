<?php

/**
 * @package SendMoneyService
 * @author tehcvillage <support@techvill.org>
 * @contributor Md Abdur Rahaman Zihad <[zihad.techvill@gmail.com]>
 * @created 20-11-2022
 */

namespace App\Services;

use App\Http\Helpers\Common;
use App\Enums\Status;
use App\Exceptions\Api\V2\{
    PaymentFailedException,
    SendMoneyException
};
use App\Models\{
    Transfer,
    Wallet,
    User
};

class SendMoneyService
{
    /**
     * @var Common;
     */
    protected $helper;


    /**
     * Construct the service class
     *
     * @param Common $helper
     *
     * @return void
     */
    public function __construct(Common $helper)
    {
        $this->helper = $helper;
    }


    /**
     * Validates if the payable request can be processed or not
     *
     * @param string $email
     *
     * @return bool
     */
    public function validateEmail($email)
    {
        $receiver = User::whereEmail($email)->first();

        // Check if receiver exists
        if (is_null($receiver)) {
            return true;
        }

        return $this->validateReceiverUserEmail($receiver);
    }


    /**
     * Validates receiver email for sending money
     *
     * @param User|null $receiver
     *
     * @return bool
     *
     * @throws SendMoneyException
     */
    protected function validateReceiverUserEmail($receiver)
    {
        if (is_null($receiver)) {
            return true;
        }

        $user = auth()->user();

        // Check if both user and receiver email addresses are same
        if ($user->email ==  $receiver->email) {
            throw new SendMoneyException(__("You cannot send money to yourself."));
        }

        // Check if receiver is a suspended user
        if (in_array($receiver->status, [Status::SUSPENDED, Status::INACTIVE])) {
            throw new SendMoneyException(__("You cannot send money to a :x user.", ["x" => $receiver->status]));
        }

        return true;
    }



    /**
     * Validates if the payable request can be processed or not
     *
     * @param string $phone
     *
     * @return bool
     *
     * @throws SendMoneyException
     */
    public function validatePhoneNumber($phone)
    {
        $user = auth()->user();

        if (is_null($user->formattedPhone) || empty($user->formattedPhone)) {
            throw new SendMoneyException(__("Please set your phone number first."));
        }

        $receiver = User::select('formattedPhone', 'status')->where("formattedPhone", $phone)->first();

        if (is_null($receiver)) {
            return true;
        }

        return $this->validateReceiverUserPhone($receiver);
    }


    /**
     * Validate receiver phone number for sending money
     *
     * @param User $receiver
     * @return bool
     *
     * @throws SendMoneyException
     */
    protected function validateReceiverUserPhone($receiver)
    {
        // Check if receiver exists
        if (is_null($receiver)) {
            return true;
        }

        $user = auth()->user();

        // Check if both user and receiver email addresses are same
        if ($user->formattedPhone ==  $receiver->formattedPhone) {
            throw new SendMoneyException(__("You cannot send money to yourself."));
        }

        // Check if receiver is a suspended user
        if (in_array($receiver->status, [Status::SUSPENDED, Status::INACTIVE])) {
            throw new SendMoneyException(__("You cannot send money to a :x user.", ["x" => $receiver->status]));
        }

        return true;
    }


    /**
     * Get available currencies of the user
     *
     * @return array
     */
    public function getSelfCurrencies()
    {
        $result = [];

        Wallet::with('currency:id,code,type')
            ->where("user_id", auth()->id())
            ->whereHas("active_currency")
            ->join('fees_limits', 'fees_limits.currency_id', 'wallets.currency_id')
            ->where('fees_limits.has_transaction', 'Yes')
            ->get()
            ->map(function ($item) use (&$result) {
                $result[$item->currency_id] = [
                    'id' => $item->currency_id,
                    'code' => optional($item->currency)->code,
                    'is_default' => $item->is_default,
                    'type' => optional($item->currency)->type
                ];
            });

        return array_values($result);
    }



    /**
     * Check the requested amount and currency in user wallet and Fees limit
     *
     * @param int $currencyId Currency Id
     * @param float $amount
     *
     * @return array
     *
     * @throws SendMoneyException
     */
    public function validateAmountLimit($currencyId, $amount)
    {
        $userId = auth()->id();

        $currencyFee = $this->helper->getFeesLimitObject(
            ['currency:id,code,symbol,type'],
            Transferred,
            $currencyId,
            null,
            null,
            ['charge_percentage', 'charge_fixed', 'currency_id', 'min_limit', 'max_limit']
        );

        if (is_null($currencyFee)) {
            return [
                'sendAmount' => $amount,
                'sendCurrency' => $currencyId,
                'totalFees' => 0,
                'sendAmountDisplay' => formatNumber($amount),
                'totalFeesDisplay' => formatNumber(0),
                'totalAmountDisplay' => formatNumber($amount),
                'currCode' => null,
                'currSymbol' => null,
                'currType' => null,
            ];
        }

        //Wallet Balance Limit Check Starts here
        $feesPercentage = $amount * ($currencyFee->charge_percentage / 100);

        $checkAmountWithFees = $amount + $currencyFee->charge_fixed + $feesPercentage;

        $wallet   = $this->helper->getUserWallet([], ['user_id' => $userId, 'currency_id' => $currencyId], ['balance']);

        if (is_null($wallet)) {
            throw new SendMoneyException(__("Wallet not found."));
        }

        // Checks if wallet has enough balance
        if ($wallet->balance < $checkAmountWithFees) {
            throw new SendMoneyException(__("Sorry, not enough funds to perform the operation."));
        }

        $minError = (float) $amount < $currencyFee->min_limit;

        $maxError = $currencyFee->max_limit &&  $amount > $currencyFee->max_limit;

        if ($minError && $maxError) {
            throw new SendMoneyException(__("Maximum acceptable amount is :x and minimum acceptable amount is :y", [
                "x" => formatNumber($currencyFee->max_limit, optional($currencyFee->currency)->id),
                "y" => formatNumber($currencyFee->min_limit, optional($currencyFee->currency)->id),
            ]));
        } elseif ($maxError) {
            throw new SendMoneyException(__(
                "Maximum acceptable amount is :x",
                [
                    "x" => formatNumber($currencyFee->max_limit, optional($currencyFee->currency)->id)
                ]
            ));
        } elseif ($maxError) {
            throw new SendMoneyException(__(
                "Minimum acceptable amount is :x",
                [
                    "x" => formatNumber($currencyFee->min_limit, optional($currencyFee->currency)->id)
                ]
            ));
        }

        $feesPercentage = $amount * ($currencyFee->charge_percentage / 100);
        $feesFixed = $currencyFee->charge_fixed;
        $totalFess = $feesPercentage + $feesFixed;
        $totalAmount = $amount + $totalFess;

        return [
            'sendAmount' => $amount,
            'sendCurrency' => $currencyId,
            'totalFees' => $totalFess,
            'sendAmountDisplay' => formatNumber($amount, optional($currencyFee->currency)->id),
            'totalFeesDisplay' => formatNumber($totalFess, optional($currencyFee->currency)->id),
            'totalAmountDisplay' => formatNumber($totalAmount, optional($currencyFee->currency)->id),
            'currCode' => optional($currencyFee->currency)->code,
            'currSymbol' => optional($currencyFee->currency)->symbol,
            'currType' => optional($currencyFee->currency)->type,
        ];
    }



    public function sendMoneyConfirm($identifier, $currencyId, $amount, $totalFees, $note)
    {
        $identifier = trim($identifier);

        $email = $this->helper->validateEmailInput($identifier);
        $phone = $this->helper->validatePhoneInput($identifier);

        $uniqueCode = unique_code();
        $totalWithFee = $amount + $totalFees;
        $processedBy = preference("processed_by");
        $user =  auth()->user();

        $currencyFee = $this->helper->getFeesLimitObject([], Transferred, $currencyId, null, null, ['charge_percentage', 'charge_fixed']);
        $senderWallet = $this->helper->getUserWallet([], ['user_id' => $user->id, 'currency_id' => $currencyId], ['id', 'balance']);
        $receiver = User::where('email', $identifier)->orWhere('formattedPhone', $identifier)->first();

        if (!$email && !$phone) {
            throw new SendMoneyException(__("Invalid send money request."));
        }

        $sendMoneyData = [
            'emailFilterValidate' => $email,
            'phoneRegex' => $phone,
            'processedBy' => $processedBy,
            'user_id' => $user->id,
            'currency_id' => $currencyId,
            'uuid' => $uniqueCode,
            'fee' => $totalFees,
            'amount' => $amount,
            'note' => trim($note),
            'receiver' => $identifier,
            'charge_percentage' => $currencyFee->charge_percentage,
            'charge_fixed' => $currencyFee->charge_fixed,
            'p_calc' => $amount * ($currencyFee->charge_percentage / 100),
            'total' => $totalWithFee,
            'senderWallet' => $senderWallet,
        ];
        if (!is_null($receiver)) {
            $sendMoneyData['userInfo'] = $receiver;
        }
        $response = (new Transfer())->processSendMoneyConfirmation($sendMoneyData, 'mobile');

        if ($response['status'] != 200) {

            if (empty($response['transactionOrTransferId'])) {
                throw new PaymentFailedException([
                    'status' => false,
                    'sendMoneyValidationErrorMessage' => $response['ex']['message'],
                ]);
            }
            throw new PaymentFailedException([
                'status' => true,
                'sendMoneyMailErrorMessage' => $response['ex']['message'],
                'tr_ref_id' => $response['transactionOrTransferId'],
            ]);
        }
        return [
            'status' => true,
            'tr_ref_id' => $response['transactionOrTransferId'],
        ];
    }
}
