<?php
session_start();
$_SESSION = array();
session_destroy();
header("Location: /Learning/admin/Login.php"); 
exit;
?>