<?php
session_start();

// ð¥ Báº®T BUá»C PHáº¢I CÃ
require_once ROOT_PATH . '/config.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] == 'admins') {
    die("Báº¡n khÃ´ng cÃ³ quyá»n thá»±c hiá»n hÃ nh Äá»ng nÃ y!");
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$quiz_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];

// 0. Kiá»m tra quyá»n sá» há»¯u Äá»i vá»i Giáº£ng viÃªn
if ($role == 'teacher') {
    $check_sql = "SELECT q.id FROM quizzes q 
                  JOIN courses c ON q.course_id = c.id 
                  WHERE q.id = $quiz_id AND c.teacher_id = $user_id";
    $check_res = $conn->query($check_sql);
    if ($check_res->num_rows === 0) {
        die("â Báº¡n khÃ´ng cÃ³ quyá»n xÃ³a Äá» thi nÃ y!");
    }
}

// 1. Xóa káº¿t quáº£ lÃ m bÃ i
$conn->query("DELETE FROM quiz_results WHERE quiz_id = $quiz_id");
$conn->query("DELETE FROM exam_results WHERE quiz_id = $quiz_id");
$conn->query("DELETE FROM quiz_attempts WHERE quiz_id = $quiz_id");

// 2. Xóa ÄÃ¡p Ã¡n
$conn->query("
    DELETE o FROM options o
    INNER JOIN questions q ON o.question_id = q.id
    WHERE q.quiz_id = $quiz_id
");

// 3. Xóa cÃ¢u há»i
$conn->query("DELETE FROM questions WHERE quiz_id = $quiz_id");

// 4. Xóa Äá» thi
$conn->query("DELETE FROM quizzes WHERE id = $quiz_id");

// Quay láº¡i trang danh sÃ¡ch
header("Location: " . BASE_PATH . "/admin/index.php");
exit;
