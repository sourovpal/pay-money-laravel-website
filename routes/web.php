<?php

Route::get('/clearapp', function ()
{
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Session::flush();
    return redirect('/');
});

Route::get('/', 'HomeController@index');

// changing-language
Route::get('change-lang', 'HomeController@setLocalization');

//coinPayment IPN
Route::post('coinpayment/check', 'Users\DepositController@coinpaymentCheckStatus');

// user email check on registration
Route::post('user-registration-check-email', 'Auth\RegisterController@checkUserRegistrationEmail');

// Unauthenticated Admin
Route::group(['prefix' => Config::get('adminPrefix'), 'namespace' => 'Admin', 'middleware' => ['no_auth:admin', 'locale', 'ip_middleware']], function ()
{
    Route::get('/', function ()
    {
        return view('admin.auth.login');
    })->name('admin');

    Route::post('adminlog', 'AdminController@authenticate');
    Route::match(['GET', 'POST'], 'forget-password', 'AdminController@forgetPassword');
    Route::get('password/resets/{token}', 'AdminController@verifyToken');
    Route::post('confirm-password', 'AdminController@confirmNewPassword');
});

// Authenticated Admin
Route::group(['prefix' => Config::get('adminPrefix'), 'namespace' => 'Admin', 'middleware' => ['guest:admin', 'locale', 'ip_middleware']], function ()
{
    Route::get('home', 'DashboardController@index')->name('dashboard');
    Route::get('adminlogout', 'AdminController@logout');
    Route::get('profile', 'AdminController@profile');
    Route::post('update-admin/{id}', 'AdminController@update');

    Route::get('change-password', 'AdminController@changePassword');
    Route::post('change-password', 'AdminController@updatePassword');

    Route::post('check-password', 'AdminController@passwordCheck');

    // Change language
    Route::post('change-lang', 'DashboardController@switchLanguage');

    // users
    Route::get('users', 'UserController@index')->middleware(['permission:view_user']);
    Route::get('users/create', 'UserController@create')->middleware(['permission:add_user']);
    Route::post('users/store', 'UserController@store');
    Route::get('users/view/{id}', 'UserController@show');
    Route::get('users/edit/{id}', 'UserController@edit')->middleware(['permission:edit_user']);
    Route::post('users/update', 'UserController@update');
    Route::get('users/delete/{id}', 'UserController@destroy')->middleware(['permission:delete_role']);

    Route::post('email_check', 'UserController@postEmailCheck');
    Route::post('duplicate-phone-number-check', 'UserController@duplicatePhoneNumberCheck');
    Route::get('users/transactions/{id}', 'UserController@eachUserTransaction');

    //Admin Can deposit for a user
    Route::match(array('GET', 'POST'), 'users/deposit/create/{id}', 'UserController@eachUserDeposit');
    Route::post('users/deposit/amount-fees-limit-check', 'UserController@amountFeesLimitCheck');
    Route::post('users/deposit/storeFromAdmin', 'UserController@eachUserDepositSuccess');
    Route::get('users/deposit/print/{id}', 'UserController@eachUserdepositPrintPdf');

    //Admin Can withdraw for a user
    Route::match(array('GET', 'POST'), 'users/withdraw/create/{id}', 'UserController@eachUserWithdraw');
    Route::post('users/withdraw/amount-fees-limit-check', 'UserController@amountFeesLimitCheck');
    Route::post('users/withdraw/storeFromAdmin', 'UserController@eachUserWithdrawSuccess');
    Route::get('users/withdraw/print/{id}', 'UserController@eachUserWithdrawPrintPdf');

    Route::get('users/wallets/{id}', 'UserController@eachUserWallet');
    Route::get('users/tickets/{id}', 'UserController@eachUserTicket');
    Route::get('users/disputes/{id}', 'UserController@eachUserDispute');

    // admin_users
    Route::get('admin_users', 'UserController@adminList')->middleware(['permission:view_admins']);
    Route::get('admin-user/create', 'UserController@adminCreate')->middleware(['permission:add_admin']);
    Route::post('admin-users/store', 'UserController@adminStore');
    Route::get('admin-user/edit/{id}', 'UserController@adminEdit')->middleware(['permission:edit_admin']);
    Route::post('admin-users/update', 'UserController@adminUpdate');
    Route::get('admin-user/delete/{id}', 'UserController@adminDestroy')->middleware(['permission:delete_admin']);

    // Merchants
    Route::get('merchants', 'MerchantController@index')->middleware(['permission:view_merchant']);
    Route::get('merchant/edit/{id}', 'MerchantController@edit')->middleware(['permission:edit_merchant']);
    Route::post('merchant/update', 'MerchantController@update');
    Route::post('merchant/logo_delete', 'MerchantController@deleteLogo');
    Route::post('merchant/delete-merchant-logo', 'MerchantController@deleteMerchantLogo');
    Route::get('merchant/payments/{id}', 'MerchantController@eachMerchantPayment');
    Route::get('merchants/userSearch', 'MerchantController@merchantsUserSearch');
    Route::get('merchants/csv', 'MerchantController@merchantCsv');
    Route::get('merchants/pdf', 'MerchantController@merchantPdf');
    Route::post('merchants/change-fee-with-group-change', 'MerchantController@changeMerchantFeeWithGroupChange');

    //Merchant Payments
    Route::get('merchant_payments', 'MerchantPaymentController@index')->middleware(['permission:view_merchant_payment']);
    Route::get('merchant_payments/edit/{id}', 'MerchantPaymentController@edit')->middleware(['permission:edit_merchant_payment']);
    Route::post('merchant_payments/update', 'MerchantPaymentController@update');
    Route::get('merchant_payments/csv', 'MerchantPaymentController@merchantPaymentCsv');
    Route::get('merchant_payments/pdf', 'MerchantPaymentController@merchantPaymentPdf');

    // Transactions
    Route::get('transactions', 'TransactionController@index')->middleware(['permission:view_transaction']);
    Route::get('transactions/edit/{id}', 'TransactionController@edit')->middleware(['permission:edit_transaction']);
    Route::post('transactions/update/{id}', 'TransactionController@update');
    Route::get('transactions_user_search', 'TransactionController@transactionsUserSearch');
    Route::get('transactions/csv', 'TransactionController@transactionCsv');
    Route::get('transactions/pdf', 'TransactionController@transactionPdf');

    // Deposits
    Route::get('deposits', 'DepositController@index')->middleware(['permission:view_deposit']);
    Route::get('deposits/edit/{id}', 'DepositController@edit')->middleware(['permission:edit_deposit']);
    Route::post('deposits/update', 'DepositController@update');
    Route::get('deposits/user_search', 'DepositController@depositsUserSearch');
    Route::get('deposits/csv', 'DepositController@depositCsv');
    Route::get('deposits/pdf', 'DepositController@depositPdf');

    // Withdrawals
    Route::get('withdrawals', 'WithdrawalController@index')->middleware(['permission:view_withdrawal']);
    Route::get('withdrawals/edit/{id}', 'WithdrawalController@edit')->middleware(['permission:edit_withdrawal']);
    Route::post('withdrawals/update', 'WithdrawalController@update');
    Route::get('withdrawals/user_search', 'WithdrawalController@withdrawalsUserSearch');
    Route::get('withdrawals/csv', 'WithdrawalController@withdrawalCsv');
    Route::get('withdrawals/pdf', 'WithdrawalController@withdrawalPdf');

    // Transfers
    Route::get('transfers', 'MoneyTransferController@index')->middleware(['permission:view_transfer']);
    Route::get('transfers/edit/{id}', 'MoneyTransferController@edit')->middleware(['permission:edit_transfer']);
    Route::post('transfers/update', 'MoneyTransferController@update');
    Route::get('transfers/user_search', 'MoneyTransferController@transfersUserSearch');
    Route::get('transfers/csv', 'MoneyTransferController@transferCsv');
    Route::get('transfers/pdf', 'MoneyTransferController@transferPdf');

    // Currency Exchanges
    Route::get('exchanges', 'ExchangeController@index')->middleware(['permission:view_exchange']);
    Route::get('exchange/edit/{id}', 'ExchangeController@edit')->middleware(['permission:edit_exchange']);
    Route::post('exchange/update', 'ExchangeController@update');
    Route::get('exchanges/user_search', 'ExchangeController@exchangesUserSearch');
    Route::get('exchanges/csv', 'ExchangeController@exchangeCsv');
    Route::get('exchanges/pdf', 'ExchangeController@exchangePdf');

    // Request Payments
    Route::get('request_payments', 'RequestPaymentController@index')->middleware(['permission:view_request_payment']);
    Route::get('request_payments/edit/{id}', 'RequestPaymentController@edit')->middleware(['permission:edit_request_payment']);
    Route::post('request_payments/update', 'RequestPaymentController@update');
    Route::get('request_payments/user_search', 'RequestPaymentController@requestpaymentsUserSearch');
    Route::get('request_payments/csv', 'RequestPaymentController@requestpaymentCsv');
    Route::get('request_payments/pdf', 'RequestPaymentController@requestpaymentPdf');

    // Revenues
    Route::get('revenues', 'RevenueController@revenues_list')->middleware(['permission:view_revenue']);
    Route::get('revenues/user_search', 'RevenueController@revenuesUserSearch');
    Route::get('revenues/csv', 'RevenueController@revenueCsv');
    Route::get('revenues/pdf', 'RevenueController@revenuePdf');

    // disputes
    Route::get('disputes', 'DisputeController@index')->middleware(['permission:view_disputes']);

    Route::get('dispute/add/{id}', 'DisputeController@add');
    Route::post('dispute/open', 'DisputeController@store');

    Route::get('dispute/discussion/{id}', 'DisputeController@discussion')->middleware(['permission:edit_dispute']);
    Route::post('dispute/reply', 'DisputeController@storeReply');
    Route::post('dispute/change_reply_status', 'DisputeController@changeReplyStatus');
    Route::get('disputes_user_search', 'DisputeController@disputesUserSearch');
    Route::get('disputes_transactions_search', 'DisputeController@disputesTransactionsSearch');
    Route::get('dispute/download/{file}', 'DisputeController@download');

    // Tickets
    Route::get('tickets/list', 'TicketController@index')->middleware(['permission:view_tickets']);
    Route::get('tickets/add', 'TicketController@create')->middleware(['permission:add_ticket']);
    Route::post('tickets/store', 'TicketController@store');
    Route::get('ticket_user_search', 'TicketController@ticketUserSearch');
    Route::get('tickets/reply/{id}', 'TicketController@reply')->middleware(['permission:edit_ticket']);
    Route::post('tickets/change_ticket_status', 'TicketController@change_ticket_status');
    Route::post('tickets/reply/store', 'TicketController@adminTicketReply');
    Route::post('tickets/reply/update', 'TicketController@replyUpdate');
    Route::post('tickets/reply/delete', 'TicketController@replyDelete');
    Route::get('tickets/edit/{id}', 'TicketController@edit')->middleware(['permission:edit_ticket']);
    Route::post('tickets/update', 'TicketController@update');
    Route::get('tickets/delete/{id}', 'TicketController@delete')->middleware(['permission:delete_ticket']);
    Route::get('ticket/download/{file}', 'TicketController@download');

    // Email Templates
    Route::get('template/{id}', 'EmailTemplateController@index')->middleware(['permission:view_email_template']);
    Route::post('template_update/{id}', 'EmailTemplateController@update')->middleware(['permission:edit_email_template']);

    Route::get('sms-template/{id}', 'SmsTemplateController@index')->middleware(['permission:view_sms_template']);
    Route::post('sms-template/update/{id}', 'SmsTemplateController@update')->middleware(['permission:edit_sms_template']);

    // Activity Logs
    Route::get('activity_logs', 'ActivityLogController@activities_list')->middleware(['permission:view_activity_log']);

    // Verifications - identity-proofs
    Route::get('identity-proofs', 'IdentityProofController@index')->middleware(['permission:view_identity_verfication']);
    Route::get('identity-proofs/csv', 'IdentityProofController@identityProofsCsv');
    Route::get('identity-proofs/pdf', 'IdentityProofController@identityProofsPdf');
    Route::get('identity-proofs/edit/{id}', 'IdentityProofController@identityProofEdit')->middleware(['permission:edit_identity_verfication']);
    Route::post('identity-proofs/update', 'IdentityProofController@identityProofUpdate');

    // Verifications - address-proofs
    Route::get('address-proofs', 'AddressProofController@index')->middleware(['permission:view_address_verfication']);
    Route::get('address-proofs/csv', 'AddressProofController@addressProofsCsv');
    Route::get('address-proofs/pdf', 'AddressProofController@addressProofsPdf');
    Route::get('address-proofs/edit/{id}', 'AddressProofController@addressProofEdit')->middleware(['permission:edit_address_verfication']);
    Route::post('address-proofs/update', 'AddressProofController@addressProofUpdate');

    // currencies
    Route::get('settings/currency', 'CurrencyController@index')->middleware(['permission:view_currency']);
    Route::match(array('GET', 'POST'), 'settings/add_currency', 'CurrencyController@add')->middleware(['permission:add_currency']);
    Route::match(array('GET', 'POST'), 'settings/edit_currency/{id}', 'CurrencyController@update')->middleware(['permission:edit_currency']);
    Route::get('settings/delete_currency/{id}', 'CurrencyController@delete')->middleware(['permission:delete_currency']);
    Route::post('currency/image_delete', 'CurrencyController@deleteImage');
    Route::post('settings/currency/delete-currency-logo', 'CurrencyController@deleteCurrencyLogo');

    // FeesLimit
    Route::get('settings/feeslimit/{tab}/{id}', 'FeesLimitController@limitList')->middleware(['permission:edit_currency']);
    Route::post('settings/get-feeslimit-details', 'FeesLimitController@getFesslimitDetails');
    Route::post('settings/feeslimit/update-deposit-limit', 'FeesLimitController@updateDepositLimit');
    Route::post('settings/get-specific-currency-details', 'FeesLimitController@getSpecificCurrencyDetails');

    //Currency PaymentMethod Settings
    Route::get('settings/payment-methods/{tab}/{id}', 'CurrencyPaymentMethodController@paymentMethodList')->middleware(['permission:edit_currency']);
    Route::post('settings/payment-methods/update-paymentMethod-Credentials', 'CurrencyPaymentMethodController@updatePaymentMethodCredentials');
    Route::post('settings/get-payment-methods-details', 'CurrencyPaymentMethodController@getPaymentMethodsDetails');
    Route::post('settings/get-payment-methods-specific-currency-details', 'CurrencyPaymentMethodController@getPaymentMethodsSpecificCurrencyDetails');

    //bank
    Route::post('settings/payment-methods/add-bank', 'CurrencyPaymentMethodController@addBank');
    Route::post('settings/payment-methods/update-bank', 'CurrencyPaymentMethodController@updateBank');
    Route::post('settings/payment-methods/delete-bank', 'CurrencyPaymentMethodController@deleteBank');
    Route::post('settings/payment-methods/getCpmId', 'CurrencyPaymentMethodController@getCpmId');
    Route::post('settings/payment-methods/show-bank-details', 'CurrencyPaymentMethodController@showbankDetails');
    Route::post('settings/payment-methods/delete-bank-logo', 'CurrencyPaymentMethodController@deleteBankLogo');

    //settings
    Route::match(array('GET', 'POST'), 'settings', 'SettingController@general');

    Route::post('settings/update-sidebar-company-logo', 'SettingController@updateSideBarCompanyLogo');
    Route::post('settings/logo-delete', 'SettingController@deleteLogo');
    Route::post('settings/logo-delete', 'SettingController@deleteLogo');
    Route::post('settings/check-sms-settings', 'SettingController@checkSmsGatewaySettings');
    Route::post('settings/delete-logo', 'SettingController@deleteSettingLogo');
    Route::post('settings/delete-favicon', 'SettingController@deleteSettingFavicon');

    // Admin Security Settings
    Route::match(['get', 'post'], 'settings/admin-security-settings', 'SettingController@adminSecuritySettings')->middleware(['permission:view_admin_security']);

    //social_links
    Route::match(array('GET', 'POST'), 'settings/social_links', 'SettingController@social_links')->middleware(['permission:view_social_links']);

    Route::get('settings/theme-set/{theme}', 'SettingController@themeSet')->middleware(['permission:view_social_links']);

    //api_informations
    Route::match(array('GET', 'POST'), 'settings/api_informations', 'SettingController@api_informations')->middleware(['permission:view_api_credentials']);

    // currency conversion rate api
    Route::match(array('GET', 'POST'), 'settings/currency-conversion-rate-api', 'SettingController@currencyConversionRateApi')->middleware(['permission:view_conversion_rate_api']);

    //appstore credentials
    Route::get('settings/app-store-credentials', 'AppStoreCredentialController@getAppStoreCredentials')->middleware(['permission:view_appstore_credentials']);
    Route::post('settings/app-store-credentials/update-google-credentials', 'AppStoreCredentialController@updateGoogleCredentials');
    Route::post('settings/app-store-credentials/update-apple-credentials', 'AppStoreCredentialController@updateAppleCredentials');
    Route::post('settings/app-store-credentials/delete-playstore-logo', 'AppStoreCredentialController@deletePlaystoreLogo');
    Route::post('settings/app-store-credentials/delete-appstore-logo', 'AppStoreCredentialController@deleteAppStoreLogo');

    //email_settings
    Route::match(array('GET', 'POST'), 'settings/email', 'SettingController@email')->middleware(['permission:view_email_setting']);

    // Route::match(array('GET', 'POST'), 'settings/sms', 'SettingController@sms')->middleware(['permission:view_sms_setting']);

    Route::match(array('GET', 'POST'), 'settings/sms/{type}', 'SettingController@sms')->middleware(['permission:view_sms_setting']);

    //countries
    Route::get('settings/country', 'CountryController@index')->middleware(['permission:view_country']);
    Route::match(array('GET', 'POST'), 'settings/add_country', 'CountryController@add')->middleware(['permission:add_country']);
    Route::match(array('GET', 'POST'), 'settings/edit_country/{id}', 'CountryController@update')->middleware(['permission:edit_country']);
    Route::get('settings/delete_country/{id}', 'CountryController@delete')->middleware(['permission:delete_country']);

    //languages
    Route::get('settings/language', 'LanguageController@index')->middleware(['permission:view_language']);
    Route::match(array('GET', 'POST'), 'settings/add_language', 'LanguageController@add')->middleware(['permission:add_language']);
    Route::match(array('GET', 'POST'), 'settings/edit_language/{id}', 'LanguageController@update')->middleware(['permission:edit_language']);
    Route::get('settings/delete_language/{id}', 'LanguageController@delete')->middleware(['permission:delete_language']);
    Route::post('settings/language/delete-flag', 'LanguageController@deleteFlag');

    //Merchant Group/Roles
    Route::get('settings/merchant-group', 'MerchantGroupController@index')->middleware(['permission:view_merchant_group']);
    Route::match(array('GET', 'POST'), 'settings/add-merchant-group', 'MerchantGroupController@add')->middleware(['permission:add_merchant_group']);
    Route::match(array('GET', 'POST'), 'settings/edit-merchant-group/{id}', 'MerchantGroupController@update')->middleware(['permission:edit_merchant_group']);
    Route::get('settings/delete-merchant-group/{id}', 'MerchantGroupController@delete')->middleware(['permission:delete_merchant_group']);

    //User Group/Roles
    Route::get('settings/user_role', 'UsersRoleController@index')->middleware(['permission:view_group']);
    Route::match(array('GET', 'POST'), 'settings/add_user_role', 'UsersRoleController@add')->middleware(['permission:add_group']);
    Route::match(array('GET', 'POST'), 'settings/edit_user_role/{id}', 'UsersRoleController@update')->middleware(['permission:edit_group']);
    Route::get('settings/delete_user_role/{id}', 'UsersRoleController@delete')->middleware(['permission:delete_group']);
    Route::get('settings/roles/check-user-permissions', 'UsersRoleController@checkUserPermissions');

    //Admin Group/Roles
    Route::get('settings/roles', 'RoleController@index')->middleware(['permission:view_role']);
    Route::match(array('GET', 'POST'), 'settings/add_role', 'RoleController@add')->middleware(['permission:add_role']);
    Route::match(array('GET', 'POST'), 'settings/edit_role/{id}', 'RoleController@update')->middleware(['permission:edit_role']);
    Route::get('settings/delete_role/{id}', 'RoleController@delete')->middleware(['permission:delete_role']);
    Route::post('settings/roles/duplicate-role-check', 'RoleController@duplicateRoleCheck');

    //Database Backup
    Route::get('settings/backup', 'BackupController@index')->middleware(['permission:view_database_backup']);
    Route::get('backup/save', 'BackupController@add')->middleware(['permission:add_database_backup']);
    Route::get('backup/download/{id}', 'BackupController@download')->middleware(['permission:edit_database_backup']);

    //metas
    Route::get('settings/metas', 'MetaController@index')->middleware(['permission:view_meta']);
    Route::match(array('GET', 'POST'), 'settings/edit_meta/{id}', 'MetaController@update')->middleware(['permission:edit_meta']);

    //Pages
    Route::get('settings/pages', 'PagesController@index')->middleware(['permission:view_page']);
    Route::get('settings/page/add', 'PagesController@add')->middleware(['permission:add_page']);
    Route::post('settings/page/store', 'PagesController@store');
    Route::get('settings/page/edit/{id}', ['uses' => 'PagesController@edit', 'as' => 'admin.page.edit'])->middleware(['permission:edit_page']);
    Route::post('settings/page/update', 'PagesController@update');
    Route::get('settings/page/delete/{id}', 'PagesController@delete')->middleware(['permission:delete_page']);

    //Preferences
    Route::get('settings/preference', 'SettingController@preference')->middleware(['permission:view_preference']);
    Route::post('save-preference', 'SettingController@savePreference')->middleware(['permission:edit_preference']);

    //Enable Woocommerce
    Route::match(array('GET', 'POST'), 'settings/enable-woocommerce', 'SettingController@enableWoocommerce')->middleware(['permission:view_enable_woocommerce']);

    //Notifications
    Route::get('settings/notification-types', 'NotificationTypeController@index')->middleware(['permission:view_notification_type']);
    Route::get('settings/notification-types/edit/{id}', 'NotificationTypeController@edit')->middleware(['permission:edit_notification_type']);
    Route::post('settings/notification-types/update/{id}', 'NotificationTypeController@update')->middleware(['permission:edit_notification_type']);
    Route::post('settings/notification-type-name/check', 'NotificationTypeController@uniqueNotificationTypeNameCheck');
    Route::get('settings/notification-settings/{type}', 'NotificationSettingController@index')->middleware(['permission:view_notification_setting']);
    Route::post('settings/notification-settings/update', 'NotificationSettingController@update')->middleware(['permission:edit_notification_setting']);

    // Addon
    Route::match(array('GET', 'POST'), '/custom/addons', 'AddonController@index');
    Route::get('/custom/addon/activation/{status}/{id}', 'AddonController@activation');

    // ModuleManager
    Route::get('module-manager/addons', 'ModuleManagerController@index')->middleware(['permission:view_addon_manager']);

    // Crypto Providers
    Route::get('crypto-providers/{provider?}', 'CryptoProviderController@index')->name('admin.crypto_providers.list')->middleware(['permission:view_crypto_provider']);
    Route::post('crypto-provider/{provider}/status-change', 'CryptoProviderController@statusChange')->name('admin.crypto_providers.status_change')->middleware(['permission:edit_crypto_provider']);
});

