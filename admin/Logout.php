<?php
session_start();
$_SESSION = array();
session_destroy();
header("Location: " . BASE_PATH . "/admin/Login.php"); 
exit;
?>
