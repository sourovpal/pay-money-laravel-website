<?php

/**
 * @package UserProfileService
 * @author tehcvillage <support@techvill.org>
 * @contributor Md Abdur Rahaman <[abdur.techvill@gmail.com]>
 * @created 05-12-2022
 */

namespace App\Services;

use App\Exceptions\Api\V2\UserProfileException;
use App\Http\Resources\User\UserProfileResource;
use App\Models\{Country, 
    Transaction, 
    UserDetail,
    Wallet,
    User,
};
use Hash;

class UserProfileService 
{
    /**
     * Get User Profile Summary
     *
     * @param int $userId
     * @return array (user, last_30_days_transaction, total_wallets, defaultWallet)
     */
    public function getProfileSummary($userId)
    {
        $user = User::with('user_detail', 'user_detail.country:id')->where(['id' => $userId])->first();
        if (!$user) {
            throw new UserProfileException(__("User not found"));
        }
        $response['user'] = new UserProfileResource($user);
        $response['last_30_days_transaction'] = Transaction::where('user_id', $userId)
                                        ->where('created_at', '>', now()->subDays(30)->endOfDay())
                                        ->count();
        $wallets = Wallet::where(['user_id' => $userId]);
        $response['total_wallets'] = $wallets->count();
        $response['defaultWallet'] = $wallets->default()
                                    ->with(['currency:id,code'])
                                    ->first(['currency_id']);
        return $response;
    }

    /**
     * Get User Profile details
     *
     * @param int $userId
     * @return array (user, wallets, timezones, countries)
     */
    public function getProfileDetails($userId)
    {
        $user = User::with('user_detail', 'user_detail.country:id')->where(['id' => $userId])->first();
        if (!$user) {
            throw new UserProfileException(__("User not found"));
        }
        $response['user']    = new UserProfileResource($user);
        $response['wallets'] = Wallet::where(['user_id' => $userId])
                                       ->with(['currency' => function ($q) {
                                           $q->type('fiat')->select('id', 'code');
                                        }])
                                        ->get(['id', 'currency_id', 'is_default']);
        $response['timezones'] = phpDefaultTimeZones();  
        $response['countries'] = Country::get(['id', 'name']);                    ;
        return $response;
    }

    /**
     * Update user information
     *
     * @param int $userId
     * @param array $userInfo
     * @param array $userDetailInfo
     * @return void
     */
    public function updateProfileInformation($userId, $userInfo, $userDetailInfo)
    {
        if (!empty($userInfo)) {
            User::where('id', $userId)->update($userInfo);
        }
        if (!empty($userDetailInfo)) {
            UserDetail::where('user_id', $userId)->update($userDetailInfo);
        }
    }

    /**
     * Upload user profile image
     *
     * @param int $userId
     * @param $image
     * @return void
     * @throws UserProfileException
     */
    public function uploadImage($userId, $image)
    {
        $user      = User::find($userId, ['id', 'picture']);
        $extension = strtolower($image->getClientOriginalExtension());
        if (!in_array($extension, getFileExtensions(3))) {
            throw new UserProfileException(__("Invalid image format detected."));
        }
        $response = uploadImage($image, User::$profilePictureDirectory, '100*100', $user->picture, '70*70');
        if (true === $response['status']) {
            $user->picture = $response['file_name'];
            $user->save();
        }
        return $response;
    }

    /**
     * Change User Password
     *
     * @param int $userId
     * @param string $oldPassword
     * @param string $password
     * @return array $response (message)
     */
    public function changePassword($userId, $oldPassword, $password)
    {
        $user = User::where(['id' => $userId])->first(['id', 'password']);
        if (!$user) {
            throw new UserProfileException(__("User not found"));
        }
        if (!Hash::check($oldPassword, $user->password)) {
            throw new UserProfileException(__("Old Password is Wrong!"));
        } 
        $user->password = Hash::make($password);
        $user->save();
        $response['message'] = __('Password Updated successfully!');
        return $response;
    }
    
    
}
