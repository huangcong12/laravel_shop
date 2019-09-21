<?php

return [
    'alipay' => [
        'app_id' => '',
        'ali_public_key' => '',
        'private_key' => '',
        'log' => [
            'file' => storage_path('log/ali_pay.log'),
        ],
    ],

    'wechat' => [
        'app_id' => '',
        'mch_id' => '',
        'key' => '',
        'cert_client' => '',
        'cert_key' => '',
        'log' => [
            'file' => storage_path('log/wechat_pay.log')
        ],
    ]
];
