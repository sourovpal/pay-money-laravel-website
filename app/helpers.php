<?php

function setDateForDb($value)
{
    $separator   = \Illuminate\Support\Facades\Session::get('date_sepa');
    $date_format = \Illuminate\Support\Facades\Session::get('date_format_type');

    if (str_replace($separator, '', $date_format) == "mmddyyyy")
    {
        $value = str_replace($separator, '/', $value);
        $date  = date('Y-m-d', strtotime($value));
    }
    else
    {
        $date = date('Y-m-d', strtotime(strtr($value, $separator, '-')));
    }
    return $date;
}

function array2string($data)
{
    $log_a = "";
    foreach ($data as $key => $value)
    {
        if (is_array($value))
        {
            $log_a .= "\r\n'" . $key . "' => [\r\n" . array2string($value) . "\r\n],";
        }
        else
        {
            $log_a .= "'" . $key . "'" . " => " . "'" . str_replace("'", "\\'", $value) . "',\r\n";
        }

    }
    return $log_a;
}

function d($var, $a = false)
{
    echo "<pre>";
    print_r($var);
    echo "</pre>";
    if ($a)
    {
        exit;
    }
}

/**
 * [unique code
 * @return [void] [unique code for each transaction]
 */
function unique_code()
{
    $length = 13;
    if (function_exists("random_bytes"))
    {
        $bytes = random_bytes(ceil($length / 2));
    }
    elseif (function_exists("openssl_random_pseudo_bytes"))
    {
        $bytes = openssl_random_pseudo_bytes(ceil($length / 2));
    }
    else
    {
        throw new Exception("no cryptographically secure random function available");
    }
    return strtoupper(substr(bin2hex($bytes), 0, $length));
}

/**
 * [current_balance description]
 * @return [void] [displaying default wallet balance on page header]
 */
function current_balance() //TODO: remove it
{
    $wallet            = App\Models\Wallet::with('currency:id,code')->where(['user_id' => \Auth::user()->id, 'is_default' => 'Yes'])->first();
    $balance_with_code = moneyFormat($wallet->currency->code, '+' . formatNumber($wallet->balance));
    return $balance_with_code;
}

/**
 * [userWallets description]
 * @return [void] [dropdown of wallets on page header]
 */
function userWallets()
{
    $wallet = App\Models\Wallet::where(['user_id' => \Auth::user()->id])->get();
    return $wallet;
}

function AssColumn($a = array(), $column = 'id')
{
    $two_level = func_num_args() > 2 ? true : false;
    if ($two_level)
    {
        $scolumn = func_get_arg(2);
    }

    $ret = array();
    settype($a, 'array');
    if (false == $two_level)
    {
        foreach ($a as $one)
        {
            if (is_array($one))
            {
                $ret[@$one[$column]] = $one;
            }
            else
            {
                $ret[@$one->$column] = $one;
            }

        }
    }
    else
    {
        foreach ($a as $one)
        {
            if (is_array($one))
            {
                if (false == isset($ret[@$one[$column]]))
                {
                    $ret[@$one[$column]] = array();
                }
                $ret[@$one[$column]][@$one[$scolumn]] = $one;
            }
            else
            {
                if (false == isset($ret[@$one->$column]))
                {
                    $ret[@$one->$column] = array();
                }

                $ret[@$one->$column][@$one->$scolumn] = $one;
            }
        }
    }
    return $ret;
}

/**
 * [dateFormat description]
 * @param  [type] $value    [any number]
 * @return [type] [formates date according to preferences setting in Admin Panel]
 */
function dateFormat($value, $userId = null) //$userId - needed for using user_id for mobile app (as mobile app does not know auth()->user()->id)
{
    $timezone = '';
    $prefix   = str_replace('/', '', request()->route()->getPrefix());
    if ($prefix == \Illuminate\Support\Facades\Config::get('adminPrefix'))
    {
        $timezone = preference('dflt_timezone');
    }
    else
    {
        if (!empty($userId))
        {
            $user = App\Models\User::with('user_detail:user_id,timezone')->where(['id' => $userId])->first(['id']);
        }
        else
        {
            if (!empty(auth()->user()->id))
            {
                $user = App\Models\User::with('user_detail:user_id,timezone')->where(['id' => auth()->user()->id])->first(['id']);
            }
        }
        if (!empty(auth()->user()->id) || !empty($userId))
        {
            $timezone = $user->user_detail->timezone;
        }
        else
        {
            $timezone = 'UTC';
        }
    }
    $today = new DateTime($value, new DateTimeZone(config('app.timezone')));
    $today->setTimezone(new DateTimeZone($timezone));
    $value = $today->format('Y-m-d H:i:s');

    $preferenceData = \App\Models\Preference::where(['category' => 'preference'])->whereIn('field', ['date_format_type', 'date_sepa'])->get(['field', 'value'])->toArray();
    $preferenceData = App\Http\Helpers\Common::key_value('field', 'value', $preferenceData);
    $preference     = $preferenceData['date_format_type'];
    $separator      = $preferenceData['date_sepa'];

    $data   = str_replace(['/', '.', ' ', '-'], $separator, $preference);
    $data   = explode($separator, $data);
    $first  = $data[0];
    $second = $data[1];
    $third  = $data[2];

    $dateInfo = str_replace(['/', '.', ' ', '-'], $separator, $value);
    $datas    = explode($separator, $dateInfo);
    $year     = $datas[0];
    $month    = $datas[1];
    $day      = $datas[2];

    $dateObj   = DateTime::createFromFormat('!m', $month);
    $monthName = $dateObj->format('F');

    $toHoursMin = \Carbon\Carbon::createFromTimeStamp(strtotime($value))->format(' g:i A');
    if ($first == 'yyyy' && $second == 'mm' && $third == 'dd')
    {
        $value = $year . $separator . $month . $separator . $day . $toHoursMin;
    }
    elseif ($first == 'dd' && $second == 'mm' && $third == 'yyyy')
    {

        $value = $day . $separator . $month . $separator . $year . $toHoursMin;
    }
    elseif ($first == 'mm' && $second == 'dd' && $third == 'yyyy')
    {

        $value = $month . $separator . $day . $separator . $year . $toHoursMin;
    }
    elseif ($first == 'dd' && $second == 'M' && $third == 'yyyy')
    {
        $value = $day . $separator . $monthName . $separator . $year . $toHoursMin;
    }
    elseif ($first == 'yyyy' && $second == 'M' && $third == 'dd')
    {
        $value = $year . $separator . $monthName . $separator . $day . $toHoursMin;
    }
    return $value;

}

