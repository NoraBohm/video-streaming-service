<?php
require_once __DIR__ . '/functions.php';

function user_exists($db) {

}

function is_email($email_address) {
    return (str_contains($email_address, "@") && str_contains($email_address, "."));
}

function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function account_data_input($db, $has_email, $username_or_email) {
    if($has_email) {
        return get_from_where_is_type($db, "video_users", "email", $username_or_email, "s");
    } else {
        return get_from_where_is_type($db, "video_users", "username", $username_or_email, "s");
    }
}

function wrong_provided_data() {
    throw_error("Username, eMail or password is incorrect");
}

function data_field_left_empty() {
    throw_error("A data field was left empty");
}
?>