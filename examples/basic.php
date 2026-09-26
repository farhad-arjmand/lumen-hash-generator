<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use FarhadArjmand\LumenHashGenerator\TokenGenerator;
use FarhadArjmand\LumenHashGenerator\TokenHasher;

$tokens = new TokenGenerator();
$hasher = new TokenHasher();
$token = $tokens->generate(prefix: 'api_');
$digest = $hasher->digest($token);
if (!$hasher->verify($token, $digest) || $hasher->verify($token . 'tampered', $digest)) {
    throw new RuntimeException('Token verification failed.');
}
echo "Generated a token and verified its digest; secret not printed.\n";
