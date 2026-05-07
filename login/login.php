<?php
function check_password($password, $user_data) {
    if (password_verify($password, $user_data['password'])) {
        session_start();
        $_SESSION['id'] = $user_data['id'];
        return true;
    } else {
        wrong_provided_data();
        return false;
    }
}

$username_or_email = $_POST["username-or-email"];
$password = $_POST["password"];

$success = false;

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/account_functions.php';

if ($username_or_email && $password) {
    $db = get_database();

    $has_email = is_email($username_or_email);

    $user_data = account_data_input($db, $has_email, $username_or_email);
    
    if (data_exists($user_data)) {
        $success = check_password($password, $user_data);
    } else {
        wrong_provided_data();
    }
} else {
    data_field_left_empty();
}

if ($success) {
    header("Location: /");
} else {
    header("Location: /login");
}
?>