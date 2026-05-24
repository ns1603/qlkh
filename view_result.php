<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$result_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];

// 1. LẤY KẾT QUẢ THI
// Chỉ cho phép xem nếu là bài của chính mình (hoặc là admin/teacher)
$sql_res = "SELECT er.*, q.title as quiz_title, q.id as quiz_id 
            FROM exam_results er 
            JOIN quizzes q ON er.quiz_id = q.id 
            WHERE er.id = $result_id AND er.user_id = $user_id";

$res = $conn->query($sql_res);
$exam_result = $res->fetch_assoc();

if (!$exam_result) {
    die("Kết quả không tồn tại hoặc bạn không có quyền xem.");
}

// Giải mã JSON đáp án người dùng đã chọn
$user_answers = json_decode($exam_result['answers'], true); 

// 2. LẤY CHI TIẾT CÂU HỎI VÀ ĐÁP ÁN ĐÚNG ĐỂ HIỂN THỊ
$quiz_id = $exam_result['quiz_id'];
$sql_questions = "SELECT * FROM questions WHERE quiz_id = $quiz_id ORDER BY id ASC";
$questions = $conn->query($sql_questions);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kết quả thi: <?= htmlspecialchars($exam_result['quiz_title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .correct-answer { color: #198754; font-weight: bold; }
        .wrong-answer { color: #dc3545; text-decoration: line-through; }
        .score-box { background: #f8f9fa; padding: 20px; border-radius: 10px; text-align: center; margin-bottom: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .score-number { font-size: 3rem; font-weight: bold; color: #0d6efd; }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                
                <div class="score-box">
                    <h3>Kết quả bài thi: <?= htmlspecialchars($exam_result['quiz_title']) ?></h3>
                    <div class="score-number"><?= $exam_result['score'] ?> / 10</div>
                    <p class="text-muted">Ngày nộp: <?= date('d/m/Y H:i', strtotime($exam_result['created_at'])) ?></p>
                    <a href="home.php" class="btn btn-secondary">Quay về trang chủ</a>
                </div>

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Chi tiết bài làm</h5>
                    </div>
                    <div class="card-body">
                        <?php 
                        $i = 1;
                        while($q = $questions->fetch_assoc()): 
                            $qid = $q['id'];
                            $user_choice = isset($user_answers[$qid]) ? $user_answers[$qid] : 'Chưa chọn';
                            $correct = $q['correct_answer'];
                            
                            // Kiểm tra đúng sai
                            $is_correct = ($user_choice === $correct);
                            $bg_class = $is_correct ? 'alert-success' : 'alert-danger';
                            $icon = $is_correct ? '✅' : '❌';
                        ?>
                            <div class="alert <?= $bg_class ?> mb-3">
                                <strong>Câu <?= $i++ ?>: <?= htmlspecialchars($q['question_text']) ?></strong>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        Bạn chọn: <strong><?= $user_choice ?></strong> <?= $icon ?>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        Đáp án đúng: <strong><?= $correct ?></strong>
                                    </div>
                                </div>
                                
                                <?php if (!$is_correct && !empty($q['explanation'])): ?>
                                    <div class="mt-2 text-muted fst-italic">
                                        <small>💡 Giải thích: <?= htmlspecialchars($q['explanation']) ?></small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
</html>