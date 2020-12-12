<?php

//Instructions:
//Create a file named encryption.php 
//and add all the code from this page. 
//
//Include encryption.php in any pages that utilize encryption.
//You do not need to make any changes to the encryption code.
//
//This code provides access to two functions: encrypt($message) and
//decrypt($message). 
//Encrypt $custID on the sending page and decrypt it on
//the receiving page. 
//
//There is a small demo at the bottom of this file that
//shows how to encrypt and decrypt. 
//

//generate key and stores in session
session_start();

if($_SESSION["key"] == null)
{
   $_SESSION["key"] = sodium_crypto_secretbox_keygen();   
}

function encrypt($message) {
   //requires string value.
   $message = strval($message);
   $key = $_SESSION["key"];
   
   $nonce = random_bytes(
        SODIUM_CRYPTO_SECRETBOX_NONCEBYTES
    );

    $cipher = base64_encode(
        $nonce.
        sodium_crypto_secretbox(
            $message,
            $nonce,
            $key
        )
    );
    sodium_memzero($message);
    sodium_memzero($key);
    return $cipher;
}

function decrypt($encrypted) {
   $key = $_SESSION["key"];
    $decoded = base64_decode($encrypted);
    if ($decoded === false) {
        throw new Exception('Scream bloody murder, the encoding failed');
    }
    if (mb_strlen($decoded, '8bit') < (SODIUM_CRYPTO_SECRETBOX_NONCEBYTES + SODIUM_CRYPTO_SECRETBOX_MACBYTES)) {
        throw new Exception('Scream bloody murder, the message was truncated');
    }
    $nonce = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
    $ciphertext = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');

    $plain = sodium_crypto_secretbox_open(
        $ciphertext,
        $nonce,
        $key
    );
    if ($plain === false) {
         throw new Exception('the message was tampered with in transit');
    }
    sodium_memzero($ciphertext);
    sodium_memzero($key);
    return $plain;
}

//test encryption functions
$demoActive = false; //set this to false when using the functions in your site
if ($demoActive) {
   
   echo "Testing encryption <br>";
   
//encrypt
   $custID = 64336;
   echo "custID: $custID <br>";

   //encrypt
   $custIDe = encrypt($custID);

   echo "custIDe: $custIDe <br>";

   //decrypt
   $custID = decrypt($custIDe);
   echo "custID: $custID <br>";
}

//Source: https://www.php.net/manual/en/intro.sodium.php

?>