/**
 * [roundFormat description]
 * @param  [type] $value   [any number]
 * @return [type] [formats to 2 decimal places]
 */
function decimalFormat($value)
{
    $preference = preference('decimal_format_amount', 2);
    return number_format((float) ($value), $preference, '.', '');
}

if (!function_exists('cryptoFormat')) {
    
    function cryptoFormat($value)
    {
        $preference = preference('decimal_format_amount_crypto', 8);
        return number_format((float) ($value), $preference, '.', '');
    }
}

/**
 * [roundFormat description]
 * @param  [type] $value     [any number]
 * @return [type] [placement of money symbol according to preferences setting in Admin Panel]
 */
function moneyFormat($symbol, $value)
{
    if (!empty($symbol)) {
        if (preference('money_format') == "before") {
            return $symbol . ' ' . $value;
        }
        return $value . ' ' . $symbol;
    }
    return $value;
}

function moneyFormatForDashboardProgressBars($symbol, $value)
{
    return moneyFormat($symbol, $value);
}

/**
 * [roundFormat description]
 * @param  [type] $value     [any number]
 * @return [type] [placement of money symbol according to preferences setting in Admin Panel]
 */
function thousandsCurrencyFormat($num)
{
    if ($num < 1000) {
        return $num;
    }
    $x           = round($num);
    $format      = number_format($x);
    $array       = explode(',', $format);
    $parts       = array('k', 'm', 'b', 't');
    $countParts = count($array) - 1;
    $display     = $x;
    $display     = $array[0] . ((int) $array[1][0] !== 0 ? '.' . $array[1][0] : '');
    $display .= $parts[$countParts - 1];
    return $display;
}

//function to set pages position on frontend
function getMenuContent($position)
{
    return \App\Models\Pages::where('position', 'like', "%$position%")->whereStatus('active')->get(['url', 'name']);
}

function getSocialLink()
{
    $data = collect(DB::table('socials')->get(['url', 'icon'])->filter(function ($social) {
        return !empty($social->url);
    }))->toArray();
    return $data;
}

function meta($url, $field)
{
    $meta = \App\Models\Meta::where('url', $url)->first([$field]);
    if ($meta) {
        return $meta->$field;
    } elseif ($field == 'title' || $field == 'description' || $field == 'keyword') {
        return __("Page Not Found");
    }
    return "";
}

function available_balance()
{
    $wallet = App\Models\Wallet::where(['user_id' => \Auth::user()->id, 'is_default' => 'Yes'])->first(['balance']);
    return $wallet->balance;
}

function getTime($date)
{
    return date("H:i A", strtotime($date));
}

function changeEnvironmentVariable($key, $value)
{
    $path = base_path('.env');

    if (is_bool(env($key))) {
        $old = env($key) ? 'true' : 'false';
    } elseif (env($key) === null) {
        $old = 'null';
    } else {
        $old = env($key);
    }

    if (file_exists($path)) {
        if ($old == 'null') {
            file_put_contents($path, "\n$key=" . $value, FILE_APPEND);
        } else {
            file_put_contents($path, str_replace(
                "$key=" . $old, "$key=" . $value, file_get_contents($path)
            ));
        }
    }
}

function thirtyDaysNameList()
{
    $data = array();
    for ($j = 30; $j > -1; $j--) {
        $data[30 - $j] = date("d M", strtotime("-$j day"));
    }
    return $data;
}

function getLastOneMonthDates()
{
    $data = array();
    for ($j = 30; $j > -1; $j--) {
        $data[30 - $j] = date("d-m", strtotime(" -$j day"));
    }
    return $data;
}

function encryptIt($value)
{
    $encoded = base64_encode(\Illuminate\Support\Facades\Hash::make($value));
    return ($encoded);
}

function formatNumber($num = 0, $currencyId = NULL)
{
    $currencyType = 'fiat';
    if (isset($currencyId)) {
        $currencyType = \App\Models\Currency::where('id', $currencyId)->value('type');
    }

    $seperator = preference('thousand_separator', '.');
    $format =  ($currencyType == 'fiat') ? preference('decimal_format_amount', 2) : preference('decimal_format_amount_crypto', 8);

    if ($seperator == '.') {
        $num = number_format($num, $format, ",", ".");
    } else if ($seperator == ',') {
        $num = number_format($num, $format, ".", ",");
    }
    return $num;
}

function getLanguagesListAtFooterFrontEnd()
{
    $languages = App\Models\Language::where(['status' => 'Active'])->get(['short_name', 'name']);
    return $languages;
}

function getAppStoreLinkFrontEnd()
{
    $app = App\Models\AppStoreCredentials::where(['has_app_credentials' => 'Yes'])->get(['logo', 'link', 'company']);
    return $app;
}

function getDestinationCurrencyRateFromExchangeRateApi($from, $to, $apiKey)
{
    $apiURL = 'https://v6.exchangerate-api.com/v6/'. $apiKey . '/pair/' . $from .'/' . $to;

    $response = \Illuminate\Support\Facades\Http::get($apiURL);
    if ($response->status() == 200) {
        return json_decode($response->getBody())->conversion_rate;
    }
}

function getDestinationCurrencyRateFromCurrencyConverterApi($from, $to, $apiKey)
{
    $url = 'https://free.currencyconverterapi.com/api/v6/convert?q=' . $from . '_' . $to . '&compact=ultra&apiKey=' . $apiKey;

    $response = \Illuminate\Support\Facades\Http::get($url);
    if ($response->status() == 200) {
        $variable = $from . "_" . $to;
        return json_decode($response)->$variable;
    }
}

function getCurrencyRate($from, $to)
{
    $enabledExchangeApi = settings('exchange_enabled_api');

    if ($enabledExchangeApi == 'currency_converter_api_key') {
        return getDestinationCurrencyRateFromCurrencyConverterApi($from, $to, settings('currency_converter_api_key'));
    } else if ($enabledExchangeApi == 'exchange_rate_api_key') {
        return getDestinationCurrencyRateFromExchangeRateApi($from, $to, settings('exchange_rate_api_key'));
    }
}

