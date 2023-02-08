<?php

/**
 * @package AuthService
 * @author tehcvillage <support@techvill.org>
 * @contributor Md Abdur Rahaman <[abdur.techvill@gmail.com]>
 * @created 30-11-2022
 */

namespace App\Services;

use App\Services\Mail\UserVerificationMailService;
use App\Exceptions\Api\V2\LoginException;
use App\Models\{
    VerifyUser,
    Wallet,
    User,
};
use Auth, DB;

class AuthService 
{
    /**
     * Get User email by login method
     *
     * @param string $email
     * @return array
     */
    public function getUserEmailByLoginMethod($email)
    {
        $loginVia = settings('login_via');
        switch ($loginVia) {
            case 'phone_only':
                return $this->checkUserByPhone($email);
                break;

            case 'email_or_phone':
                if (strpos($email, '@') !== false) {
                    return $this->checkUserByEmail($email);
                } else {
                    return $this->checkUserByPhone($email);
                }
                break;
            
            default:
                return $this->checkUserByEmail($email);
                break;
        }
    }

    /**
     * Check user by phone number
     *
     * @param string $phone
     * @return array
     */
    public function checkUserByPhone($phone)
    {
        $success = ['status' => true];
        $formattedRequest = ltrim($phone, '0');
        $phnUser          = User::where(['phone' => $formattedRequest])->orWhere(['formattedPhone' => $formattedRequest])->first(['email']);
        if (!$phnUser) {
            $success['status'] = false;
        } else {
            $success['email'] = $phnUser->email;
        }
        return $success;
    }

    /**
     * Check user by email address
     *
     * @param string $email
     * @return array
     */
    public function checkUserByEmail($email)
    {
        $success = ['status' => true];
        $user = User::where(['email' => $email])->first(['email']);
        if (!$user) {
            $success['status'] = false;
        } else {
            $success['email'] = $user->email;
        }
        return $success;
    }

    /**
     * User login
     *
     * @param string $email
     * @param string $password
     * @return array
     * @throws LoginException
     */
    public function login($email, $password)
    {
        DB::beginTransaction();
        $response = ['status' => true, 'user' => null];
        $response = $this->getUserEmailByLoginMethod($email);
        if (!$response['status']) {
            throw new LoginException(__("Invalid email & credentials"));
        }
        $email = $response['email'];
        $user  = User::where(['email' => $email])->first(['status']);
        if (isset($user->status) && 'inactive' == strtolower($user->status)) {
            $response['status']     = false;
            $response['userStatus'] = $user->status;
            $response['message']    = __('Your account is inactivated. Please try again later!');
            return $response;
        }
        if ('Enabled' == preference('verification_mail')) {
            $user = User::where(['email' => $email])->first(['id', 'first_name', 'last_name', 'email', 'status']);
            if (0 == $user->user_detail->email_verification) {
                (new VerifyUser())->createVerifyUser($user->id);
                DB::commit();
                $response = (new UserVerificationMailService())->send($user);
                if ($response['status']) {
                    $response['status'] = false;
                    return $response;
                }
            }
        }
        if (!Auth::attempt(['email' => $email, 'password' => $password])) {
            throw new LoginException(__("Invalid email & credentials"));
        }
        $response['user'] = $user = Auth::user();
        $wallet = Wallet::where(['user_id' => $user->id, 'currency_id' => settings('default_currency')])->first();
        if (empty($wallet)) {
            $wallet    = new Wallet();
            $newWallet = $wallet->createWallet($user->id, settings('default_currency'));
        }
        DB::commit();
        return $response;
    }
    
    public function registration($request)
    {
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
    }
    
}
