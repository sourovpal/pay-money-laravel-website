<?php

/**
 * @package SendMoneyController
 * @author tehcvillage <support@techvill.org>
 * @contributor Md Abdur Rahaman Zihad <[zihad.techvill@gmail.com]>
 * @created 04-12-2022
 */

namespace App\Http\Controllers\Api\V2;


use App\Services\SendMoneyService;
use App\Http\Controllers\Controller;
use App\Exceptions\Api\V2\{
    PaymentFailedException,
    SendMoneyException
};
use App\Http\Requests\Api\V2\SendMoney\{
    AmountLimitCheckRequest,
    EmailCheckRequest,
    PhoneCheckRequest,
    SendMoneyRequest
};

class SendMoneyController extends Controller
{
    /**
     * SendMoneyService
     *
     * @var SendMoneyService
     */
    private $service;

    public function __construct(SendMoneyService $service)
    {
        $this->service = $service;
    }


    /**
     * Validate the email for sending money
     *
     * @param EmailCheckRequest $request
     *
     * @return JsonResponse
     */
    public function emailValidate(EmailCheckRequest $request)
    {
        try {
            if ($this->service->validateEmail($request->receiver_email)) {
                return $this->okResponse([], __("Payable Request."));
            }
        } catch (SendMoneyException $exception) {
            return $this->unprocessableResponse([], $exception->getMessage());
        } catch (\Exception $exception) {
            // Common return value for every other exception
        }
        return $this->unprocessableResponse([], __("Email validation failed."));
    }


    /**
     * Validate the phone number for sending money
     *
     * @param PhoneCheckRequest $request
     *
     * @return JsonResponse
     */
    public function phoneValidate(PhoneCheckRequest $request)
    {
        try {
            if ($this->service->validatePhoneNumber($request->receiver_phone)) {
                return $this->okResponse([], __("Payable Request."));
            }
        } catch (SendMoneyException $exception) {
            return $this->unprocessableResponse([], $exception->getMessage());
        } catch (\Exception $exception) {
            // Common return value for every other exception
        }
        return $this->unprocessableResponse([], __("Phone number validation failed."));
    }


    /**
     * Get requested user's activated currencies in feesLimit
     *
     * @return JsonResponse
     */
    public function getCurrencies()
    {
        try {
            return $this->successResponse(['currencies' => $this->service->getSelfCurrencies()]);
        } catch (SendMoneyException $exception) {
            return $this->unprocessableResponse([], $exception->getMessage());
        } catch (\Exception $exception) {
            // Common return value for every other exception
        }
        return $this->unprocessableResponse([], __("Failed to process the request."));
    }


    /**
     * Validate requested amount & currency against User's wallet and System settings
     *
     * @param AmountLimitCheckRequest $request
     *
     * @return JsonResponse
     */
    public function amountLimitCheck(AmountLimitCheckRequest $request)
    {
        try {
            return $this->successResponse([
                $this->service->validateAmountLimit($request->send_currency, $request->send_amount)
            ]);
        } catch (SendMoneyException $exception) {
            return $this->unprocessableResponse([], $exception->getMessage());
        } catch (\Exception $exception) {
            // Common return value for every other exception
        }
        return $this->unprocessableResponse([], __("Failed to process the request."));
    }

    /**
     * Confirm and complete send money process
     *
     * @param SendMoneyRequest $request
     *
     * @return JsonResponse
     */
    public function sendMoneyConfirm(SendMoneyRequest $request)
    {
        try {
            $response = $this->service->sendMoneyConfirm(
                $request->email ?? $request->phone,
                $request->currency_id,
                $request->amount,
                $request->total_fees,
                $request->note
            );
        } catch (SendMoneyException $exception) {
            return $this->unprocessableResponse([], $exception->getMessage());
        } catch (PaymentFailedException $exception) {
            return $this->unprocessableResponse($exception->getDecodedMessage());
        } catch (\Exception $exception) {
            // Common return value for every other exception
        }
        return $this->okResponse($response);
    }
}
