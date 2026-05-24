<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role'])) { header("Location: ../../index.php"); exit; }

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];

if ($id > 0) {
    $sql = "SELECT cmt.id, c.teacher_id 
            FROM comments cmt
            JOIN lessons l ON cmt.lesson_id = l.id
            JOIN courses c ON l.course_id = c.id
            WHERE cmt.id = $id";
    
    $check = $conn->query($sql)->fetch_assoc();

    if ($check) {
        if ($role == 'teacher' && $check['teacher_id'] != $user_id) {
            $_SESSION['status_message'] = "Bạn không có quyền xóa bình luận này!";
        } else {
            $conn->query("DELETE FROM comments WHERE id = $id");
            $_SESSION['status_message'] = "Đã xóa bình luận!";
        }
    }
}

header("Location: ListBinhLuan.php");
exit;
?>