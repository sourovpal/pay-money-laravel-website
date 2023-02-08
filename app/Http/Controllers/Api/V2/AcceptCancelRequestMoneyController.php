<?php

/**
 * @package AcceptCancelRequestMoneyController
 * @author tehcvillage <support@techvill.org>
 * @contributor Md Abdur Rahaman <[abdur.techvill@gmail.com]>
 * @created 20-12-2022
 */

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\User\RequestMoneyDetailResource;
use App\Http\Requests\RequestMoney\GetFeesRequest;
use App\Http\Requests\AcceptMoney\{
    CheckAmountLimitRequest,
    StoreRequest,
};
use App\Exceptions\Api\V2\{
    AcceptMoneyException,
    PaymentFailedException,
    CurrencyException,
};
use App\Services\AcceptMoneyService;
use DB, Exception;
use App\Http\Controllers\Controller;

class AcceptCancelRequestMoneyController extends Controller
{
    /**
     * Get details of a request payment transaction
     *
     * @param AcceptMoneyService $service
     * @return JsonResponse
     */
    public function details(AcceptMoneyService $service)
    {
        try {
            $requestPayment = $service->details(request('tr_ref_id'));
            return $this->successResponse(new RequestMoneyDetailResource($requestPayment));
        } catch (AcceptMoneyException $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        } catch (Exception $e) {
            // Common return value for every other exception
        }
        return $this->unprocessableResponse([], __("Failed to process the request."));
    }

    /**
     * Check maximum and minimum limit and wallet balance
     *
     * @param CheckAmountLimitRequest $request
     * @param AcceptMoneyService $service
     * @return JsonResponse
     */
    public function checkAmountLimit(CheckAmountLimitRequest $request, AcceptMoneyService $service)
    {
        try {
            $response = $service->checkAmountLimit($request->amount, $request->currency_id, auth()->user()->id);
            if ($response['status']) {
                return $this->okResponse();
            }
            return $this->unprocessableResponse($response);
        } catch (AcceptMoneyException $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        } catch (Exception $e) {
            // Common return value for every other exception
        }
        return $this->unprocessableResponse([], __("Failed to process the request."));
    }

    /**
     * Get fees for request money
     * @param GetFeesRequest $request
     * @param AcceptMoneyService $service
     * @return JsonResponse
     */
    public function getFees(GetFeesRequest $request, AcceptMoneyService $service)
    {
        try {
            $response = $service->getFees($request->amount, $request->currency_id);
            if (!$response['status']) {
                return $this->unprocessableResponse($response);
            }
            return $this->successResponse($response);
        } catch (AcceptMoneyException $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        } catch (Exception $e) {
            // Common return value for every other exception
        }
        return $this->unprocessableResponse([], __("Failed to process the request."));
    }

    /**
     * Accept request money
     * @param AcceptMoneyService $service
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request, AcceptMoneyService $service)
    {
        try {
            $trId         = request('tr_id');
            $amount       = request('amount');
            $userId       = auth()->user()->id;
            $currencyId   = request('currency_id');
            $emailOrPhone = request('emailOrPhone');
            $processedBy  = preference('processed_by');
            $response     = $service->store($trId, $amount, $userId, $currencyId, $emailOrPhone, $processedBy);
        } catch (CurrencyException $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        }  catch (AcceptMoneyException $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        } catch (PaymentFailedException $e) {
            return $this->unprocessableResponse($e->getDecodedMessage());
        } catch (Exception $e) {
            return $this->unprocessableResponse([], __("Failed to process the request."));
        }
        return $this->okResponse($response);
    }

    /**
     * Cancel request money by request creator
     * @param AcceptMoneyService $service
     * @return JsonResponse
     */
    public function cancelByCreator(AcceptMoneyService $service)
    {
        try {
            DB::beginTransaction();
            $response = $service->cancel(request('tr_id'), auth()->user()->id);
            if (!$response['status']) {
                return $this->unprocessableResponse($response);
            }
            DB::commit();
            $response = $service->sendCancelNotificationToReceiver($response['requestPayment']);
            if (!$response['status']) {
                return $this->unprocessableResponse($response);
            }
            return $this->okResponse($response);
        } catch (Exception $e) {
            DB::rollBack();
        }
        return $this->unprocessableResponse([], __("Failed to process the request."));
    }

    /**
     * Cancel request money by request receiver
     * @param AcceptMoneyService $service
     * @return JsonResponse
     */
    public function cancelByReceiver(AcceptMoneyService $service)
    {
        try {
            DB::beginTransaction();
            $response = $service->cancel(request('tr_id'), auth()->user()->id);
            if (!$response['status']) {
                return $this->unprocessableResponse($response);
            }
            DB::commit();
            $response = $service->sendCancelNotificationToCreator($response['requestPayment']);
            if (!$response['status']) {
                return $this->unprocessableResponse($response);
            }
            return $this->okResponse($response);
        } catch (Exception $e) {
            DB::rollBack();
        }
        return $this->unprocessableResponse([], __("Failed to process the request."));
    }
    
}
