<?php

/**
 * @package RegistrationController
 * @author tehcvillage <support@techvill.org>
 * @contributor Md Abdur Rahaman <[abdur.techvill@gmail.com]>
 * @created 06-12-2022
 */

namespace App\Http\Controllers\Api\V2;

use App\Services\Mail\UserVerificationMailService;
use Illuminate\Http\Request;
use App\Http\Requests\{
    CheckDuplicatePhoneNumberRequest,
    CheckDuplicateEmailRequest,
    UserStoreRequest
};
use App\Models\{
    RoleUser,
    User,
    QrCode,
    VerifyUser,
};
use DB, Exception;
use App\Http\Controllers\Controller;

class RegistrationController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = new User();
    }

    /**
     * Check duplicate email during registration
     *
     * @param CheckDuplicateEmailRequest $request
     * @return JsonResponse
     */
    public function checkDuplicateEmail(CheckDuplicateEmailRequest $request)
    {
        $data['status']  = true;
        $data['success'] = __("Email Available!");
        return $this->successResponse($data);
    }

    /**
     * Check duplicate phone number during registration
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkDuplicatePhoneNumber(CheckDuplicatePhoneNumberRequest $request)
    {
        $data['status']  = true;
        $data['success'] = __("The phone number is Available!");
        return $this->successResponse($data);
    }

    /**
     * User Registration
     *
     * @param UserStoreRequest $request
     * @return JsonResponse
    */
    public function registration(UserStoreRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = $this->user->createNewUser($request, 'user');
            RoleUser::insert(['user_id' => $user->id, 'role_id' => $user->role_id, 'user_type' => 'User']);
            $this->user->createUserDetail($user->id);
            $this->user->createUserDefaultWallet($user->id, settings('default_currency'));
            if ('none' != settings('allowed_wallets')) {
                $this->user->createUserAllowedWallets($user->id, settings('allowed_wallets'));
            }
            (new QrCode())->createUserQrCode($user);
            $userEmail          = $user->email;
            $userFormattedPhone = $user->formattedPhone;
            $this->user->processUnregisteredUserTransfers($userEmail, $userFormattedPhone, $user, settings('default_currency'));
            $this->user->processUnregisteredUserRequestPayments($userEmail, $userFormattedPhone, $user, settings('default_currency'));
            if (!$user->user_detail->email_verification) {
                if ("Enabled" == preference('verification_mail')) {
                    (new VerifyUser())->createVerifyUser($user->id);
                    DB::commit();
                    $response = (new UserVerificationMailService)->send($user);
                    if (!$response['status']) {
                        return $this->unprocessableResponse($response);
                    }
                    return $this->successResponse($response);
                } 
            }
            DB::commit();
            $success['message'] = __("Registration Successfull!");
            return $this->successResponse($success);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->unprocessableResponse([], __("Failed to process the request."));
        }
    }

}
