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

function wrong_provided_data() {
    throw_error("Username, eMail or password is incorrect");
}

$username_or_email = $_POST["username-or-email"];
$password = $_POST["password"];

$success = false;

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/account_functions.php';

if ($username_or_email && $password) {
    $db = get_database();

    $has_email = is_email($username_or_email);

    if($has_email) {
        $user_data = get_from_where_is_type($db, "video_users", "email", $username_or_email, "s");
    } else {
        $user_data = get_from_where_is_type($db, "video_users", "username", $username_or_email, "s");
    }
    
    if (data_exists($user_data)) {
        $success = check_password($password, $user_data);
    } else {
        wrong_provided_data();
    }
} else {
    throw_error("A data field was left empty");
}

if ($success) {
    header("Location: /");
} else {
    header("Location: /login");
}
?>