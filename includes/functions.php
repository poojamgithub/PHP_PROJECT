<?php
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePassword($password) {
    return strlen($password) >= 6;
}

function validateFile($file) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    return in_array($file['type'], $allowedTypes) && $file['size'] <= 5000000; // Max 5MB
}
?>