<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 'admins' && $_SESSION['user_role'] != 'admin')) {
    die("Truy cáº­p bá» tá»« chá»i!");
}

if ($_SESSION['user_role'] == 'admins') {
    die("Báº¡n khÃ´ng cÃ³ quyá»n thá»±c hiá»n hÃ nh Äá»ng nÃ y!");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $conn->query("DELETE FROM orders WHERE id = $id");
    $_SESSION['status_message'] = "ÄÃ£ xÃ³a ÄÆ¡n hÃ ng!";
}

header("Location: ListDonHang.php");
exit;
?>
