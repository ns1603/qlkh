<?php
session_start();

// 🔥 BẮT BUỘC PHẢI CÓ
require_once $_SERVER['DOCUMENT_ROOT'] . '/Learning/config.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$quiz_id = intval($_GET['id']);

// 1. Xóa kết quả làm bài
$conn->query("DELETE FROM quiz_results WHERE quiz_id = $quiz_id");

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
header("Location: /Learning/admin/index.php");
exit;
