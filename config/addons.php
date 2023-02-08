<?php

$adminPrefix = env('ADMIN_PREFIX');

return [
    'name' => 'Addons',

    'options' => [
        ['label' => 'Settings', 'url' => '#']
    ],

    'route_group' => [
        'authenticated' => [
            'admin' => [
                'prefix' => $adminPrefix,
                'middleware' => ['guest:admin', 'locale', 'ip_middleware']
            ],
            'user' => [
                'middleware' => ['guest:users', 'locale', 'twoFa', 'check-user-inactive']
            ]
        ],
        'unauthenticated' => [
            'admin' => [
                'prefix' => $adminPrefix,
                'middleware' => ['no_auth:admin', 'locale', 'ip_middleware']
            ],
            'user' => [
                'middleware' => ['no_auth:users', 'locale']
            ],
        ]
    ],

    'items' => [
        'fv8wtkr1jfc' => 'CryptoExchange',
        'v33hrp9x5en' => 'MobileMoney',
        'azv5h23qhwe' => 'Referral',
        'xnucugaeu6q' => 'Remittance',
        'zlwds4xzwjk' => 'shop',
        'y6udqa9sf3v' => 'EventTicket',
        'wr6h7efkefa' => 'Agent',
        'f9t4qeh9dmq' => 'PaymentLink',
        'hxrku28ngdp' => 'Woocommerce',
        'mn7hifa2ruq' => 'Investment'
    ],
    
    'cache_keys' => [
        'paymoney_cache-preferences',
        'paymoney_cache-settings'
    ],

    'file_permission' => 755
];