// Unauthenticated User
Route::group(['middleware' => ['no_auth:users', 'locale']], function ()
{
    Route::get('/login', 'Auth\LoginController@index')->name("login");
    Route::post('/authenticate', 'Auth\LoginController@authenticate');
    Route::get('register', 'Auth\RegisterController@create');
    Route::post('register/duplicate-phone-number-check', 'Auth\RegisterController@registerDuplicatePhoneNumberCheck');
    Route::post('register/store', 'Auth\RegisterController@store');
    Route::get('/user/verify/{token}', 'Auth\RegisterController@verifyUser');
    Route::match(['GET', 'POST'], 'forget-password', 'Auth\ForgotPasswordController@forgetPassword');
    Route::get('password/resets/{token}', 'Auth\ForgotPasswordController@verifyToken');
    Route::post('confirm-password', 'Auth\ForgotPasswordController@confirmNewPassword');
});

//2fa
Route::group(['middleware' => ['guest:users', 'locale', 'check-user-inactive'], 'namespace' => 'Users'], function ()
{
    Route::get('2fa', 'CustomerController@view2fa');
    Route::post('2fa/verify', 'CustomerController@verify2fa');
    Route::get('google2fa', 'CustomerController@viewGoogle2fa')->name('google2fa');
    Route::post('google2fa/verify', 'CustomerController@verifyGoogle2fa');
    Route::post('google2fa/verifyGoogle2faOtp', 'CustomerController@verifyGoogle2faOtp');
});

