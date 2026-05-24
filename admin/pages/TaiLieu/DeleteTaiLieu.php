<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role'])) {
    header("Location: ../../index.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];

if ($id > 0) {
    $sql = "SELECT m.id, m.file_path, c.teacher_id 
            FROM lesson_materials m 
            JOIN lessons l ON m.lesson_id = l.id 
            JOIN courses c ON l.course_id = c.id 
            WHERE m.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $material = $stmt->get_result()->fetch_assoc();

    if ($material) {
        if ($role == 'teacher' && $material['teacher_id'] != $user_id) {
            $_SESSION['status_message'] = "Bạn không có quyền xóa tài liệu này!";
        } else {
            if (!empty($material['file_path'])) {
                $file_absolute_path = ROOT_PATH . '/' . $material['file_path'];
                if (file_exists($file_absolute_path)) {
                    unlink($file_absolute_path);
                }
            }
            $del = $conn->prepare("DELETE FROM lesson_materials WHERE id = ?");
            $del->bind_param("i", $id);
            
            if ($del->execute()) {
                $_SESSION['status_message'] = "Đã xóa tài liệu thành công!";
            } else {
                $_SESSION['status_message'] = "Lỗi Database!";
            }
        }
    } else {
        $_SESSION['status_message'] = "Tài liệu không tồn tại!";
    }
}

header("Location: ListTaiLieu.php");
exit;
?>
