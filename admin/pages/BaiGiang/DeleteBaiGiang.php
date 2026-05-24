<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role'])) {
    header("Location: ../../index.php");
    exit;
}

if ($_SESSION['user_role'] == 'admins') {
    die("Bạn không có quyền thực hiện hành động này!");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];

if ($id > 0) {
    $stmt = $conn->prepare("SELECT lessons.id, lessons.attachment, courses.teacher_id 
                            FROM lessons 
                            JOIN courses ON lessons.course_id = courses.id 
                            WHERE lessons.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $lesson = $stmt->get_result()->fetch_assoc();

    if ($lesson) {
        if ($role == 'teacher' && $lesson['teacher_id'] != $user_id) {
            $_SESSION['status_message'] = "Bạn không có quyền xóa bài giảng này!";
        } else {
            if (!empty($lesson['attachment'])) {
                $file_path = ROOT_PATH . '/' . $lesson['attachment'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }

            $del = $conn->prepare("DELETE FROM lessons WHERE id = ?");
            $del->bind_param("i", $id);
            if ($del->execute()) {
                $_SESSION['status_message'] = "Đã xóa bài giảng thành công!";
            } else {
                $_SESSION['status_message'] = "Lỗi Database: " . $conn->error;
            }
        }
    } else {
        $_SESSION['status_message'] = "Bài giảng không tồn tại!";
    }
} else {
    $_SESSION['status_message'] = "ID không hợp lệ!";
}

header("Location: ListBaiGiang.php");
exit;
?>
