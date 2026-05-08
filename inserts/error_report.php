<?php
//session_start();

if ($_SESSION["error"] !== null) {
    echo "<div class=\"error\">" . htmlspecialchars($_SESSION["error"]) . "</div>";
    $_SESSION["error"] = null;
}
?>