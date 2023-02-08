<?php
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('login', 'LoginController@login');
Route::post('registration', 'RegistrationController@registration');
Route::post('duplicate-email-check', 'RegistrationController@checkDuplicateEmail');
Route::post('duplicate-phone-number-check', 'RegistrationController@checkDuplicatePhoneNumber');
Route::get('default-country-short-name', 'CountryController@getDefaultCountryShortName');
Route::get('countries', 'CountryController@list');
Route::get('user-types', 'PreferenceController@userRoles');

/**
 * Preference routes
 */
Route::group(['prefix' => 'preference'], function () {
    Route::get('/', 'PreferenceController@preferenceSettings');
    Route::get('custom', 'PreferenceController@customSetting');
    Route::get('check-login-via', 'PreferenceController@checkLoginVia');
    Route::get('check-processed-by', 'PreferenceController@checkProcessedByApi');
});

Route::group(['middleware' => ['auth:api-v2']], function () {
    Route::get('check-user-status', 'ProfileController@checkUserStatus');
    /**
     * Profile routes
     */
    Route::group(['prefix' => 'profile'], function () {
        Route::get('/summary', 'ProfileController@summary');
        Route::get('/details', 'ProfileController@details');
        Route::put('/update', 'ProfileController@update');
        Route::post('/upload-image', 'ProfileController@uploadImage');
    });
    Route::post('/change-password', 'ProfileController@changePassword');
    Route::get('/default-wallet-balance', 'ProfileController@getDefaultWalletBalance');
    Route::get('/available-balances', 'ProfileController@getUserAvailableWalletsBalance');

    /**
     * Transaction routes
     */
    Route::group(['prefix' => 'transaction'], function () {
        Route::get('activityall', 'TransactionController@list');
        Route::post('details', 'TransactionController@details');
    });

    /**
     * Send money routes
     */
    Route::group(['name' => 'send-money.', 'prefix' => 'send-money'], function () {
        Route::post('/email-check', 'SendMoneyController@emailValidate')->name('validate-email');
        Route::post('/phone-check', 'SendMoneyController@phoneValidate')->name('validate-phone');
        Route::get('/get-currencies', 'SendMoneyController@getCurrencies')->name('get-currencies');
        Route::post('/check-amount-limit', 'SendMoneyController@amountLimitCheck')->name('check-amount-limit');
        Route::post('/confirm', 'SendMoneyController@sendMoneyConfirm')->name('confirm');
    });

    /**
     * Exchange money routes
     */
    Route::group(['prefix' => 'exchange-money'], function () {
        Route::get('get-currencies', 'ExchangeMoneyController@getCurrencies');
        Route::post('amount-limit-check', 'ExchangeMoneyController@exchangeLimitCheck');
        Route::post('get-wallets-balance', 'ExchangeMoneyController@getExchangeWalletsBalance');
        Route::post('get-destination-wallets', 'ExchangeMoneyController@getExchangableDestinations');
        Route::post('get-exchange-rate', 'ExchangeMoneyController@getCurrenciesExchangeRate');
        Route::post('confirm-details', 'ExchangeMoneyController@reviewExchangeDetails');
        Route::post('complete', 'ExchangeMoneyController@exchangeMoneyComplete');
    });

    // Request Money routes
    Route::group(['prefix' => 'request-money'], function () {
        Route::post('email-check', 'RequestMoneyController@checkEmail');
        Route::post('phone-check', 'RequestMoneyController@checkPhone');
        Route::get('currencies', 'RequestMoneyController@getCurrency');
        Route::post('confirm', 'RequestMoneyController@store');
        Route::post('accept', 'AcceptCancelRequestMoneyController@store');
        Route::post('cancel-by-creator', 'AcceptCancelRequestMoneyController@cancelByCreator');
        Route::post('cancel-by-receiver', 'AcceptCancelRequestMoneyController@cancelByReceiver');

    });
    
    // Accept Money routes
    Route::group(['prefix' => 'accept-money'], function () {
        Route::get('details', 'AcceptCancelRequestMoneyController@details');
        Route::post('amount-limit-check', 'AcceptCancelRequestMoneyController@checkAmountLimit');
        Route::get('fees', 'AcceptCancelRequestMoneyController@getFees');
    });

    /**
     * Payout setting routes
     */
    Route::group(['prefix' => 'payout-setting'], function () {
        Route::get('/payment-methods', 'PayoutSettingController@paymentMethods');
        Route::get('/crypto-currencies', 'PayoutSettingController@cryptoCurrencies');
    });
    Route::resource('/payout-settings', PayoutSettingController::class);
    
});
