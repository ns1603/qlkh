<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role'])) { header("Location: ../../index.php"); exit; }

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id > 0) {
    $conn->query("DELETE FROM users WHERE id = $id");
    $_SESSION['status_message'] = "Đã xóa học viên!";
}
header("Location: ListHocVien.php");
exit;
?>