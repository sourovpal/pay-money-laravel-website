<?php

/**
 * @package ExchangeMoneyService
 * @author tehcvillage <support@techvill.org>
 * @contributor Md Abdur Rahaman Zihad <[zihad.techvill@gmail.com]>
 * @created 30-11-2022
 */

namespace App\Services;

use App\Exceptions\Api\V2\ExchangeMoneyException;
use App\Http\Helpers\Common;
use App\Models\{
    CurrencyExchange,
    FeesLimit,
    Currency,
    Wallet
};
use Illuminate\Database\Eloquent\Collection;

class ExchangeMoneyService
{
    /**
     * Common
     *
     * @var Common
     */
    protected $helper;

    /**
     * Currencies needed in the lifecycle
     *
     * @var Currency
     */
    private $currencies = null;


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
     * Get available currencies of the user
     *
     * @return array
     */
    public function getSelfCurrencies()
    {
        $result = [
            "currencies" => []
        ];

        Wallet::with("currency:id,code,type")
            ->where("user_id", auth()->id())
            ->whereHas("active_currency")
            ->join("fees_limits", "fees_limits.currency_id", "wallets.currency_id")
            ->where("fees_limits.has_transaction", "Yes")
            ->where("fees_limits.transaction_type_id", Exchange_From)
            ->get()
            ->map(function ($item) use (&$result) {
                if ($item->is_default == "Yes") {
                    $result["default"] = $item->currency_id;
                }
                $result["currencies"][$item->currency_id] = [
                    "id" => $item->currency_id,
                    "code" => optional($item->currency)->code,
                ];
                return $item;
            });
        $result["currencies"] = array_values($result["currencies"]);
        return $result;
    }


    /**
     * Exchange amount limit check
     *
     * @param double $amount
     * @param int $currencyId
     *
     * @return array
     *
     * @throws Exception
     */
    public function amountLimitCheck($amount, $currencyId)
    {
        $wallet = Wallet::with("currency")->where(["currency_id" => $currencyId, "user_id" => auth()->id()])->first();

        $feesDetails = FeesLimit::with("currency")->where(["transaction_type_id" => Exchange_From, "currency_id" => $currencyId])->first();

        if (is_null($wallet)) {
            throw new ExchangeMoneyException(__("Wallet not found."), [
                "reason" => "walletNotFound",
                "currencyCode" => null,
                "message" => __("Wallet not found."),
                "status" => "401"
            ]);
        }
        // If currency fees are not set
        if (is_null($feesDetails)) {
            throw new ExchangeMoneyException(__("Currency fees are not set."), [
                "reason" => "feesNotSet",
                "currencyCode" => optional($wallet->currency)->code,
                "message" => __("Currency fees are not set."),
                "status" => "401"
            ]);
        }

        // If currency fee does not support transaction
        if ($feesDetails->has_transaction == "No") {
            throw new ExchangeMoneyException(".", [
                "reason" => "noHasTransaction",
                "currencyCode" => optional($wallet->currency)->code,
                "message" => __("The currency :x fees limit is inactive.", ["x" => optional($feesDetails->currency)->code]),
                "status" => "401"
            ]);
        }

        // Calculate total amount with the fees
        $checkAmount = $amount + ($feesDetails->charge_fixed ?? 0) + ((($feesDetails->charge_percentage ?? 0) / 100) * $amount);

        // Check if the wallet has enough balance
        $this->walletHasBalance($wallet, $checkAmount);
        // Check if the wallet is in required limit
        $this->amountIsInLimit($feesDetails, $amount);

        return [
            "amount" => $amount,
            "total_amount" => $checkAmount,
            "balance" => $wallet->balance,
            "currency" => optional($wallet->currency)->code,
            "message" => __("Valid amount.")
        ];
    }


