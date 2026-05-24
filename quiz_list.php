<?php
session_start();
include 'config.php';

// Check đăng nhập
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
$user_id = $_SESSION['user_id'];

// Lấy danh sách đề thi mà học viên được phép thấy (dựa trên khóa học đã đăng ký)
$sql = "SELECT q.*, c.title as course_name 
        FROM quizzes q
        JOIN courses c ON q.course_id = c.id
        JOIN enrollments e ON c.id = e.course_id
        WHERE e.user_id = ?
        ORDER BY q.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php include 'header.php'; ?>

<style>
    /* ====== BREADCRUMB ====== */
    .breadcrumbs-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 90px 0;
        text-align: center;
    }
    .breadcrumbs-custom-title {
        font-size: 3rem;
        font-weight: 800;
        color: #fff;
    }
    .breadcrumbs-custom-path li,
    .breadcrumbs-custom-path a {
        color: rgba(255,255,255,0.85);
    }

    /* ====== QUIZ CARD ====== */
    .quiz-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 28px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        transition: all 0.35s ease;
        height: 100%;
    }
    .quiz-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 18px 45px rgba(102, 126, 234, 0.25);
    }

    .quiz-card-header {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 20px;
    }
    .quiz-card-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: #2c3e50;
    }

    /* ====== BADGE ====== */
    .quiz-status-badge {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        white-space: nowrap;
    }
    .badge-completed {
        background: linear-gradient(135deg, #11998e, #38ef7d);
        color: #fff;
    }
    .badge-pending {
        background: linear-gradient(135deg, #fbc531, #e1b12c);
        color: #000;
    }

    /* ====== INFO ====== */
    .quiz-card-body {
        display: grid;
        grid-template-columns: 1fr;
        gap: 15px;
        margin-bottom: 20px;
    }
    .quiz-info-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px;
        background: #f4f6fb;
        border-radius: 14px;
    }
    .quiz-info-item i {
        font-size: 1.4rem;
        color: #667eea;
    }
    .quiz-info-item-label {
        font-size: 0.8rem;
        color: #6c757d;
    }
    .quiz-info-item-value {
        font-size: 1rem;
        font-weight: 700;
        color: #2c3e50;
    }

    /* ====== FOOTER ====== */
    .quiz-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* ====== SCORE ====== */
    .quiz-score {
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .quiz-score.done {
        color: #11998e;
    }
    .quiz-score.ready {
        color: #f39c12;
    }

    /* ====== BUTTON ====== */
    .button-primary-gradient {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: #fff;
        padding: 12px 26px;
        border-radius: 30px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }
    .button-primary-gradient:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.5);
        color: #fff;
    }

    /* ====== DISABLED BUTTON ====== */
    .btn-secondary {
        background: #adb5bd;
        border-radius: 30px;
        padding: 12px 26px;
        font-weight: 700;
        cursor: not-allowed;
        color: #fff;
    }

</style>

<div class="breadcrumbs-custom">
    <div class="container">
        <h1 class="breadcrumbs-custom-title">Danh sách Bài kiểm tra</h1>
        <ul class="breadcrumbs-custom-path">
            <li><a href="home.php">Trang chủ</a></li>
            <li class="active">Bài kiểm tra</li>
        </ul>
    </div>
</div>

<section class="section section-lg bg-default" style="padding: 60px 0;">
    <div class="container">
        <?php if($result->num_rows > 0): ?>
            <div class="row">
                <?php while($row = $result->fetch_assoc()): 
                    $q_id = $row['id'];
                    
                    $check_sql = "SELECT score FROM exam_results WHERE user_id = ? AND quiz_id = ?";
                    $check_stmt = $conn->prepare($check_sql);
                    $check_stmt->bind_param("ii", $user_id, $q_id);
                    $check_stmt->execute();
                    $check_res = $check_stmt->get_result();
                    $done = $check_res->fetch_assoc(); // Nếu có dữ liệu nghĩa là đã làm rồi
                ?>
                <div class="col-md-6 col-lg-4" style="margin-bottom: 30px;">
                    <div class="quiz-card wow fadeInUp">
                        <div class="quiz-card-header">
                            <h3 class="quiz-card-title">
                                <i class="mdi mdi-clipboard-text-outline"></i> <?= htmlspecialchars($row['title']) ?>
                            </h3>
                            <span class="quiz-status-badge <?= $done ? 'badge-completed' : 'badge-pending' ?>">
                                <?= $done ? '✓ Đã hoàn thành' : '⏳ Chưa làm' ?>
                            </span>
                        </div>
                        
                        <div class="quiz-card-body">
                            <div class="quiz-info-item">
                                <i class="mdi mdi-book-open-variant"></i>
                                <div class="quiz-info-item-content">
                                    <div class="quiz-info-item-label">Khóa học</div>
                                    <div class="quiz-info-item-value"><?= htmlspecialchars($row['course_name']) ?></div>
                                </div>
                            </div>
                            
                            <div class="quiz-info-item">
                                <i class="mdi mdi-clock-outline"></i>
                                <div class="quiz-info-item-content">
                                    <div class="quiz-info-item-label">Thời gian</div>
                                    <div class="quiz-info-item-value"><?= $row['time_limit'] ?> phút</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="quiz-card-footer">
                            <?php if($done): ?>
                                <div class="quiz-score done">
                                    <i class="mdi mdi-trophy"></i>
                                    <span><?= floatval($done['score']) ?> / 10 điểm</span>
                                </div>
                                <button class="btn btn-secondary" disabled title="Bạn chỉ được làm bài 1 lần">
                                    <i class="mdi mdi-lock"></i> Đã nộp bài
                                </button>
                            <?php else: ?>
                                <div class="quiz-score ready" style="color: #f39c12; font-size: 1rem;">
                                    <i class="mdi mdi-alert-circle-outline"></i>
                                    <span>Sẵn sàng</span>
                                </div>
                                <a href="take_quiz.php?id=<?= $row['id'] ?>" 
                                    style="display: inline-flex; 
                                            align-items: center;
                                            justify-content: center;
                                            background: linear-gradient(45deg, #11998e, #38ef7d); /* Màu nền Gradient */
                                            border: none;
                                            border-radius: 50px;
                                            padding: 12px 30px;
                                            color: white;              /* Chữ màu trắng */
                                            text-decoration: none;
                                            font-weight: bold;
                                            box-shadow: 0 4px 15px rgba(0,0,0,0.2);"> <span>Vào thi ngay <i class="mdi mdi-play-circle-outline"></i></span>
                                    </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <span class="empty-state-icon">📝</span>
                <h4>Bạn chưa có bài kiểm tra nào</h4>
                <p>Hãy đăng ký khóa học để có thể làm bài kiểm tra</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'footer.php'; ?>