<?php
require_once __DIR__ . '/functions.php';

/**
 * A functions that checks if a string contains a eMail address.
 * 
 * A functions that checks if a string contains a eMail address by identifying the @ symbol and dot required for a eMail address with a domain.
 * 
 * @param string $email_address;
 * 
 * @return bool;
 */
function is_email($email_address) {
    return (str_contains($email_address, "@") && str_contains($email_address, "."));
}

/**
 * A function that hashes a password using the default password hashing algorithm that automatically salts it.
 * 
 * @param string $password;
 * 
 * @return string;
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * A function that gets your user data correctly from the database for both usernames and eMail adresses.
 * 
 * @param mysqli $db;
 * @param bool $has_email;
 * @param string $username_or_email;
 * 
 * @return array|false|null;
 */
function account_data_input($db, $has_email, $username_or_email) {
    if($has_email) {
        return get_from_where_is_type($db, "video_users", "email", $username_or_email, "s");
    } else {
        return get_from_where_is_type($db, "video_users", "username", $username_or_email, "s");
    }
}

/**
 * A function that throws a standard error for providing incorrect data in a login or register form.
 * 
 * @return void;
 */
function wrong_provided_data() {
    throw_error("Username, eMail or password is incorrect");
}

/**
 * A function that throws a standard error for a empty data in a login or register form.
 * 
 * @return void;
 */
function data_field_left_empty() {
    throw_error("A data field was left empty");
}
?>