    /**
     * Returns from source and destination wallets
     *
     * @param int $fromCurrrency
     * @param int $toCurrency
     *
     * @return array
     *
     * @throws ExchangeMoneyException
     */
    public function getWallets($fromCurrency, $toCurrency)
    {
        $wallets = Wallet::with("currency")->whereIn("currency_id", [$fromCurrency, $toCurrency])->where("user_id", auth()->id())->get();

        if (is_null($wallets)) {
            throw new ExchangeMoneyException(__("Wallets connected to the currencies are not found."));
        }

        $fromWallet = $wallets->where("currency_id", $fromCurrency)->first();

        $toWallet = $wallets->where("currency_id", $toCurrency)->first();

        if (is_null($fromWallet)) {
            throw new ExchangeMoneyException(__("Source wallet is not found."), [
                "destination" => [
                    "balance" => formatNumber($toWallet->balance, $toCurrency),
                    "currency" => optional($toWallet->currency)->code
                ]
            ]);
        }

        if (is_null($toWallet)) {
            throw new ExchangeMoneyException(__("Destination wallet is not found."), [
                "source" => [
                    "balance" => formatNumber($fromWallet->balance, $fromCurrency),
                    "currency" => optional($fromWallet->currency)->code
                ]
            ]);
        }

        return [
            "source" => [
                "balance" => formatNumber($fromWallet->balance, $fromCurrency),
                "currency" => optional($fromWallet->currency)->code
            ],
            "destination" => [
                "balance" => formatNumber($toWallet->balance, $toCurrency),
                "currency" => optional($toWallet->currency)->code
            ]
        ];
    }


    /**
     * Returns available destination wallets
     *
     * @param int $sourceCurrency
     *
     * @return array
     *
     * @throws ExchangeMoneyException
     */
    public function getAvailableDestinationWallets($sourceCurrency)
    {
        $wallets = Currency::select("currencies.id", "currencies.code", "wallets.balance")
            ->where("currencies.id", "!=", $sourceCurrency)
            ->where("currencies.status", "Active")
            ->leftJoin("fees_limits", "fees_limits.currency_id", "currencies.id")
            ->leftJoin("wallets", "wallets.currency_id", "currencies.id")
            ->where("fees_limits.transaction_type_id", Exchange_From)
            ->where("fees_limits.has_transaction", "Yes")
            ->where("wallets.user_id", auth()->id())
            ->get();

        if (count($wallets) == 0) {
            throw new ExchangeMoneyException(__("No destination wallet available."));
        }
        return $wallets;
    }


    /**
     * Get source currency to exchange currency exchange rates
     *
     * @param int $fromCurrencyId
     * @param int $toCurrencyId
     * @param double $amount
     *
     * @return array
     *
     * @throws ExchangeMoneyException
     */
    public function getCurrenciesExchangeRates($fromCurrencyId, $toCurrencyId, $amount)
    {
        $currencies = $this->getCurrencies([$fromCurrencyId, $toCurrencyId]);;

        $fromCurrency = $currencies->where("id", $fromCurrencyId)->first();

        $toCurrency = $currencies->where("id", $toCurrencyId)->first();

        if (is_null($fromCurrency)) {
            throw new ExchangeMoneyException(__("Source currency not found."));
        }

        if (is_null($toCurrency)) {
            throw new ExchangeMoneyException(__("Destination wallet currency not found."));
        }

        if ($toCurrency->exchange_from == "api" && settings("exchange_enabled_api") != "Disabled" && ((settings("exchange_enabled_api") == "currency_converter_api_key" && !empty(settings("currency_converter_api_key")))  || (settings("exchange_enabled_api") == "exchange_rate_api_key" && !empty(settings("exchange_rate_api_key"))))) {
            $conversionRate = getCurrencyRate($fromCurrency->code, $toCurrency->code);
        } else {
            $defaultCurrency = Currency::where("default", 1)->first();
            $conversionRate = ($defaultCurrency->rate / $fromCurrency->rate) * $toCurrency->rate;
        }

        $totalAmount = $conversionRate * $amount;
        $formattedAmount = number_format($conversionRate, 8, ".", "");

        return [
            "rate" => (float) $formattedAmount,
            "code" => $toCurrency->code,
            "symbol" => $toCurrency->symbol,
            "total_amount" => formatNumber($totalAmount),
            "formatted_amount" => moneyFormat($toCurrency->code, formatNumber($totalAmount))
        ];
    }



