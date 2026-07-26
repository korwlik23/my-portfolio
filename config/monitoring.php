<?php

return [
    'health_token' => env('HEALTH_CHECK_TOKEN'),
    'check_queue_table' => env('HEALTH_CHECK_QUEUE_TABLE', true),
    'admin_email' => env('ADMIN_EMAIL'),
    'email_alerts' => env('OPERATIONAL_EMAIL_ALERTS', true),
];