// Authenticated User
Route::group(['middleware' => ['guest:users', 'locale', 'twoFa', 'check-user-inactive'], 'namespace' => 'Users'], function ()
{
    Route::get('dashboard', 'CustomerController@dashboard');
    Route::get('wallet-list', 'CustomerController@getWallets');

    Route::get('/logout', 'CustomerController@logout');
    Route::get('check-user-status', 'CustomerController@checkUserStatus');
    Route::get('check-request-creator-suspended-status', 'CustomerController@checkRequestCreatorSuspendedStatus');
    Route::get('check-request-creator-inactive-status', 'CustomerController@checkRequestCreatorInactiveStatus');
    Route::get('check-processed-by', 'CustomerController@checkProcessedBy');

    //Settings
    Route::group(['middleware' => ['permission:manage_setting']], function ()
    {
        Route::get('profile', 'CustomerController@profile');
        Route::get('profile/2fa', 'CustomerController@profileTwoFa');
        Route::post('profile/2fa/update', 'CustomerController@UpdateProfileTwoFa');
        Route::post('profile/2fa/ajaxTwoFa', 'CustomerController@ajaxTwoFa');

        //add or update user's qr-code
        Route::post('profile/qr-code/add-or-update', 'CustomerController@addOrUpdateUserProfileQrCode');
        Route::get('profile/qr-code-print/{id}/{printQrCode}', 'CustomerController@printUserQrCode');

        //KYC
        Route::get('profile/personal-id', 'CustomerController@personalId');
        Route::post('profile/personal-id-update', 'CustomerController@updatePersonalId');
        Route::get('profile/personal-address', 'CustomerController@personalAddress');
        Route::post('profile/personal-address-update', 'CustomerController@updatePersonalAddress');

        //google2fa
        Route::post('profile/2fa/google2fa', 'CustomerController@google2fa');
        Route::post('profile/2fa/google2fa/complete-google2fa-verification', 'CustomerController@completeGoogle2faVerification');
        Route::post('profile/2fa/google2fa/otp-verify', 'CustomerController@google2faOtpVerification');

        //2fa
        Route::post('profile/2fa/disabledTwoFa', 'CustomerController@disabledTwoFa');
        Route::post('profile/2fa/ajaxTwoFaSettingsVerify', 'CustomerController@ajaxTwoFaSettingsVerify');
        Route::post('profile/2fa/check-phone', 'CustomerController@checkPhoneFor2fa');
        //

        Route::post('prifile/update_password', 'CustomerController@updateProfilePassword');
        Route::match(['get', 'post'], 'profile-image-upload', 'CustomerController@profileImage');
        Route::post('profile/getVerificationCode', 'CustomerController@generatePhoneVerificationCode');
        Route::post('profile/complete-phone-verification', 'CustomerController@completePhoneVerification');
        Route::post('profile/add-phone-number', 'CustomerController@addPhoneNumberViaAjax'); //without verification
        Route::post('profile/update-phone-number', 'CustomerController@updatePhoneNumberViaAjax');
        Route::post('profile/editGetVerificationCode', 'CustomerController@editGeneratePhoneVerificationCode');
        Route::post('profile/edit-complete-phone-verification', 'CustomerController@editCompletePhoneVerification');
        Route::post('profile/delete-phone-number', 'CustomerController@deletePhoneNumberViaAjax');
        Route::post('prifile/update', 'CustomerController@updateProfileInfo');
        Route::post('profile/duplicate-phone-number-check', 'CustomerController@userDuplicatePhoneNumberCheck');
    });

    // Deposit - Without Suspend Middleware
    Route::group(['middleware' => ['permission:manage_deposit']], function ()
    {
        Route::get('deposit-money/print/{id}', 'DepositController@depositPrintPdf');
    });

    // Deposit - With Suspend Middleware
    Route::group(['middleware' => ['permission:manage_deposit', 'check-user-suspended']], function ()
    {
        Route::match(array('GET', 'POST'), 'deposit', 'DepositController@create');
        Route::post('deposit/getDepositFeesLimit', 'DepositController@getDepositFeesLimit');
        Route::post('deposit/fees-limit-currency-payment-methods-is-active-payment-methods-list', 'DepositController@getDepositMatchedFeesLimitsCurrencyPaymentMethodsSettingsPaymentMethods');
        Route::post('deposit/store', 'DepositController@store');

        //Stripe
        Route::get('deposit/stripe_payment', 'DepositController@stripePayment');
        Route::post('deposit/stripe-make-payment', 'DepositController@stripeMakePayment');
        Route::post('deposit/stripe-confirm-payment', 'DepositController@stripeConfirm');
        Route::get('deposit/stripe-payment/success', 'DepositController@stripePaymentSuccess')->name('deposit.stripe.success');

        //PayPal
        Route::get('deposit/payment_success', 'DepositController@paypalDepositPaymentConfirm');
        Route::get('deposit/payment_cancel', 'DepositController@paymentCancel');
        Route::get('deposit/paypal-payment/success/{amount}', 'DepositController@paypalDepositPaymentSuccess')->name('deposit.paypal.success');

        //2Checkout
        Route::get('deposit/checkout/payment', 'DepositController@checkoutPayment');
        Route::get('deposit/checkout/payment/confirm', 'DepositController@checkoutPaymentConfirm');
        Route::get('deposit/checkout/payment/success', 'DepositController@checkoutPaymentSuccess')->name('deposit.checkout.success');

        //PayUmoney
        Route::get('deposit/payumoney_payment', 'DepositController@payumoneyPayment');
        Route::post('deposit/payumoney_confirm', 'DepositController@payumoneyPaymentConfirm');
        Route::get('deposit/payumoney_success', 'DepositController@payumoneyPaymentSuccess')->name('deposit.payumoney.success');
        Route::post('deposit/payumoney_fail', 'DepositController@payumoneyPaymentFail');

        //Bank
        Route::post('deposit/bank-payment', 'DepositController@bankPaymentConfirm');
        Route::post('deposit/bank-payment/get-bank-detail', 'DepositController@getBankDetailOnChange');
        Route::get('deposit/bank-payment/success', 'DepositController@bankPaymentSuccess')->name('deposit.bank.success');

        //Payeer
        Route::get('deposit/payeer/payment', 'DepositController@payeerPayement');
        Route::get('deposit/payeer/payment/confirm', 'DepositController@payeerPayementConfirm');
        Route::get('deposit/payeer/payment/fail', 'DepositController@payeerPayementFail');
        Route::get('deposit/payeer/payment/status', 'DepositController@payeerPayementStatus');
        Route::get('deposit/payeer/payment/success', 'DepositController@payeerPayementSuccess')->name('deposit.payeer.success');

        //Coinpayment
        Route::post('deposit/make-transaction', 'DepositController@makeCoinPaymentTransaction');
        Route::get('deposit/coinpayment-transaction-info', 'DepositController@viewCoinpaymentTransactionInfo');
    });

    // Withdrawal - Without Suspend Middleware
    Route::group(['middleware' => ['permission:manage_withdrawal']], function ()
    {
        Route::get('payouts', 'WithdrawalController@payouts');
        Route::get('payout/setting', 'WithdrawalController@payoutSetting');
        Route::get('withdrawal-money/print/{id}', 'WithdrawalController@withdrawalPrintPdf');
    });

    // Withdrawal - With Suspend Middleware
    Route::group(['middleware' => ['permission:manage_withdrawal', 'check-user-suspended']], function ()
    {
        Route::post('payout/setting/store', 'WithdrawalController@payoutSettingStore');
        Route::post('payout/setting/update', 'WithdrawalController@payoutSettingUpdate');
        Route::post('payout/setting/delete', 'WithdrawalController@payoutSettingDestroy');
        Route::match(array('GET', 'POST'), 'payout', 'WithdrawalController@withdrawalCreate');
        Route::get('withdrawal/confirm-transaction', 'WithdrawalController@withdrawalConfirmation');
        Route::get('withdrawal/method/{id}', 'WithdrawalController@selectWithdrawalMethod');
        Route::post('withdrawal/store', 'WithdrawalController@withdrawalStore');
        Route::post('withdrawal/amount-limit', 'WithdrawalController@withdrawalAmountLimitCheck');
        Route::post('withdrawal/fees-limit-payment-method-isActive-currencies', 'WithdrawalController@getWithdrawalFeesLimitsActiveCurrencies');
    });

    //Transfer - Without Suspend Middleware
    Route::group(['middleware' => ['permission:manage_transfer']], function ()
    {
        Route::get('moneytransfer/print/{id}', 'MoneyTransferController@transferPrintPdf');
    });

    //Transfer - With Suspend Middleware
    Route::group(['middleware' => ['permission:manage_transfer', 'check-user-suspended']], function ()
    {
        Route::match('GET', 'moneytransfer', 'MoneyTransferController@create');
        Route::post('transfer', 'MoneyTransferController@create');
        Route::post('transfer-user-email-phone-receiver-status-validate', 'MoneyTransferController@transferUserEmailPhoneReceiverStatusValidate');
        Route::post('amount-limit', 'MoneyTransferController@amountLimitCheck');
        Route::get('send-money-confirm', 'MoneyTransferController@sendMoneyConfirm');
    });

    // transactions
    Route::group(['middleware' => ['permission:manage_transaction']], function ()
    {
        Route::match(array('GET', 'POST'), 'transactions', 'UserTransactionController@index');
        Route::get('transactions/{id}', 'UserTransactionController@showDetails');
        Route::post('get_transaction', 'UserTransactionController@getTransaction');
        Route::get('transactions/print/{id}', 'UserTransactionController@getTransactionPrintPdf');
        Route::get('transactions/exchangeTransactionPrintPdf/{id}', 'UserTransactionController@exchangeTransactionPrintPdf');
        Route::get('transactions/merchant-payment-print/{id}', 'UserTransactionController@merchantPaymentTransactionPrintPdf');
    });

    // Currency Exchange - Without Suspend Middleware
    Route::group(['middleware' => ['permission:manage_exchange']], function ()
    {
        Route::get('exchange-money/print/{id}', 'ExchangeController@exchangeOfPrintPdf');
    });

    // Currency Exchange - With Suspend Middleware
    Route::group(['middleware' => ['permission:manage_exchange', 'check-user-suspended']], function ()
    {
        Route::get('exchange', 'ExchangeController@exchange');
        Route::post('exchange-of-money', 'ExchangeController@exchangeOfCurrency');
        Route::post('exchange/get-currencies-except-users-existing-wallets', 'ExchangeController@getActiveHasTransactionExceptUsersExistingWalletsCurrencies');
        Route::post('exchange/get-currencies-exchange-rate', 'ExchangeController@getCurrenciesExchangeRate');
        Route::get('exchange-of-money-success', 'ExchangeController@exchangeOfCurrencyConfirm');
        Route::post('exchange/getBalanceOfToWallet', 'ExchangeController@getBalanceOfToWallet');
        Route::post('exchange/amount-limit-check', 'ExchangeController@amountLimitCheck');
    });

    // Request Payments - Without Suspend Middleware
    Route::group(['middleware' => ['permission:manage_request_payment']], function ()
    {
        Route::get('request-payment/print/{id}', 'RequestPaymentController@printPdf');
        Route::post('request_payment/cancel', 'RequestPaymentController@cancel');
        Route::post('request_payment/cancelfrom', 'RequestPaymentController@cancelfrom');
    });

    // Request Payments - With Suspend Middleware
    Route::group(['middleware' => ['permission:manage_request_payment', 'check-user-suspended']], function ()
    {
        Route::get('request_payment/check-creator-status', 'RequestPaymentController@checkReqCreatorStatus');
        Route::get('request_payment/add', 'RequestPaymentController@add');
        Route::post('request', 'RequestPaymentController@store');
        Route::get('request_payment/accept/{id}', 'RequestPaymentController@requestAccept');
        Route::post('request-payment/amount-limit', 'RequestPaymentController@amountLimitCheck');
        Route::post('request_payment/request-user-email-phone-receiver-status-validate', 'RequestPaymentController@requestUserEmailPhoneReceiverStatusValidate');
        Route::post('request_payment/accepted', 'RequestPaymentController@requestAccepted');
        Route::get('request_payment/accept-money-confirm', 'RequestPaymentController@requestAcceptedConfirm');
        Route::get('request-money-confirm', 'RequestPaymentController@requestMoneyConfirm');
    });

    // Merchants
    Route::group(['middleware' => ['permission:manage_merchant']], function ()
    {
        Route::get('merchants', 'MerchantController@index');
        Route::get('merchant/detail/{id}', 'MerchantController@detail');
        Route::get('merchant/payments', 'MerchantController@payments');
        Route::get('merchant/add', 'MerchantController@add');
        Route::post('merchant/store', 'MerchantController@store');
        Route::get('merchant/edit/{id}', 'MerchantController@edit');
        Route::post('merchant/update', 'MerchantController@update');

        // QR Code - starts
        Route::post('merchant/generate-standard-merchant-payment-qrCode', 'MerchantController@generateStandardMerchantPaymentQrCode');
        Route::post('merchant/generate-express-merchant-qr-code', 'MerchantController@generateExpressMerchantQrCode');
        Route::post('merchant/update-express-merchant-qr-code', 'MerchantController@updateExpressMerchantQrCode');
        Route::get('merchant/qr-code-print/{id}/{printQrCode}', 'MerchantController@printMerchantQrCode');
        // QR Code - ends
    });

    // Disputes
    Route::group(['middleware' => ['permission:manage_dispute']], function ()
    {
        Route::get('disputes', 'DisputeController@index');
        Route::get('dispute/add/{id}', 'DisputeController@add');
        Route::post('dispute/open', 'DisputeController@store');
        Route::get('dispute/discussion/{id}', 'DisputeController@discussion');
        Route::post('dispute/reply', 'DisputeController@storeReply');
        Route::post('dispute/change_reply_status', 'DisputeController@changeReplyStatus');
        Route::get('dispute/download/{file}', 'DisputeController@download');
    });

    // Tickets
    Route::group(['middleware' => ['permission:manage_ticket']], function ()
    {
        Route::get('tickets', 'TicketController@index');
        Route::get('ticket/add', 'TicketController@create');
        Route::post('ticket/store', 'TicketController@store');
        Route::get('ticket/reply/{id}', 'TicketController@reply');
        Route::post('ticket/reply_store', 'TicketController@reply_store');
        Route::post('ticket/change_reply_status', 'TicketController@changeReplyStatus');
        Route::get('ticket/download/{file}', 'TicketController@download');
    });

});