function getCompanyLogo()
{
    $session = session('company_logo');
    if (!$session) {
        session(['company_logo' => settings('logo')]);
    }
    return $session;
}

function setActionSession()
{
    $key = time();
    session(['action-session' => encrypt($key)]);
    session(['session-key' => $key]);
}

function actionSessionCheck()
{
    if (!\Illuminate\Support\Facades\Session::has('action-session'))
    {
        abort(404);
    }
    else
    {
        $key          = session('session-key');
        $encryptedKey = session('action-session');
        if ($key != decrypt($encryptedKey))
        {
            abort(404);
        }
    }
}

function clearActionSession()
{
    session()->forget('action-session');
    session()->forget('session-key');
}

function getCurrencyIdOfTransaction($transactions)
{
    $currencies = [];
    foreach ($transactions as $trans)
    {
        $currencies[] = $trans->currency_id;
    }
    return $currencies;
}

//fixed - for exchange rate - if set to 0 - which is unusual
function generateAmountBasedOnDfltCurrency($data, $currencyWithRate)
{
    $data_map = [];
    foreach ($data as $key => $value)
    {
        foreach ($currencyWithRate as $currencyRate)
        {
            if ($currencyRate->id == $value->currency_id)
            {
                if (!isset($data_map[$value->day][$value->month]))
                {
                    $data_map[$value->day][$value->month] = 0;
                }
                if ($value->currency_id != session('default_currency'))
                {
                    if ($currencyRate->rate != 0)
                    {
                        $data_map[$value->day][$value->month] += abs($value->amount / $currencyRate->rate);
                    }
                    else
                    {
                        $data_map[$value->day][$value->month] = 0;
                    }
                }
                else
                {
                    $data_map[$value->day][$value->month] += abs($value->amount);
                }
            }
        }
    }
    return $data_map;
}

//fixed - for exchange rate - if set to 0 - which is unusual
function generateAmountForTotal($data, $currencyWithRate)
{
    $final = 0;
    foreach ($data as $key => $value)
    {
        foreach ($currencyWithRate as $currencyRate)
        {
            if ($currencyRate->id == $value->currency_id)
            {
                if ($value->currency_id != session('default_currency'))
                {
                    if ($currencyRate->rate != 0)
                    {
                        $final += abs($value->total_charge / $currencyRate->rate);
                    }
                    else
                    {
                        // $data_map[$value->day][$value->month] = 0;
                        $final += 0;
                    }
                }
                else
                {
                    $final += abs($value->total_charge);
                }
            }
        }
    }
    return $final;
}

function checkAppMailEnvironment()
{
    $checkMail = env('APP_MAIL', 'true');
    return $checkMail;
}

function checkAppSmsEnvironment()
{
    $checkSms = env('APP_SMS', 'true');
    return $checkSms;
}

function phpDefaultTimeZones()
{
    $zones_array = array();
    $timestamp   = time();
    foreach (timezone_identifiers_list() as $key => $zone)
    {
        date_default_timezone_set($zone);
        $zones_array[$key]['zone']          = $zone;
        $zones_array[$key]['diff_from_GMT'] = 'UTC/GMT ' . date('P', $timestamp);
    }
    return $zones_array;
}

function getSmsConfigDetails()
{
    return \App\Models\SmsConfig::where(['status' => 'Active'])->first();
}

function sendSMSwithNexmo($nexmoCredentials, $to, $message)
{
    $trimmedMsg = trim(preg_replace('/\s\s+/', ' ', $message));
    $url        = 'https://rest.nexmo.com/sms/json?' . http_build_query([
        'api_key'    => '' . trim($nexmoCredentials['Key']) . '',
        'api_secret' => '' . trim($nexmoCredentials['Secret']) . '',
        'from'       => '' . $nexmoCredentials['default_nexmo_phone_number'] . '',
        'to'         => '' . $to . '',
        'text'       => '' . strip_tags($trimmedMsg) . '',
    ]);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
}

function sendSMSwithTwilio($twilioCredentials, $to, $message)
{
    $accountSID   = $twilioCredentials['account_sid'];
    $authToken    = $twilioCredentials['auth_token'];
    $twilioNumber = $twilioCredentials['default_twilio_phone_number'];
    $trimmedMsg   = trim(preg_replace('/\s\s+/', ' ', $message));

    $client = new \Twilio\Rest\Client($accountSID, $authToken);
    $client->messages->create(
        $to,
        array(
            'from' => $twilioNumber,
            'body' => strip_tags($trimmedMsg)
        )
    );
}

function sendSMS($to, $message)
{
    $smsConfig = getSmsConfigDetails();
    if (!empty($smsConfig)) {
        $smsCredentials = json_decode($smsConfig->credentials, true);
        if (count($smsCredentials) > 0) {
            if ($smsConfig->type == 'nexmo') {
                sendSMSwithNexmo($smsCredentials, $to, $message);
            }
            elseif ($smsConfig->type == 'twilio') {
                sendSMSwithTwilio($smsCredentials, $to, $message);
            }
        }
    }
}

function six_digit_random_number()
{
    return mt_rand(100000, 999999);
}

function getBrowser($agent)
{
    $browserName = 'Unknown';
    $platform = 'Unknown';
    $version  = "";
    $userBrowser = '';

    if (preg_match('/linux/i', $agent)) {
        $platform = 'linux';
    } elseif (preg_match('/macintosh|mac os x/i', $agent)) {
        $platform = 'mac';
    } elseif (preg_match('/windows|win32/i', $agent)) {
        $platform = 'windows';
    }

    $browsers = [
        'Edg' => 'Microsoft Edge',
        'MSIE' => 'Internet Explorer',
        'Trident' => 'Internet Explorer',
        'Chrome' => 'Google Chrome',
        'Firefox' => 'Mozilla Firefox',
        'Safari' => 'Apple Safari',
        'Opera Mini' => 'Opera Mini',
        'Opera' => 'Opera',
        'Netscape' => 'Netscape'
    ];

    foreach($browsers as $key => $value) {
        if (strpos($agent, $key) !== FALSE) {
            $browserName = $value;
            $userBrowser = $key;
            break;
        }
    }

    // finally get the correct version number
    $known = array('Version', $userBrowser, 'other');
    $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $agent, $matches)) {
        // we have no matching number just continue
    }

    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        // we will have two since we are not using 'other' argument yet
        // see if version is before or after the name
        if (strripos($agent, "Version") < strripos($agent, $userBrowser)) {
            $version = $matches['version'][0];
        } else {
            $version = $matches['version'][1];
        }
    } else {
        $version = $matches['version'][0];
    }

    // check if we have a number
    if ($version == null || $version == "") {
        $version = "?";
    }

    return [
        'name'     => $browserName,
        'version'  => $version,
        'platform' => $platform,
    ];
}

