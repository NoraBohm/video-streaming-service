<?php
require_once __DIR__ . '/vendor/autoload.php';

function load_dotenv() {
    // Load secrets from the file .env
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

function get_database_direct() {
    // Connect to database
    $mysqli = new mysqli(
        'ostrawebb.se', 
        $_ENV['DB_USER'], 
        $_ENV['DB_PASS'],
        $_ENV['DB_USER']
    );
    $mysqli->set_charset('utf8');
    return $mysqli;
}

function get_database() {
    load_dotenv();
    return get_database_direct();
}

function data_exists($data) {
    if ($data == null) {
        return false;
    }
    return (count($data) > 0);
}

function get_from_where_is_type($db, $from, $where, $is, $type) {
    $get_request = $db->prepare("SELECT * FROM " . $from . " WHERE " . $where . " = ?");
    $get_request->bind_param($type, $is);
    $get_request->execute();

    $get_result = $get_request->get_result();
    $data = $get_result->fetch_assoc();

    return $data;
}

function throw_error($message) {
    session_start();
    $_SESSION["error"] = $message;
}

function clear_error() {
    session_start();
    $_SESSION["error"] = null;
}

?>