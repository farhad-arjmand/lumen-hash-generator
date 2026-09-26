<?php

return [
    // Optional HMAC key for token digests; keep it stable and outside your database.
    // Null uses SHA-256, appropriate for the high-entropy random tokens generated here.
    // If configured, the key must contain at least 32 bytes.
    'pepper' => env('HASH_GENERATOR_PEPPER'),
];