function getBrowserFingerprint($user_id, $browser_fingerprint)
{
    $getBrowserFingerprint = App\Models\DeviceLog::where(['user_id' => $user_id, 'browser_fingerprint' => $browser_fingerprint])->first(['browser_fingerprint']);
    return $getBrowserFingerprint;
}

function checkDemoEnvironment()
{
    $checkSms = env('APP_DEMO', 'true');
    return $checkSms;
}

function coinPaymentInfo()
{
    $transInfo = \Illuminate\Support\Facades\Session::get('transInfo');
    $cpm       = \App\Models\CurrencyPaymentMethod::where(['method_id' => $transInfo['payment_method'], 'currency_id' => $transInfo['currency_id']])->first(['method_data']);
    return json_decode($cpm->method_data);
}

function captchaCheck($enabledCaptcha, $key)
{
    if (isset($enabledCaptcha) && ($enabledCaptcha == 'login' || $enabledCaptcha == 'registration' || $enabledCaptcha == 'login_and_registration')) {
        \Illuminate\Support\Facades\Config::set([(($key == 'site_key') ? 'captcha.sitekey' : 'captcha.secret') => settings($key)]);
    }
}

function getLanguageDefault()
{
    $getDefaultLanguage = \App\Models\Language::where(['default' => '1'])->first(['id', 'short_name']);
    return $getDefaultLanguage;
}

function getAuthUserIdentity()
{
    $getAuthUserIdentity = \App\Models\DocumentVerification::where(['user_id' => auth()->user()->id, 'verification_type' => 'identity'])->first(['verification_type', 'status']);
    return $getAuthUserIdentity;
}

function getAuthUserAddress()
{
    $getAuthUserAddress = \App\Models\DocumentVerification::where(['user_id' => auth()->user()->id, 'verification_type' => 'address'])->first(['verification_type', 'status']);
    return $getAuthUserAddress;
}

function allowedDecimalPlaceMessage($decimalPosition)
{
    $message = "*Allowed upto " . $decimalPosition . " decimal places.";
    return $message;
}

function allowedImageDimension($width, $height, $panel = null)
{
    if ($panel == 'user')
    {
        $message = "*" . __('Recommended Dimension') . ": " . $width . " px * " . $height . " px";
    }
    else
    {
        $message = "*Recommended Dimension: " . $width . " px * " . $height . " px";
    }
    return $message;
}

/**
 * [CUSTOM AES-256 ENCRYPTION/DECRYPTION METHOD]
 * param  $action [encrypt/decrypt]
 * param  $string [string]
 */
function initAES256($action, $plaintext)
{
    $output   = '';
    $cipher   = "AES-256-CBC";
    $password = 'K8m26hzj22TtZxnzX96vmRAVTzPxNXRB';
    $key      = substr(hash('sha256', $password, true), 0, 32); // Must be exact 32 chars (256 bit)
                                                                // $ivlen    = openssl_cipher_iv_length($cipher);
                                                                // $iv       = openssl_random_pseudo_bytes($ivlen); // IV must be exact 16 chars (128 bit)
    $secretIv = 'UP4n2cr8Bwn83X4h';
    $iv       = substr(hash('sha256', $secretIv), 0, 16);
    if ($plaintext != '')
    {
        if ($action == 'encrypt')
        {
            $output = base64_encode(openssl_encrypt($plaintext, $cipher, $key, OPENSSL_RAW_DATA, $iv));
        }
        if ($action == 'decrypt')
        {
            $output = openssl_decrypt(base64_decode($plaintext), $cipher, $key, OPENSSL_RAW_DATA, $iv);
        }
    }
    return $output;
}

function getDefaultCountry()
{
    return \App\Models\Country::where(['is_default' => 'yes'])->first()->short_name;
}

function getFormatedCurrencyList($rates, $rateAmount)
{
    foreach ($rates as $coin => $coinDetails) {
        if ((INT) $coinDetails['is_fiat'] === 0) {
            if ($rates[$coin]['rate_btc'] != 0) {
                $rate = ($rateAmount / $rates[$coin]['rate_btc']);
            }
            else {
                $rate = $rateAmount;
            }
            $coins[] = [
                'name'     => $coinDetails['name'],
                'rate'     => number_format($rate, 8, '.', ''),
                'iso'      => $coin,
                'icon'     => 'https://www.coinpayments.net/images/coins/' . $coin . '.png',
                'selected' => $coin == 'BTC' ? true : false,
                'accepted' => $coinDetails['accepted'],
            ];
            $aliases[$coin] = $coinDetails['name'];
        }

        if ((INT) $coinDetails['is_fiat'] === 0 && $coinDetails['accepted'] == 1) {
            $renamedCoin = explode('.', $coin);

            $rate           = ($rateAmount / $rates[$coin]['rate_btc']);
            $coins_accept[] = [
                'name'     => $coinDetails['name'],
                'rate'     => number_format($rate, 8, '.', ''),
                'iso'      => $coin,
                'icon'     => 'https://www.coinpayments.net/images/coins/' . ((count($renamedCoin) > 1) ? $renamedCoin[0] : $coin)  . '.png',
                'selected' => $coin == 'BTC' ? true : false,
                'accepted' => $coinDetails['accepted'],
            ];
        }

        if ((INT) $coinDetails['is_fiat'] === 1) {
            $fiat[$coin] = $coinDetails;
        }
    }

    return ['coins' => $coins, 'coins_accept' => $coins_accept, 'fiat' => $fiat, 'aliases' => $aliases];
}
/**
 * [CUSTOM AES-256 ENCRYPTION/DECRYPTION METHOD]
 * param  $action [encrypt/decrypt]
 * param  $string [string]
 */
