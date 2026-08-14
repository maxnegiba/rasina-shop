<?php

return [
    'admin_mfa' => [
        'code_seconds' => (int) env('ADMIN_MFA_CODE_SECONDS', 600),
        'send_attempts' => (int) env('ADMIN_MFA_SEND_ATTEMPTS', 3),
        'verify_attempts' => (int) env('ADMIN_MFA_VERIFY_ATTEMPTS', 5),
    ],
];
