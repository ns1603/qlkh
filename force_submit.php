<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) exit;

$user_id = $_SESSION['user_id'];
$quiz_id = intval($_POST['quiz_id'] ?? 0);

// Lấy attempt đang làm
$attempt = $conn->query("
    SELECT * FROM quiz_attempts 
    WHERE user_id = $user_id 
      AND quiz_id = $quiz_id 
      AND status = 'doing'
")->fetch_assoc();

if (!$attempt) exit;


$conn->query("
    UPDATE quiz_attempts 
    SET status = 'submitted', submit_time = NOW()
    WHERE id = {$attempt['id']}
");