    /**
     * Check if the wallet has enough balance
     *
     * @param Wallet $wallet
     * @param double $amount
     *
     * @return bool
     *
     * @throws ExchangeMoneyException
     */
    private function walletHasBalance(Wallet $wallet, $amount)
    {
        if ($amount > $wallet->balance || $wallet->balance < 0) {
            throw new ExchangeMoneyException(".", [
                "reason" => "insufficientBalance",
                "currencyCode" => optional($wallet->currency)->code,
                "message" => __("Sorry, wallet does not have enough balance."),
                "status" => "401"
            ]);
        }

        return true;
    }


    /**
     * Check if the transfer amount does not exceeds the limit
     *
     * @param FeesLimit $fees
     * @param double $amount
     *
     * @return bool
     *
     * @throws ExchangeMoneyException
     */
    public function amountIsInLimit(FeesLimit $fees, $amount)
    {
        $minError = (float) $amount < $fees->min_limit;
        $maxError = $fees->max_limit &&  $amount > $fees->max_limit;

        if (!$minError && !$maxError) {
            return true;
        }
        // Check if the transfer amount exceeds the limit set by the admin
        if ($minError && $maxError) {
            throw new ExchangeMoneyException(__("Amount must be between :x and :y", ["x" => moneyFormat(optional($fees->currency)->code, formatNumber($fees->min_limit)), "y" => moneyFormat(optional($fees->currency)->code, formatNumber($fees->max_limit))]), [
                "reason" => "minMaxLimit",
                "currencyCode" => optional($fees->currency)->code,
                "minLimit" => $fees->min_limit,
                "maxLimit" => $fees->max_limit,
                "message" => __("Minimum amount should be :x and maximum amount :y", ["x" => moneyFormat(optional($fees->currency)->code, formatNumber($fees->min_limit)), "y" => moneyFormat(optional($fees->currency)->code, formatNumber($fees->max_limit))]),
                "status" => "401"
            ]);
        } elseif ($minError) {
            throw new ExchangeMoneyException(__("Amount must be greater than or equal :x", ["x" => moneyFormat(optional($fees->currency)->code, formatNumber($fees->min_limit))]), [
                "reason" => "minLimit",
                "currencyCode" => optional($fees->currency)->code,
                "minLimit" => $fees->min_limit,
                "message" => __("Amount is lower than minimum limit :y", ["y" => moneyFormat(optional($fees->currency)->code, formatNumber($fees->min_limit))]),
                "status" => "401"
            ]);
        } else {
            throw new ExchangeMoneyException(__("Amount must be less than or equal :x", ["x" => moneyFormat(optional($fees->currency)->code, formatNumber($fees->max_limit))]), [
                "reason" => "maxLimit",
                "currencyCode" => optional($fees->currency)->code,
                "maxLimit" => $fees->max_limit,
                "message" => __("Amount is greater than maximum limit :y", ["y" => moneyFormat(optional($fees->currency)->code, formatNumber($fees->max_limit))]),
                "status" => "401"
            ]);
        }
    }

