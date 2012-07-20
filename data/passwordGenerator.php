<?php

/**
 * Generate password for the database.
 * @see Symfony/Component/Security/Core/Encoder/MessageDigestPasswordEncoder.php
 */

$pass = 'password';

$digest = hash('sha512', $pass, true);

for ($i = 1; $i < 5000; $i++) {
    $digest = hash('sha512', $digest.$pass, true);
}

$encoded = base64_encode($digest);

echo preg_quote($encoded);


