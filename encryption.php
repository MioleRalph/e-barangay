<?php
function encryptData($data) {
    return openssl_encrypt($data, 'AES-256-CBC', ENCRYPTION_KEY, 0, ENCRYPTION_IV);
}

function decryptData($encryptedData) {
    return openssl_decrypt($encryptedData, 'AES-256-CBC', ENCRYPTION_KEY, 0, ENCRYPTION_IV);
}
?>
