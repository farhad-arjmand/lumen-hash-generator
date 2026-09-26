<?php

// Put this in the HOST application's routes/api.php, after configuring Sanctum.
// Example only: the package does not automatically register this endpoint.
// Authorize the user's permission to issue credentials before using them in your app.

use FarhadArjmand\LumenHashGenerator\TokenGenerator;
use Illuminate\Support\Facades\Route;

Route::post('/random-identifiers', function (TokenGenerator $generator) {
    return response()->json(['identifier' => $generator->generate(prefix: 'ref_')])
        ->header('Cache-Control', 'no-store');
})->middleware(['auth:sanctum', 'throttle:10,1']);
