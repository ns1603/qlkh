<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role'])) { header("Location: ../../index.php"); exit; }

if ($_SESSION['user_role'] == 'admins') {
    die("Báº¡n khÃ´ng cÃ³ quyá»n thá»±c hiá»n hÃ nh Äá»ng nÃ y!");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$quiz_id = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;
$role = $_SESSION['user_role'];
$user_id = $_SESSION['user_id'];

if ($id > 0) {
    // Check quyá»n trÆ°á»c khi xÃ³a
    if ($role == 'teacher') {
        $check_sql = "SELECT q.id FROM questions q 
                      JOIN quizzes qu ON q.quiz_id = qu.id 
                      JOIN courses c ON qu.course_id = c.id 
                      WHERE q.id = $id AND c.teacher_id = $user_id";
        $check_res = $conn->query($check_sql);
        if ($check_res->num_rows === 0) {
            die("â Báº¡n khÃ´ng cÃ³ quyá»n xÃ³a cÃ¢u há»i nÃ y!");
        }
    }
    
    $conn->query("DELETE FROM options WHERE question_id = $id");
    $conn->query("DELETE FROM questions WHERE id = $id");
    
    $_SESSION['status_message'] = "ÄÃ£ xÃ³a cÃ¢u há»i!";
}

header("Location: ListCauHoi.php?quiz_id=$quiz_id");
exit;
?>
