<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 'admins' && $_SESSION['user_role'] != 'admin')) {
    die("Truy cập bị từ chối!");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $conn->query("DELETE FROM orders WHERE id = $id");
    $_SESSION['status_message'] = "Đã xóa đơn hàng!";
}

header("Location: ListDonHang.php");
exit;
?>