<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role'])) { header("Location: ../../index.php"); exit; }

if ($_SESSION['user_role'] == 'admins') {
    die("Báº¡n khÃ´ng cÃ³ quyá»n thá»±c hiá»n hÃ nh Äá»ng nÃ y!");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id > 0) {
    $conn->query("DELETE FROM users WHERE id = $id");
    $_SESSION['status_message'] = "ÄÃ£ xÃ³a há»c viÃªn!";
}
header("Location: ListHocVien.php");
exit;
?>
