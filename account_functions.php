<?php
require_once __DIR__ . '/functions.php';

function user_exists($db) {

}

function is_email($email_address) {
    return (str_contains($email_address, "@") && str_contains($email_address, "."));
}

?>