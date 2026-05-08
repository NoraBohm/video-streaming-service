<?php

use GrahamCampbell\ResultType\Success;

$displayname = $_POST["displayname"];
$username = $_POST["username"];
$email = $_POST["email"];
$password = $_POST["password"];

if ($username !== null) {
    $username = trim($username);
}
if ($email !== null) {
    $email = trim($email);
}
if ($displayname == "" || $displayname == null) {
    $displayname = $username;
}

$success = false;

//require_once __DIR__ . '/functions.php';
//require_once __DIR__ . '/account_functions.php';

/*if (!session_()) {
    session_start();
}*/

require_once $_SERVER['DOCUMENT_ROOT'] . '/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/account_functions.php';

function register_account($db, $username, $displayname, $email, $password) {
    $request = $db->prepare("INSERT INTO video_users (username, displayname, email, password) VALUES (?, ?, ?, ?)");
    $request->bind_param("ssss", $username, $displayname, $email, hash_password($password));
    $request_results = $request->execute();
    return [$request_results, $db->insert_id];
    //return $db->insert_id;
}

if ($username && $email && $password) {
    $username_is_email = is_email($username);
    if (! $username_is_email) {
        $has_email = is_email($email);
        if ($has_email) {
            $db = get_database();

            $user_data_username = account_data_input($db, false, $username);
            $user_data_email = account_data_input($db, true, $email);
            if (data_exists($user_data_username) || data_exists($user_data_username)) {
                throw_error("Username or eMail already taken");
            } else {
                list($success, $user_id) = register_account($db, $username, $displayname, $email, $password);
                if ($success) {
                    //session_start();
                    $_SESSION['id'] = $user_id;
                    $Success = true;
                } else {
                    throw_error("Internal error registering account");
                }
            }
        } else {
            throw_error("Specified eMail is invalid");
        }
    } else {
        throw_error("Username is a eMail address");
    }
    

    
} else {
    data_field_left_empty();
}

if ($success) {
    header("Location: /");
} else {
    header("Location: /register");
}
?>