function convert_string($action, $string) {
    $output         = '';
    $encrypt_method = "AES-256-CBC";
    $secret_key     = 'XXD93D945143F656DD9094450F802743F5457551991C8CXX';
    $secret_iv      = 'XXE8327B11DA84769CB73FE4495C63XX';
    // hash
    $key                   = hash('sha256', $secret_key);
    $initialization_vector = substr(hash('sha256', $secret_iv), 0, 16);
    if ($string != '') {
        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $initialization_vector);
            $output = base64_encode($output);
        } if ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $initialization_vector);
        }
    }
    return $output;
}

if (!function_exists('getStatuses')) {

    /**
     * Return status, class, colors
     *
     * @return string
     */
    function getStatuses()
    {
        return [
            'success' => ['text' => __('Success'), 'label' => 'success', 'color' => 'green'],
            'pending' => ['text' => __('Pending'), 'label' => 'primary', 'color' => 'blue'],
            'refund' => ['text' => __('Refunded'), 'label' => 'warning', 'color' => 'orange'],
            'blocked' => ['text' => __('Cancelled'), 'label' => 'danger', 'color' => 'red'],
            'active' => ['text' => __('Active'), 'label' => 'success', 'color' => 'green'],
            'inactive' => ['text' => __('Inactive'), 'label' => 'danger', 'color' => 'red'],
            'suspended' => ['text' => __('Suspended'), 'label' => 'warning', 'color' => 'orange'],
            'open' => ['text' => __('Open'), 'label' => 'success', 'color' => 'green'],
            'in progress' => ['text' => __('In Progress'), 'label' => 'primary', 'color' => 'blue'],
            'hold' => ['text' => __('Hold'), 'label' => 'warning', 'color' => 'orange'],
            'closed' => ['text' => __('Closed'), 'label' => 'danger', 'color' => 'red'],
            'approved' => ['text' => __('Approved'), 'label' => 'success', 'color' => 'green'],
            'rejected' => ['text' => __('Rejected'), 'label' => 'danger', 'color' => 'red'],
            'solve' => ['text' => __('Solved'), 'label' => 'success', 'color' => 'green'],
            'moderation' => ['text' => __('Moderation'), 'label' => 'primary', 'color' => 'blue'],
            'disapproved' => ['text' => __('Disapproved'), 'label' => 'danger', 'color' => 'red'],
            'draft' => ['text' => __('Draft'), 'label' => 'default', 'color' => 'default'],
            'cancelled' => ['text' => __('Cancelled'), 'label' => 'danger', 'color' => 'red'],
            'completed' => ['text' => __('Completed'), 'label' => 'info', 'color' => 'blue'],
        ];
    }
}

if (!function_exists('getStatus')) {

    /**
     * Get transactions status by HTML Lable
     *
     * @param string $status
     *
     * @return HTMLString
     */
    function getStatus($status = null)
    {
        if (empty($status)) {
            return '';
        }
        $statuses = getStatuses();

        $status = strtolower($status);
        return $statuses[$status]['text'];
    }
}

if (!function_exists('getStatusLabel')) {

    /**
     * Get transactions status by HTML Lable
     *
     * @param string $status
     *
     * @return HTMLString
     */
    function getStatusLabel($status = null)
    {
        if (empty($status)) {
            return '';
        }
        $statuses = getStatuses();

        $status = strtolower($status);
        return '<span class="label label-' . $statuses[$status]['label'] . '">' . $statuses[$status]['text'] . '</span>';
    }
}

if (!function_exists('getStatusBadge')) {

    /**
     * Get transactions status by HTML Lable
     *
     * @param string $status
     *
     * @return HTMLString
     */
    function getStatusBadge($status = null)
    {
        if (empty($status)) {
            return '';
        }
        $statuses = getStatuses();

        $status = strtolower($status);
        return '<span class="badge badge-' . $statuses[$status]['label'] . '">' . $statuses[$status]['text'] . '</span>';
    }
}

if (!function_exists('getStatusText')) {

    /**
     * Get transactions status by HTML text
     *
     * @param string $status
     *
     * @return HTMLString
     */
    function getStatusText($status = null)
    {
        if (empty($status)) {
            return '';
        }
        $statuses = getStatuses();

        $status = strtolower($status);
        return '<span class="text text-' . $statuses[$status]['color'] . '">' . $statuses[$status]['text'] . '</span>';
    }
}

if (!function_exists('getStatusInputLabel')) {

    /**
     * Transaction edit sender-receiver Title Text
     *
     * @param string $type [TransactionTypeId like Deposit, Withdrawal]
     * @param string $userType [user - receiver]
     *
     * @return HtmlString
     */
    function getStatusInputLabel($type = null, $userType = null)
    {
        if (empty($type) || empty($userType)) {
            return '';
        }

        $transactionTypes = [
            Deposit => ['user' => 'User', 'receiver' => 'Receiver'],
            Exchange_From => ['user' => 'User', 'receiver' => 'Receiver'],
            Exchange_To => ['user' => 'User', 'receiver' => 'Receiver'],
            Withdrawal => ['user' => 'User', 'receiver' => 'Receiver'],
            Payment_Sent => ['user' => 'User', 'receiver' => 'Receiver'],
            Payment_Received => ['user' => 'User', 'receiver' => 'Receiver'],
            Transferred => ['user' => 'Paid By', 'receiver' => 'Paid To'],
            Received => ['user' => 'Paid By', 'receiver' => 'Paid To'],
            Request_From => ['user' => 'Request From', 'receiver' => 'Request To'],
            Request_To => ['user' => 'Request From', 'receiver' => 'Request To']
        ];

        if (config('referral.is_active')) {
            $transactionTypes[Referral_Award] = ['user' => 'User', 'receiver' => 'Receiver'];
        }

        if (module('CryptoExchange')) {
            $transactionTypes[Crypto_Buy] = ['user' => 'User', 'receiver' => 'Receiver'];
            $transactionTypes[Crypto_Sell] = ['user' => 'User', 'receiver' => 'Receiver'];
            $transactionTypes[Crypto_Swap] = ['user' => 'User', 'receiver' => 'Receiver'];
        }

        if (module('Investment')) {
            $transactionTypes[Investment] = ['user' => 'User', 'receiver' => 'Receiver'];
        }

        if (module('BlockIo')) {
            $transactionTypes[Crypto_Sent] = ['user' => 'Sender', 'receiver' => 'Receiver'];
            $transactionTypes[Crypto_Received] = ['user' => 'Sender', 'receiver' => 'Receiver'];
        }

        return '<label class="control-label col-sm-3 fw-bold text-end" for="'. $userType .'">' . $transactionTypes[$type][$userType] . '</label>';
    }
}

