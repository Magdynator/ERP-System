<?php

return [
    'currency' => env('ERP_CURRENCY', 'USD'),
    'audit' => [
        'enabled' => env('ERP_AUDIT_ENABLED', true),
        'channel' => env('ERP_AUDIT_CHANNEL', 'single'),
    ],
];
