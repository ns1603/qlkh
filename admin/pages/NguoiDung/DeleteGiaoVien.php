<?php
session_start();
include(__DIR__ . '/../../../config.php');
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 'admins' && $_SESSION['user_role'] != 'admin')) {
    die("Truy cập bị từ chối!");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$my_id = $_SESSION['user_id'];

if ($id == $my_id) {
    $_SESSION['status_message'] = "Bạn không thể tự xóa tài khoản của chính mình!";
    header("Location: ListGiaoVien.php");
    exit;
}

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role != 'student'");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['status_message'] = "Đã xóa giáo viên thành công!";
    } else {
        $_SESSION['status_message'] = "Lỗi: Không thể xóa (Có thể giáo viên này đang phụ trách khóa học).";
    }
}

header("Location: ListGiaoVien.php");
exit;
?>