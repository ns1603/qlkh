<?php
session_start();
include 'config.php';

// ================== KIỂM TRA ĐĂNG NHẬP ==================
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$quiz_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($quiz_id <= 0) {
    die("ID bài thi không hợp lệ!");
}

// ================== KIỂM TRA ATTEMPT ==================
$check_attempt = $conn->query("
    SELECT status 
    FROM quiz_attempts 
    WHERE user_id = $user_id AND quiz_id = $quiz_id
");

if ($check_attempt->num_rows > 0) {
    $attempt = $check_attempt->fetch_assoc();

    if ($attempt['status'] === 'submitted') {
        echo "<script>
            alert('Bạn đã hoàn thành bài thi này. Không thể làm lại!');
            window.location.href='quiz_list.php';
        </script>";
        exit;
    } else {
        echo "<script>
            alert('Bài thi này đã được mở trước đó và sẽ được nộp tự động.');
            window.location.href='quiz_list.php';
        </script>";
        exit;
    }
}

// ================== LẤY THÔNG TIN ĐỀ ==================
$quiz_rs = $conn->query("SELECT * FROM quizzes WHERE id = $quiz_id");
$quiz = $quiz_rs->fetch_assoc();

if (!$quiz) {
    die("Đề thi không tồn tại!");
}

// ================== LẤY CÂU HỎI ==================
$q_rs = $conn->query("SELECT * FROM questions WHERE quiz_id = $quiz_id ORDER BY id ASC");
$questions = [];
while ($row = $q_rs->fetch_assoc()) {
    $questions[] = $row;
}

if (empty($questions)) {
    die("Đề thi chưa có câu hỏi!");
}

// ================== TẠO ATTEMPT (ĐÁNH DẤU BẮT ĐẦU) ==================
$conn->query("
    INSERT INTO quiz_attempts (user_id, quiz_id, start_time, status)
    VALUES ($user_id, $quiz_id, NOW(), 'doing')
");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Làm bài: <?= htmlspecialchars($quiz['title']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f7fa;
            user-select: none;
        }
        .quiz-container {
            max-width: 850px;
            margin: 30px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,.08);
        }
        .question-box {
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        .question-title {
            font-weight: 600;
            margin-bottom: 15px;
        }
        .option-label {
            display: block;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 8px;
            cursor: pointer;
        }
        .option-label:hover {
            background: #f8f9fa;
        }
        .timer-bar {
            position: sticky;
            top: 0;
            z-index: 999;
            background: #212529;
            color: #fff;
            padding: 12px;
            font-weight: 600;
        }
        .timer-warning {
            background: #dc3545 !important;
        }
    </style>
</head>

<body oncontextmenu="return false">

<div id="timerBar" class="timer-bar">
    <div class="container d-flex justify-content-between">
        <span><?= htmlspecialchars($quiz['title']) ?></span>
        <span>Còn lại: <span id="timer" class="text-warning">00:00</span></span>
    </div>
</div>

<div class="quiz-container">
    <div class="alert alert-warning">
        ⚠ Không reload / thoát tab. Hệ thống sẽ tự động nộp bài.
    </div>

    <form id="quizForm" action="submit_quiz.php" method="POST">
        <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">

        <?php foreach ($questions as $i => $q): ?>
            <div class="question-box">
                <div class="question-title">
                    Câu <?= $i + 1 ?>: <?= htmlspecialchars($q['question_text']) ?>
                </div>

                <?php foreach (['A','B','C','D'] as $opt): ?>
                    <label class="option-label">
                        <input type="radio" name="answer[<?= $q['id'] ?>]" value="<?= $opt ?>" class="me-2">
                        <?= $opt ?>. <?= htmlspecialchars($q['option_' . strtolower($opt)]) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <div class="text-center">
            <button type="button" class="btn btn-primary btn-lg px-5" onclick="submitQuiz()">
                Nộp bài
            </button>
        </div>
    </form>
</div>

<script>
let timeLeft = <?= (int)$quiz['time_limit'] * 60 ?>;
let submitted = false;

const timer = document.getElementById('timer');
const bar = document.getElementById('timerBar');

const interval = setInterval(() => {
    let m = Math.floor(timeLeft / 60);
    let s = timeLeft % 60;
    timer.textContent = `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;

    if (timeLeft <= 60) bar.classList.add('timer-warning');

    if (timeLeft <= 0) {
        clearInterval(interval);
        autoSubmit();
    }
    timeLeft--;
}, 1000);

function submitQuiz() {
    if (confirm("Bạn chắc chắn nộp bài?")) {
        submitted = true;
        window.onbeforeunload = null;
        document.getElementById('quizForm').submit();
    }
}

// AUTO SUBMIT KHI THOÁT
function autoSubmit() {
    if (submitted) return;
    submitted = true;

    const form = document.getElementById('quizForm');
    const data = new FormData(form);
    navigator.sendBeacon('submit_quiz.php', data);
}

window.addEventListener('beforeunload', autoSubmit);
document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'hidden') autoSubmit();
});
</script>

</body>
</html>