if (! function_exists('getPaymoneySettings')) {

    /**
     * Get Paymoney configurations info
     *
     * @param string $type
     *
     * @return array
     */
    function getPaymoneySettings($type = null)
    {
        if (empty($type)) {
            return false;
        }
        $array = [
            'transaction_types' => [
                'web' => [
                    'sent' => [Deposit, Transferred, Exchange_From, Exchange_To, Request_From, Withdrawal, Payment_Sent],
                    'received' => [Received, Request_To, Payment_Received]
                ],
                'mobile' => [
                    'sent' => ['Deposit' => Deposit, 'Transferred' => Transferred, 'Exchange_From' => Exchange_From, 'Exchange_To' => Exchange_To, 'Request_From' => Request_From, 'Withdrawal' => Withdrawal, 'Payment_Sent' => Payment_Sent],
                    'received' => ['Received' => Received, 'Request_To' => Request_To, 'Payment_Received' => Payment_Received]
                ],

            ],
            'payment_methods' => [
                'web' => [
                    'all' => [Mts, Stripe, Paypal, PayUmoney, Bank, Coinpayments, Payeer, Crypto],
                    'deposit' => [Mts, Stripe, Paypal, PayUmoney, Bank, Coinpayments, Payeer],
                    'withdrawal' => [Paypal, Bank, Crypto],
                    'fiat' => [
                        'deposit' => [Mts, Stripe, Paypal, PayUmoney, Bank, Coinpayments, Payeer],
                        'withdrawal' => [Mts, Paypal, Bank],
                    ],
                    'crypto' => [
                        'deposit' => [Mts, Coinpayments],
                        'withdrawal' => [Mts, Crypto],
                    ]
                ],
                'mobile' => [
                    'all' => ['Stripe' => Stripe, 'Paypal' => Paypal, 'Bank' => Bank, 'Coinpayments' => Coinpayments, 'Crypto' => Crypto],
                    'deposit' => ['Stripe' => Stripe,'Paypal' => Paypal, 'Bank' => Bank, 'Coinpayments' => Coinpayments],
                    'withdrawal' => ['Paypal' => Paypal, 'Bank' => Bank, 'Crypto' => Crypto],
                    'fiat' => [
                        'deposit' => ['Stripe' => Stripe, 'Paypal' => Paypal, 'Bank' => Bank],
                        'withdrawal' => ['Paypal' => Paypal, 'Bank' => Bank],
                    ],
                    'crypto' => [
                        'deposit' => ['Coinpayments' => Coinpayments],
                        'withdrawal' => ['Crypto' => Crypto],
                    ]
                ]
            ]
        ];

        // Mobile MOney - Payment method
        if (config('mobilemoney.is_active')) {
            if (defined('MobileMoney')) {
                $array['payment_methods']['web']['all'][] = MobileMoney;
                $array['payment_methods']['web']['deposit'][] = MobileMoney;
                $array['payment_methods']['web']['withdrawal'][] = MobileMoney;
                $array['payment_methods']['web']['fiat']['deposit'][] = MobileMoney;
                $array['payment_methods']['web']['fiat']['withdrawal'][] = MobileMoney;
            }
        }

        // Referral Award- Transaction Type
        if (config('referral.is_active')) {
            $array['transaction_types']['web']['sent'][] = Referral_Award;
        }

        if (module('CryptoExchange')) {
            $array['transaction_types']['web']['sent'][] = Crypto_Sell;
            $array['transaction_types']['web']['sent'][] = Crypto_Buy;
            $array['transaction_types']['web']['sent'][] = Crypto_Swap;
            $array['transaction_types']['mobile']['sent']['Crypto_Sell'] = Crypto_Sell;
            $array['transaction_types']['mobile']['sent']['Crypto_Buy'] = Crypto_Buy;
            $array['transaction_types']['mobile']['sent']['Crypto_Swap'] = Crypto_Swap;
        }
        if (module('Investment')) {
            $array['transaction_types']['web']['sent'][] = Investment;
        }

        if (module('BlockIo')) {
            $array['transaction_types']['web']['sent'][] = Crypto_Sent;
            $array['transaction_types']['web']['received'][] = Crypto_Received;
            $array['transaction_types']['mobile']['sent']['Crypto_Sent'] = Crypto_Sent;
            $array['transaction_types']['mobile']['received']['Crypto_Received'] = Crypto_Received;
        }

        // Transaction Types
        $array['transaction_types']['web']['all'] = array_merge($array['transaction_types']['web']['sent'], $array['transaction_types']['web']['received']);
        $array['transaction_types']['mobile']['all'] = array_merge($array['transaction_types']['mobile']['sent'], $array['transaction_types']['mobile']['received']);

        return $array[$type];
    }
}


if (!function_exists('preference')) {

    /**
     * Get preference values
     *
     * @param string $field [return specific value]
     * @param string $default [take default value as optional if not provide]
     *
     * @return void
     */
    function preference($field = null, $default = null)
    {
        $preference = new App\Models\Preference();

        if (is_null($field)) {
            return $preference->getAll()->pluck('value', 'field')->toArray();
        }

        $value = $default;
        $preferences = $preference->getAll()->pluck('value', 'field')->toArray();

        if (array_key_exists($field, $preferences)) {
            $value = $preferences[$field];
        }

        return $value;
    }
}

if (!function_exists('settings')) {

    /**
     * Get settings values
     *
     * @param string $field [return specific value, if don't match provide type values]
     *
     * @return string
     * @return array
     */
    function settings($field = null)
    {
        $setting = new App\Models\Setting();

        if (is_null($field)) {
            return $setting->getAll()->pluck('value', 'name')->toArray();
        }

        $settings = $setting->getAll()->pluck('value', 'name')->toArray();

        if (array_key_exists($field, $settings)) {
            $result = $settings[$field];
        } else {
            $result = $setting->getAll()->where('type', $field)->pluck('value', 'name')->toArray();
        }

        return $result;
    }
}

