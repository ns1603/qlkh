<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role'])) { header("Location: ../../index.php"); exit; }

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$quiz_id = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;

if ($id > 0) {
    
    $conn->query("DELETE FROM options WHERE question_id = $id");
    $conn->query("DELETE FROM questions WHERE id = $id");
    
    $_SESSION['status_message'] = "Đã xóa câu hỏi!";
}

header("Location: ListCauHoi.php?quiz_id=$quiz_id");
exit;
?>