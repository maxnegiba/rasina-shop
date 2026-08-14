<?php

return [
    'admin_mfa' => [
        'code_seconds' => (int) env('ADMIN_MFA_CODE_SECONDS', 600),

        'idle_seconds' => (int) env(
            'ADMIN_MFA_IDLE_SECONDS',
            env('ADMIN_MFA_SESSION_SECONDS', 7200),
        ),

        'absolute_seconds' => (int) env('ADMIN_MFA_ABSOLUTE_SECONDS', 43200),

        'send_attempts' => (int) env('ADMIN_MFA_SEND_ATTEMPTS', 3),
        'verify_attempts' => (int) env('ADMIN_MFA_VERIFY_ATTEMPTS', 5),
    ],
];
