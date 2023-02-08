<?php

/**
 * @package ProfileController
 * @author tehcvillage <support@techvill.org>
 * @contributor Md Abdur Rahaman <[abdur.techvill@gmail.com]>
 * @created 05-12-2022
 */

namespace App\Http\Controllers\Api\V2;

use Illuminate\Http\Request;
use App\Exceptions\Api\V2\{
    UserProfileException,
    LoginException,
    WalletException
};
use App\Http\Requests\{
    UpdatePasswordRequest,
    UploadUserProfilePictureRequest
};
use App\Services\{
    UserProfileService,
    WalletService
};
use App\Models\{
    Wallet, 
    User
};
use Exception;
use App\Http\Controllers\Controller;

/**
 * @group  User Profile
 * 
 * API to manage user profile
 */
class ProfileController extends Controller
{
    /**
     * Show User Profile summary
     *
     * @param UserProfileService $service
     * @return JsonResponse
     * @throws LoginException
     */
    public function summary(UserProfileService $service)
    {
        try {
            $userId   = auth()->user()->id;
            $response = $service->getProfileSummary($userId);
            return $this->successResponse($response);
        } catch (LoginException $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        } catch (Exception $e) {
            return $this->unprocessableResponse([], __("Failed to process the request."));
        }
    }

    /**
     * Show User Profile details
     *
     * @param UserProfileService $service
     * @return JsonResponse
     * @throws LoginException
     */
    public function details(UserProfileService $service)
    {
        try {
            $userId   = auth()->user()->id;
            $response = $service->getProfileDetails($userId);
            return $this->successResponse($response);
        } catch (LoginException $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        } catch (Exception $e) {
            return $this->unprocessableResponse([], __("Failed to process the request."));
        }
    }

    /**
     * Update User Profile informatpion
     *
     * @param Request $request
     * @param UserProfileService $service
     * @return JsonResponse
     * @throws Exception
     */
    public function update(Request $request, UserProfileService $service)
    {
        try {
            $userId         = auth()->user()->id;
            $userInfo       = $request->only('first_name', 'last_name'); 
            $userDetailInfo = $request->only('country_id', 'address_1', 'address_2', 'city', 'state', 'timezone');
            $service->updateProfileInformation($userId, $userInfo, $userDetailInfo);
            $defaultWallet = $request->default_wallet;
            (new WalletService())->changeDefaultWallet($userId, $defaultWallet);
            return $this->okResponse();
        } catch (Exception $e) {
            return $this->unprocessableResponse([], __("Failed to process the request."));
        }
    }

    /**
     * Change User Profile Picture
     *
     * @param UploadUserProfilePictureRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function uploadImage(UploadUserProfilePictureRequest $request, UserProfileService $service)
    {
        try {
            if ($request->hasFile('image')) {
                $userId   = auth()->user()->id;
                $image    = $request->file('image');
                $response = $service->uploadImage($userId, $image);
            }
            if (true === $response['status']) {
                return $this->okResponse([], $response['message']);
            }
            return $this->unprocessableResponse([], $response['message']);
        } catch (UserProfileException $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        } catch (Exception $e) {
            return $this->unprocessableResponse([], __("Failed to process the request."));
        }
    }

    /**
     * Change User Password
     *
     * @param UpdatePasswordRequest $request
     * @param UserProfileService $service
     * @return JsonResponse
     * @throws UserProfileException
     */
    public function changePassword(UpdatePasswordRequest $request, UserProfileService $service)
    {
        try {
            $userId       = auth()->user()->id;
            $old_password = $request->old_password;
            $password     = $request->password;
            $response     = $service->changePassword($userId, $old_password, $password);
            return $this->okResponse([], $response['message']);
        } catch (UserProfileException $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        } catch (Exception $e) {
            return $this->unprocessableResponse([], __("Failed to process the request."));
        }
    }

    /**
     * Get default Wallet balance
     *
     * @param WalletService $service
     * @throws WalletException
     * @return JsonResponse
     */
    public function getDefaultWalletBalance(WalletService $service)
    {
        try {
            $userId   = auth()->user()->id;
            $response = $service->defaultWalletBalance($userId);
            return $this->okResponse($response);
        } catch (WalletException $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        } catch (Exception $e) {
            return $this->unprocessableResponse([], __("Failed to process the request."));
        }
    }

    /**
     * Get user's all available wallet balances
     *
     * @return JsonResponse
     * @throws WalletException
     */
    public function getUserAvailableWalletsBalance()
    {
        try {
            $userId   = auth()->user()->id;
            $wallet   = new Wallet();
            $wallets  = $wallet->getAvailableBalance($userId);
            if (!$wallets) {
                throw new WalletException(__("No :X found.", ["X" => __("Wallet")]));
            }
            return $this->okResponse($wallets);
        } catch (WalletException $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        } catch (Exception $e) {
            return $this->unprocessableResponse([], __("Failed to process the request."));
        }
    }

    /**
     * Check current user's status
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkUserStatus()
    {
        $status = User::where(['id' => auth()->user()->id])->value('status');
        $response['status'] = __($status);
        return $this->okResponse($response);
    }


}