if (!function_exists('isDefault')) {

    /**
     * Get is_default status by HTML Label
     *
     * @param string $status
     *
     * @return HTMLString
     */
    function isDefault($status = null)
    {
        if (empty($status)) {
            return '';
        }
        $statuses = [
            'yes' => ['text' => __('Yes'), 'label' => 'success', 'color' => 'green'],
            'no' => ['text' => __('No'), 'label' => 'danger', 'color' => 'red']
        ];

        $status = strtolower($status);
        return '<span class="label label-' . $statuses[$status]['label'] . '">' . $statuses[$status]['text'] . '</span>';
    }
}

function dataTableOptions(array $options = [])
{
    $default = [
        'order' => [[0, 'desc']],
        'pageLength' => preference('row_per_page'),
        'language' => preference('language'),
    ];

    return array_merge($default, $options);
}

if (!function_exists('templateHeaderText')) {

    /**
     * Get Email or sms template header text
     *
     * @param string $heading
     *
     * @return String
     */
    function templateHeaderText($heading)
    {
        $heading = str_replace('!', '', $heading);

        if (str_contains($heading, 'Notice of ')) {
            $heading = str_replace('Notice of ', '', $heading);
        } else if (str_contains($heading, 'Notice for')) {
            $heading = str_replace('Notice for ', '', $heading);
        } else if (str_contains($heading, 'Notice to')) {
            $heading = str_replace('Notice to ', '', $heading);
        }

        return __('Compose :x Template', ['x' => $heading]);
    }
}

if (!function_exists('uploadImage')) {

    /**
     * upload Image file
     *
     * @param string $file [original source]
     * @param string $location [file path where to upload]
     * @param string $size [optional - for resizing the main file]
     * @param string $old [optional - delete the old file(pass only name with extension)]
     * @param string $thumb [optional - thumb size (70*70) ]
     *
     * @return Array
     */
    function uploadImage($file, $location, $size = null, $old = null, $thumb = null)
    {
        $response = [
            'status' => true,
            'message' => __('File uploaded successfully.')
        ];

        $path = makeDirectory($location);

        if (!$path) {
            $response = [
                'status' => false,
                'message' => __('Directory could not been created.'),
            ];
        }

        if ($thumb){
            $thumbPath = makeDirectory($location . '/thumb/');
            if (!$thumbPath) {
                $response = [
                    'status' => false,
                    'message' => __('Thumb direcotry could not been created.'),
                ];
            }
        }

        if (!empty($old)) {
            removeFile($location . '/' . $old);
            removeFile($location . '/thumb/' . $old);
        }

        try {
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $image    = \Intervention\Image\Facades\Image::make($file);

            if (!empty($size)) {
                $size = explode('*', strtolower($size));
                $image->resize($size[0], $size[1]);
            }
            $image->save($location . '/' . $filename);

            if (!empty($thumb)) {
                $thumb = explode('*', $thumb);
                \Intervention\Image\Facades\Image::make($file)->resize($thumb[0], $thumb[1])->save($location . '/thumb/' . $filename);
            }

            $response['file_name'] = $filename;

        } catch (\Exception $e) {
            $response = [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
        return $response;
    }
}

if (!function_exists('makeDirectory')) {
    /**
     * making directory
     *
     * @param string $path
     * @param int $permission
     *
     * @return bool
     */
    function makeDirectory($path, $permission = null)
    {
        if (file_exists($path)) {
            return true;
        }

        $permission = !empty($permission) ? $permission : config('file_permission', 0755);
        return mkdir($path, $permission, true);
    }

}

if (!function_exists('removeFile')) {
    /**
     * making directory
     *
     * @param string $path
     *
     * @return bool
     */
    function removeFile($path)
    {
        return file_exists($path) && is_file($path) ? @unlink($path) : false;
    }
}

function isActive(String $name = null)
{

    /**
     * Checking if module active or not
     *
     * @param string $name
     *
     * @return bool
     */
    if (is_null($name)) {
        return \Nwidart\Modules\Facades\Module::collections();
    }

    return \Nwidart\Modules\Facades\Module::collections()->has($name);
}

function module(String $name = null)
{
    /**
     * Find a single module or collection
     *
     * @param string $name
     *
     * @return collection
     */
    if (is_null($name)) {
        return \Nwidart\Modules\Facades\Module::all();
    }

    return \Nwidart\Modules\Facades\Module::find($name);
}

if (!function_exists('n_as_k_c')) {
    function n_as_k_c() {
        if(!g_e_v()) {
            return true;
        }
        if(!g_c_v()) {
            try {
                $d_ = g_d();
                $e_ = g_e_v();
                $e_ = explode('.', $e_);
                $c_ = md5($d_ . $e_[1]);
                if($e_[0] == $c_) {
                    p_c_v();
                    return false;
                } else {
                    return true;
                }
            } catch(\Exception $e) {
                return true;
            }
        }
        return false;
    }

}

if (!function_exists('m_ins_ckr')) {

    function m_ins_ckr($mns) {
        if(!m_g_e_v($mns)) {
            return true;
        }
        if(!m_g_c_v($mns)) {
            try {
                $d_ = g_d();
                $e_ = m_g_e_v($mns);
                $e_ = explode('.', $e_);
                $c_ = md5($d_ . $e_[1]);
                if($e_[0] == $c_) {
                    Cache::put(base64_decode($mns), env(base64_decode($mns)), 2629746);
                    return false;
                } else {
                    return true;
                }
            } catch(\Exception $e) {
                return true;
            }
        }
        return false;
    }

}

if (!function_exists('generatePDF')) {

    function generatePDF($view, $fileName, $data)
    {
        $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp']);
        $mpdf = new \Mpdf\Mpdf([
            'mode'        => 'utf-8',
            'format'      => 'A3',
            'orientation' => 'P',
        ]);

        $mpdf->autoScriptToLang         = true;
        $mpdf->autoLangToFont           = true;
        $mpdf->allow_charset_conversion = false;

        $mpdf->WriteHTML(view($view, $data));
        $mpdf->Output($fileName . time() . '.pdf', 'D');
    }
}

if (!function_exists('checkFileValidation')) {
    function checkFileValidation($ext, $type = 0)
    {
        return in_array(strtolower($ext), getFileExtensions($type)) ? true : false;
    }
}

if (!function_exists('getFileExtensions')) {
    function getFileExtensions($type = 0)
    {
        $extensions = array(
            0 => ['jpg', 'jpeg', 'png', 'bmp', 'gif', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'pdf'],
            1 => ['jpg', 'jpeg', 'png', 'gif', 'doc', 'docx', 'pdf'],
            2 => ['jpg', 'jpeg', 'png', 'doc', 'docx', 'pdf'],
            3 => ['jpg', 'jpeg', 'png', 'gif', 'bmp'],
            4 => ['ico'],
            5 => ['doc', 'docx', 'xls', 'xlsx', 'csv', 'pdf'],
            6 => ['jpg', 'jpeg', 'png'],
            7 => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'ico'],
        );
        return $extensions[$type];
    }
}


if (!function_exists('maxFileSize')) {
    /**
     * maxFileSize
     * @param  int $fileSize
     * @return string
     */
    function maxFileSize($fileSize)
    {
        $data = [];
        $maxfileSize = preference('file_size');
        if (isset($maxfileSize) && !empty($maxfileSize)) {
            $maxFileSize = (int) $maxfileSize;
            if (($fileSize / 1024) <= $maxfileSize * 1024) {
                $data['status'] = 1;
            } else if (($fileSize / 1024) > $maxfileSize * 1024) {
                $data['status'] = 0;
                $data['message'] = __('Maximum File Size :x MB.', ['x' => $maxfileSize]);
            }
            return $data;
        }
    }
}

if (!function_exists('xss_clean')) {

    function xss_clean($data)
    {
        // Fix &entity\n;
        $data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        // Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do {
            // Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
        } while ($old_data !== $data);

        return $data;
    }
}

