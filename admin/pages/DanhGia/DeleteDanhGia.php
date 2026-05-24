<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role'])) { header("Location: ../../index.php"); exit; }

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];

if ($id > 0) {
    $sql = "SELECT r.id, c.teacher_id 
            FROM ratings r
            JOIN courses c ON r.course_id = c.id
            WHERE r.id = $id";
    $check = $conn->query($sql)->fetch_assoc();

    if ($check) {
        if ($role == 'teacher' && $check['teacher_id'] != $user_id) {
            $_SESSION['status_message'] = "Bạn không được xóa đánh giá của khóa học khác!";
        } else {
            $conn->query("DELETE FROM ratings WHERE id = $id");
            $_SESSION['status_message'] = "Đã xóa đánh giá!";
        }
    }
}

header("Location: ListDanhGia.php");
exit;
?>