    /**
     * Review exchange request
     *
     * @param int $fromCurrencyId
     * @param int $toCurrencyId
     * @param double $amount
     *
     * @return array
     *
     * @throws ExchangeMoneyException
     */
    public function reviewExchangeRequest($fromCurrencyId, $toCurrencyId, $amount)
    {
        $rateDetails = $this->getCurrenciesExchangeRates($fromCurrencyId, $toCurrencyId, $amount);

        $rate = $rateDetails["rate"];

        $feesDetails = $this->helper->getFeesLimitObject(["currency"], Exchange_From, $fromCurrencyId, null, null, ["*"]);

        if (is_null($feesDetails)) {
            throw new ExchangeMoneyException(__("Source currency fees not set."));
        }

        $wallet = Wallet::with("currency")->where(["currency_id" => $fromCurrencyId, "user_id" => auth()->id()])->first();

        $feesChargePercentage = $amount * (($feesDetails->charge_percentage / 100) ?? 1);

        $totalFess = $feesChargePercentage + ($feesDetails->charge_fixed ?? 0);

        $this->walletHasBalance($wallet, $totalFess);

        $this->amountIsInLimit($feesDetails, $amount);

        $destinationAmount = $rate * $amount;

        $totalAmount = $amount + $totalFess;

        $toCurrency = $this->getCurrencies($toCurrencyId);

        $success["destination_amount"] = $destinationAmount;
        $success["destination_amount_formatted"] = moneyFormat($toCurrency->code, formatNumber($destinationAmount));
        $success["total_amount"] = $totalAmount;
        $success["total_amount_formatted"] = moneyFormat(optional($wallet->currency)->code, formatNumber($totalAmount));
        $success["total_fees"] = $totalFess;
        $success["total_fees_formatted"] = moneyFormat(optional($wallet->currency)->code, formatNumber($totalFess));
        $success["exchange_rate"] = $rate;
        $success["exchange_rate_formatted"] = formatNumber($rate);

        return $success;
    }


    public function exchangeMoney($fromCurrencyId, $toCurrencyId, $amount)
    {
        $user_id              = auth()->id();
        $uuid                 = unique_code();
        $fromWallet           = $this->helper->getUserWallet([], ["user_id" => $user_id, "currency_id" => $fromCurrencyId], ["id", "currency_id", "balance"]);
        $toWallet             = $this->helper->getUserWallet([], ["user_id" => $user_id, "currency_id" => $toCurrencyId], ["id", "balance"]);
        $feesDetails          = $this->helper->getFeesLimitObject([], Exchange_From, $fromCurrencyId, null, null, ["*"]);

        $totalFees = ($feesDetails->charge_fixed ?? 0) + ((($feesDetails->charge_percentage ?? 0) / 100) * $amount);

        $rateDetails = $this->getCurrenciesExchangeRates($fromCurrencyId, $toCurrencyId, $amount);

        $rate = $rateDetails["rate"];

        $destinationAmount = $amount * $rate;

        $this->amountIsInLimit($feesDetails, $amount);

        $arr = [
            "user_id" => $user_id,
            "toWalletCurrencyId" => $toCurrencyId, //
            "fromWallet" => $fromWallet,
            "toWallet" => $toWallet,
            "finalAmount" => $destinationAmount,
            "uuid" => $uuid,
            "destinationCurrencyExRate" => $rate,
            "amount" => $amount,
            "fee" => $totalFees,
            "charge_percentage" => $feesDetails->charge_percentage,
            "charge_fixed" => $feesDetails->charge_fixed,
            "formattedChargePercentage" => $amount * ($feesDetails->charge_percentage / 100),
        ];

        //Get response
        $response = (new CurrencyExchange())->processExchangeMoneyConfirmation($arr, "mobile");

        if ($response["status"] != 200 && (!isset($response["exchangeCurrencyId"]) || $response["exchangeCurrencyId"] == null)) {
            throw new ExchangeMoneyException($response["ex"]["message"], $response);
        }
        return array_merge($response, ["status" => true]);
    }


    /**
     * Get the currencies
     *
     * @param array|int $ids
     *
     * @return Collection|Currency|null
     */
    private function getCurrencies($ids = [])
    {
        if (is_null($this->currencies)) {
            $this->currencies = Currency::whereIn("id", (array) $ids)->get();
        }
        if (!is_array($ids)) {
            return $this->currencies->where("id", $ids)->first();
        }
        return $this->currencies;
    }
}
