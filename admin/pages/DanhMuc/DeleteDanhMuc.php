<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role'])) {
    header("Location: ../../index.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $conn->query("UPDATE courses SET category_id = NULL WHERE category_id = $id");

    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['status_message'] = "Đã xóa danh mục thành công!";
    } else {
        $_SESSION['status_message'] = "Lỗi khi xóa: " . $conn->error;
    }
} else {
    $_SESSION['status_message'] = "ID danh mục không hợp lệ!";
}

header("Location: ListDanhMuc.php");
exit;
?>