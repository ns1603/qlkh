<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: quiz_list.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$quiz_id = isset($_POST['quiz_id']) ? (int) $_POST['quiz_id'] : 0;
$user_answers = isset($_POST['answer']) ? $_POST['answer'] : [];

if ($quiz_id <= 0) {
    die("Dữ liệu bài thi không hợp lệ!");
}

$attempt_rs = $conn->query("
    SELECT id, status 
    FROM quiz_attempts 
    WHERE user_id = $user_id AND quiz_id = $quiz_id
    LIMIT 1
");

if ($attempt_rs->num_rows === 0) {
    die("Không tìm thấy lượt làm bài!");
}

$attempt = $attempt_rs->fetch_assoc();

if ($attempt['status'] === 'submitted') {
    echo "<script>
        alert('Bài thi đã được nộp trước đó!');
        window.location.href='quiz_list.php';
    </script>";
    exit;
}

$attempt_id = (int) $attempt['id'];


$q_rs = $conn->query("
    SELECT id, correct_answer 
    FROM questions 
    WHERE quiz_id = $quiz_id
");

$total_questions = $q_rs->num_rows;
$correct_count = 0;

while ($q = $q_rs->fetch_assoc()) {
    $qid = $q['id'];
    $correct = strtoupper(trim($q['correct_answer']));

    if (isset($user_answers[$qid])) {
        $user_choice = strtoupper(trim($user_answers[$qid]));
        if ($user_choice === $correct) {
            $correct_count++;
        }
    }
}

$score = 0;
if ($total_questions > 0) {
    $score = round(($correct_count / $total_questions) * 10, 2);
}

$answers_json = json_encode($user_answers, JSON_UNESCAPED_UNICODE);

$conn->begin_transaction();

try {
    // 1. Update attempt
    $stmt1 = $conn->prepare("
        UPDATE quiz_attempts 
        SET submit_time = NOW(), status = 'submitted' 
        WHERE id = ?
    ");
    $stmt1->bind_param("i", $attempt_id);
    $stmt1->execute();

    // 2. Insert result
    $stmt2 = $conn->prepare("
        INSERT INTO quiz_results 
        (user_id, quiz_id, attempt_id, score, correct_count, total_questions, answers, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt2->bind_param(
        "iiidiis",
        $user_id,
        $quiz_id,
        $attempt_id,
        $score,
        $correct_count,
        $total_questions,
        $answers_json
    );
    $stmt2->execute();

    $result_id = $conn->insert_id;

    $conn->commit();

    header("Location: view_result.php?id=$result_id");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    die("Lỗi hệ thống: " . $e->getMessage());
}
