<?php
require_once __DIR__ . '/vendor/autoload.php';
/**
 * Load secrets from the file .env to be used to set the $_ENV variable for environment variables
 * 
 * @return array<string, string|null>
 */
function load_dotenv() {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}
/**
 * Connect to database and use utf8
 * 
 * @return mysqli;
 */
function get_database_direct() {
    $mysqli = new mysqli(
        'ostrawebb.se', 
        $_ENV['DB_USER'], 
        $_ENV['DB_PASS'],
        $_ENV['DB_USER']
    );
    $mysqli->set_charset('utf8');
    return $mysqli;
}

/**
 * Connect to database and use utf8, but with loading the environemnt variables automatically
 * 
 * @return mysqli;
 */
function get_database() {
    load_dotenv();
    return get_database_direct();
}
/**
 * @param null|array $data;
 */
function data_exists($data) {
    if ($data == null) {
        return false;
    }
    return (count($data) > 0);
}

/**
 * Get data where something from somewhewhere is something of a certain type.
 * 
 * A configurable standrad command to recieve a row from a database where something is a value of a certain type.
 * 
 * @param mysqli $db;
 * @param string $from;
 * @param string $where;
 * @param string $type;
 * @param mixed $is;
 * 
 * @return array|false|null;
 */
function get_from_where_is_type($db, $from, $where, $is, $type) {
    $get_request = $db->prepare("SELECT * FROM " . $from . " WHERE " . $where . " = ?");
    $get_request->bind_param($type, $is);
    $get_request->execute();

    $get_result = $get_request->get_result();
    $data = $get_result->fetch_assoc();

    return $data;
}

/**
 * Puts a error message in a session variable called "error".
 * 
 * @param string $message;
 * 
 * @return void;
 */
function throw_error($message) {
    session_start();
    $_SESSION["error"] = $message;
}

/**
 * Clears the session variable called "error" that contains error messages.
 * 
 * @return void;
 */
function clear_error() {
    session_start();
    $_SESSION["error"] = null;
}

?>