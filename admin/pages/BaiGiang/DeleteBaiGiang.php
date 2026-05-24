<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role'])) {
    header("Location: ../../index.php");
    exit;
}

if ($_SESSION['user_role'] == 'admins') {
    die("Báº¡n khÃ´ng cÃ³ quyá»n thá»±c hiá»n hÃ nh Äá»ng nÃ y!");
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
            $_SESSION['status_message'] = "Báº¡n khÃ´ng cÃ³ quyá»n xÃ³a bÃ i giáº£ng nÃ y!";
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
                $_SESSION['status_message'] = "ÄÃ£ xÃ³a bÃ i giáº£ng thÃ nh cÃ´ng!";
            } else {
                $_SESSION['status_message'] = "Lá»i Database: " . $conn->error;
            }
        }
    } else {
        $_SESSION['status_message'] = "BÃ i giáº£ng khÃ´ng tá»n táº¡i!";
    }
} else {
    $_SESSION['status_message'] = "ID khÃ´ng há»£p lá»!";
}

header("Location: ListBaiGiang.php");
exit;
?>