/* Merchant Payment Start*/
Route::match(array('GET', 'POST'), 'payment/form', 'MerchantPaymentController@index');
Route::get('payment/success', 'MerchantPaymentController@success');
Route::get('payment/fail', 'MerchantPaymentController@fail');

//paymoney
Route::post('payment/mts_pay', 'MerchantPaymentController@mtsPayment');

//stripe
Route::post('payment/stripe', 'MerchantPaymentController@stripePayment');
Route::post('standard-merchant/stripe-make-payment', 'MerchantPaymentController@stripeMakePayment');

//paypal
// Route::post('payment/paypal', 'MerchantPaymentController@paypalPayment');
Route::POST('payment/paypal_payment_success', 'MerchantPaymentController@paypalPaymentSuccess');

//payumoney
Route::post('payment/payumoney', 'MerchantPaymentController@payumoney');
Route::post('payment/payumoney_success', 'MerchantPaymentController@payuPaymentSuccess');
Route::post('payment/payumoney_fail', 'MerchantPaymentController@merchantPayumoneyPaymentFail');

//CoinPayments
//CoinPayments
Route::post('payment/coinpayments', 'MerchantPaymentController@coinPayments');
Route::post('payment/coinpayments/make-transaction', 'MerchantPaymentController@coinPaymentMakeTransaction');
Route::get('payment/coinpayments/coinpayment-transaction-info', 'MerchantPaymentController@viewCoinpaymentTransactionInfo');
// Route::get('payment/coinpayments_check', 'MerchantPaymentController@coinPaymentsCheck');
/* Merchant Payment End*/

/* PayMoney Merchant API Start*/
/* All url under this section must be started with the url 'merchant/api' */
Route::post('merchant/api/verify', 'MerchantApiPayment@verifyClient');
Route::match(array('GET', 'POST'), 'merchant/payment', 'MerchantApiPayment@generatedUrl');
Route::post('merchant/api/transaction-info', 'MerchantApiPayment@storeTransactionInfo');
Route::get('merchant/payment/cancel', 'MerchantApiPayment@cancelPayment');
/* PayMoney Merchant API End*/

Route::group(['middleware' => ['guest:users']], function ()
{
    Route::get('merchant/payment/confirm', 'MerchantApiPayment@confirmPayment');
});

Route::get('download/package', 'ContentController@downloadPackage');
Route::get('{url}', 'ContentController@pageDetail');