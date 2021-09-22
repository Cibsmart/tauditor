<?php

return [
    'client_id' => env('FIDELITY_CLIENT_ID'),
    'client_secret' => env('FIDELITY_CLIENT_SECRET'),
    'generate_token_url' => env('FIDELITY_GENERATE_ACCESS_TOKEN_URL'),
    'confirm_deduction_url_bulk' => env('FIDELITY_CONFIRM_DEDUCTION_URL_BULK'),
    'confirm_deduction_url_single' => env('FIDELITY_CONFIRM_DEDUCTION_URL_SINGLE'),
];
