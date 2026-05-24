<?php
session_start();

// 🔥 BẮT BUỘC PHẢI CÓ
require_once ROOT_PATH . '/config.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] == 'admins') {
    die("Bạn không có quyền thực hiện hành động này!");
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$quiz_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];

// 0. Kiểm tra quyền sở hữu đối với Giảng viên
if ($role == 'teacher') {
    $check_sql = "SELECT q.id FROM quizzes q 
                  JOIN courses c ON q.course_id = c.id 
                  WHERE q.id = $quiz_id AND c.teacher_id = $user_id";
    $check_res = $conn->query($check_sql);
    if ($check_res->num_rows === 0) {
        die("❌ Bạn không có quyền xóa đề thi này!");
    }
}

// 1. Xóa kết quả làm bài
$conn->query("DELETE FROM quiz_results WHERE quiz_id = $quiz_id");
$conn->query("DELETE FROM exam_results WHERE quiz_id = $quiz_id");
$conn->query("DELETE FROM quiz_attempts WHERE quiz_id = $quiz_id");

// 2. Xóa đáp án
$conn->query("
    DELETE o FROM options o
    INNER JOIN questions q ON o.question_id = q.id
    WHERE q.quiz_id = $quiz_id
");

// 3. Xóa câu hỏi
$conn->query("DELETE FROM questions WHERE quiz_id = $quiz_id");

// 4. Xóa đề thi
$conn->query("DELETE FROM quizzes WHERE id = $quiz_id");

// Quay lại trang danh sách
header("Location: " . BASE_PATH . "/admin/index.php");
exit;