if (!function_exists('stripBeforeSave')) {
    /**
     * stripBeforeSave method
     * This function strips or skips HTML tags
     *
     * @param string $string [The text that will be stripped]
     * @param array $options
     *
     * @return string
     */
    function stripBeforeSave($string = null, $options = ['skipAllTags' => true, 'mergeTags' => false])
    {
        $finalString = [];
        if ($options['skipAllTags'] === false) {
            $allow = '<h1><h2><h3><h4><h5><h6><p><b><br><hr><i><pre><small><strike><strong><sub><sup><time><u><form><input><textarea><button><select><option><label><frame><iframe><img><audio><video><a><link><nav><ul><ol><li><table><th><tr><td><thead><tbody><div><span><header><footer><main><section><article>';
            if (isset($options['mergeTags']) && $options['mergeTags'] === true && isset($options['allowedTags'])) {
                $allow .= is_array($options['allowedTags']) ? implode('', $options['allowedTags']) : trim($options['allowedTags']);
            } else {
                $allow = isset($options['allowedTags']) && is_array($options['allowedTags']) ? implode('', $options['allowedTags']) : trim($options['allowedTags']);
            }
            if (is_array($string)) {
                foreach ($string as $key => $value) {
                    $finalString[$key] = strip_tags($value, $allow);
                }
            } else {
                $finalString = strip_tags($string, $allow);
            }
        } else {
            if (is_array($string)) {
                foreach ($string as $key => $value) {
                    $finalString[$key] = strip_tags($value);
                }
            } else {
                $finalString = strip_tags($string);
            }
        }
        return !empty($finalString) ? $finalString : null;
    }
}

if (!function_exists('getTransactionTypes')) {

    function getTransactionTypes($type = null)
    {
        $transactionTypes = [];

        $fetchTransctionTypes =  \App\Models\TransactionType::get();
        foreach ($fetchTransctionTypes as $value) {
            $transactionTypes[strtolower($value->name)] = $value->name;
        }

        if ($type) {
            return $transactionTypes[$type];
        }

        return $transactionTypes;
    }
}

if (!function_exists('getColumnValue')) {

    function getColumnValue($object, $columnOne = 'first_name', $columnTwo = 'last_name', $emptyReturn = '-')
    {
        $name = [];
        if (optional($object)->{$columnOne}) {
            $name[] = $object->{$columnOne};
        }
        if (optional($object)->{$columnTwo}) {
            $name[] = $object->{$columnTwo};
        }

        return count($name) ? implode(' ', $name) : $emptyReturn;
    }
}

if (!function_exists('getStripeMonths')) {

    function getStripeMonths()
    {
        $number = range(1, 12);
        $data = [];
        foreach ($number as $num) {
            $value = sprintf('%02d', $num);

            $data[] = '<option value="' . $value . '">' . $value . '</option>';
        }

        return implode(' ', $data);
    }
}

if (!function_exists('currencyTransactionTypes')) {
    function currencyTransactionTypes($transactionType)
    {
        $activateFor = [];
        $transactionType = request()->ajax() ? json_decode($transactionType) : $transactionType;
        if (!empty($transactionType)) {
            foreach ($transactionType as $key => $value) {
                $activateFor[$value] = '';
            }
        } else {
            $activateFor = ['' => ''];
        }

        return $activateFor;
    }
}


if (!function_exists('miniCollection')) {

    /**
     * Returns a new \App\Lib\MiniCollection object
     * @param array $hayStack optional
     * @return \App\Lib\MiniCollection;
     */
    function miniCollection($hayStack = [], $nested = false)
    {
        return new \App\Http\Helpers($hayStack, $nested);
    }
}


if (!function_exists('logoPath')) {
    function logoPath()
    {
        return !empty(settings('logo')) && file_exists(public_path('images/logos/' . settings('logo'))) 
            ? asset('public/images/logos/' . settings('logo')) 
            : asset('public/uploads/userPic/default-logo.jpg');
    }
}

if (!function_exists('getSystemLogo')) {
    function getSystemLogo($className = 'logo-size')
    {
        return '<img src="'. logoPath() .'" class="' . $className. '" alt="'. __('Logo') .'"/>';
    }
}


if (!function_exists('faviconPath')) {
    function faviconPath()
    {
        return !empty(settings('favicon')) && file_exists(public_path('images/logos/' . settings('favicon'))) 
            ? asset('public/images/logos/' . settings('favicon')) 
            : asset('public/uploads/userPic/default-image.png');
    }
}