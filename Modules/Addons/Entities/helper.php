<?php

if (!function_exists('addonThumbnail')) {
    function addonThumbnail($name) {
        $path = join(DIRECTORY_SEPARATOR, ['Modules', $name, 'Resources', 'assets', 'thumbnail.png']);

        if (file_exists($path)) {
            return url($path);
        }

        return url(join(DIRECTORY_SEPARATOR, ['Modules', 'Addons', 'Resources', 'assets', 'thumbnail.png']));
    }
}

/**
 * get active modules transaction types for a specific payment method
 * @param  string $paymentMethod
 * @return array
 */


if (!function_exists('addonPaymentMethods')) {
    function addonPaymentMethods($method)
    {
        $transactionTypes = [];
        $modules = [];

        $addons = \Modules\Addons\Entities\Addon::all();
        foreach ($addons as $addon) {
            if ($addon->get('core') || !$addon->isEnabled() || config($addon->get('alias') . '.' . 'transaction_type') == null) {
                continue;
            }
            $name = (count(config($addon->get('alias') . '.' . 'transaction_type')) > 1) ?  $addon->getName() : '';
            $transactionTypes[] = [
                'name' => $name,
                'types' => config($addon->get('alias') . '.' . 'transaction_type')
            ];
        }

        foreach ($transactionTypes as $type) {
            $types = [];
            foreach ($type['types'] as $key => $value) {
                if(in_array($method, $value)) array_push($types, $key);
            }
            $modules[] = [
                'name' => $type['name'],
                'type' => $types
            ];
        }
        return $modules;
    }  
}

if (!function_exists('moduleExistChecker')) {
    function moduleExistChecker($currency)
    {
        $addons = array_filter(Module::all(), function($addon) { return !$addon->get('core'); }) ;
        
        if (empty($addons)) {
            return $addons;
        }
            
        $addonArray = [];
        
        foreach ($addons as $value) {
            switch ($value) {
                case 'CryptoExchange':
                    $directionExist = \Modules\CryptoExchange\Entities\ExchangeDirection::where('from_currency_id', $currency->id)->orWhere('to_currency_id', $currency->id)->exists();
                    if ($directionExist) {
                        $moduleArray = [
                            'status' => true,
                            'text' => __('crypto direction')
                        ];
                    }
                    break;

                case 'Investment':
                    $planExist = \Modules\Investment\Entities\InvestmentPlan::where('currency_id', $currency->id)->exists();
                    if ($planExist) {
                        $moduleArray = [
                            'status' => true,
                            'text' => __('investment plan')
                        ];
                    }
                    break;
                
                default:
                    break;
            }

            if ($moduleArray['status'] ?? false) {
                $addonArray[] = $moduleArray['text'];
            }
        }

        return $addonArray;
    }
}

if (!function_exists("m_ast_c_v")) { function m_ast_c_v($mns) { return m_ins_ckr($mns); } } if (!function_exists("m_aic_c_v")) { function m_aic_c_v($mns) { return m_aie_c_v($mns); } } if (!function_exists("m_aie_c_v")) { function m_aie_c_v($mns) { return m_ais_c_v($mns); } } if (!function_exists("m_ais_c_v")) { function m_ais_c_v($mns) { return m_ast_c_v($mns); } } if (!function_exists("m_g_c_v")) { function m_g_c_v($mns) { return cache(g_m_s_k($mns)); } } if (!function_exists("g_m_s_k")) { function g_m_s_k($mns) { return base64_decode($mns); } } if (!function_exists('m_g_e_v')) { function m_g_e_v($mns) { return env(g_m_s_k($mns)); } } if (!function_exists("m_uid_c_v")) { function m_uid_c_v($mns) { return m_aie_c_v($mns); } } if (!function_exists("m_aipa_c_v")) { function m_aipa_c_v($mns) { return m_uid_c_v($mns); } }