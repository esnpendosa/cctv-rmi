<?php

return [
    'go2rtc_host'              => env('CCTV_GO2RTC_HOST', 'localhost'),
    'go2rtc_port'              => env('CCTV_GO2RTC_PORT', 1984),
    'health_check_interval'    => env('CCTV_HEALTH_CHECK_INTERVAL', 5),
    'default_tax_rate'         => env('CCTV_DEFAULT_TAX_RATE', 12),
    'invoice_prefix'           => env('CCTV_INVOICE_PREFIX', 'INV'),
    'quotation_prefix'         => env('CCTV_QUOTATION_PREFIX', 'QUO'),
    'public_stream_rate_limit' => env('CCTV_PUBLIC_RATE_LIMIT', 60),
    'invoice_due_days'         => env('CCTV_INVOICE_DUE_DAYS', 30),
];
