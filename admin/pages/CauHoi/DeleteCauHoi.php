<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role'])) { header("Location: ../../index.php"); exit; }

if ($_SESSION['user_role'] == 'admins') {
    die("Bạn không có quyền thực hiện hành động này!");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$quiz_id = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;
$role = $_SESSION['user_role'];
$user_id = $_SESSION['user_id'];

if ($id > 0) {
    // Check quyền trước khi xóa
    if ($role == 'teacher') {
        $check_sql = "SELECT q.id FROM questions q 
                      JOIN quizzes qu ON q.quiz_id = qu.id 
                      JOIN courses c ON qu.course_id = c.id 
                      WHERE q.id = $id AND c.teacher_id = $user_id";
        $check_res = $conn->query($check_sql);
        if ($check_res->num_rows === 0) {
            die("❌ Bạn không có quyền xóa câu hỏi này!");
        }
    }
    
    $conn->query("DELETE FROM options WHERE question_id = $id");
    $conn->query("DELETE FROM questions WHERE id = $id");
    
    $_SESSION['status_message'] = "Đã xóa câu hỏi!";
}

header("Location: ListCauHoi.php?quiz_id=$quiz_id");
exit;
?>
