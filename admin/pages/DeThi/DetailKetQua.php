<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_id']) || 
   ($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'admins' && $_SESSION['user_role'] != 'teacher')) {
    die("Truy cập bị từ chối!");
}

$result_id = isset($_GET['id']) ? intval($_GET['id']) : 0;


$sql_info = "SELECT er.*, u.fullname, u.email, q.title as quiz_title, q.id as quiz_id, c.teacher_id
             FROM exam_results er
             JOIN users u ON er.user_id = u.id
             JOIN quizzes q ON er.quiz_id = q.id
             JOIN courses c ON q.course_id = c.id
             WHERE er.id = $result_id";

$info = $conn->query($sql_info)->fetch_assoc();

if (!$info) {
    die("Không tìm thấy kết quả thi này!");
}

// Check quyền giảng viên
if ($_SESSION['user_role'] == 'teacher' && $info['teacher_id'] != $_SESSION['user_id']) {
    die("❌ Bạn không có quyền xem chi tiết kết quả này!");
}

$user_answers = json_decode($info['answers'], true);

$quiz_id = $info['quiz_id'];
$sql_questions = "SELECT * FROM questions WHERE quiz_id = $quiz_id ORDER BY id ASC";
$questions = $conn->query($sql_questions);

?>

<?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/header.php"; ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/navbar.php"; ?>

<div class="container-fluid page-body-wrapper">
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/sidebar.php"; ?>
    <div class="main-panel">
        <div class="content-wrapper">
            
            <div class="page-header">
                <h3 class="page-title"> Chi tiết bài làm </h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="ViewDiemThi.php?id=<?= $quiz_id ?>">Danh sách lớp</a></li>
                        <li class="breadcrumb-item active">Chi tiết</li>
                    </ol>
                </nav>
            </div>

            <div class="row">
                <div class="col-12 grid-margin stretch-card">
                    <div class="card bg-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="card-title mb-1">Học viên: <?= htmlspecialchars($info['fullname']) ?></h4>
                                    <p class="text-muted mb-0"><?= htmlspecialchars($info['email']) ?></p>
                                    <p class="text-muted mt-2">Đề thi: <strong><?= htmlspecialchars($info['quiz_title']) ?></strong></p>
                                </div>
                                <div class="text-right text-center">
                                    <h2 class="display-4 font-weight-bold text-primary mb-0">
                                        <?= floatval($info['score']) ?>
                                    </h2>
                                    <span class="badge badge-outline-primary">Điểm số (Thang 10)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <?php 
                    $stt = 1;
                    if ($questions->num_rows > 0):
                        while($q = $questions->fetch_assoc()):
                            $qid = $q['id'];
                            $correct_ans = strtoupper(trim($q['correct_answer']));
                            
                            $user_ans = isset($user_answers[$qid]) ? strtoupper(trim($user_answers[$qid])) : null;
                            $is_correct = ($user_ans === $correct_ans);
                            $card_border = $is_correct ? 'border-success' : 'border-danger';
                            $icon_status = $is_correct ? '<i class="mdi mdi-check-circle text-success"></i>' : '<i class="mdi mdi-close-circle text-danger"></i>';
                    ?>
                    
                    <div class="card mb-3 <?= $card_border ?>" style="border-left: 5px solid;">
                        <div class="card-body">
                            <h5 class="card-title">
                                Câu <?= $stt++ ?>: <?= $icon_status ?> 
                                <span style="font-size: 1rem; line-height: 1.5;"><?= htmlspecialchars($q['question_text']) ?></span>
                            </h5>
                            
                            <div class="row mt-3">
                                <?php 
                                    $options = [
                                        'A' => $q['option_a'],
                                        'B' => $q['option_b'],
                                        'C' => $q['option_c'],
                                        'D' => $q['option_d']
                                    ];
                                ?>
                                
                                <?php foreach($options as $key => $val): ?>
                                    <?php 
                                      
                                        $style = "background: #f8f9fa; border: 1px solid #ddd;"; // Mặc định
                                        $icon = "";

                                        if ($key === $correct_ans) {
                                            $style = "background: #d1e7dd; border: 1px solid #198754; color: #0f5132; font-weight: bold;";
                                            $icon = "<i class='mdi mdi-check'></i>";
                                        }

                                        if ($key === $user_ans && !$is_correct) {
                                            $style = "background: #f8d7da; border: 1px solid #dc3545; color: #842029; font-weight: bold;";
                                            $icon = "<i class='mdi mdi-close'></i> (Đã chọn)";
                                        }
                                        if ($key === $user_ans && $is_correct) {
                                             $icon = "<i class='mdi mdi-check-all'></i> (Bạn chọn đúng)";
                                        }
                                    ?>
                                    
                                    <div class="col-md-6 mb-2">
                                        <div class="p-2 rounded" style="<?= $style ?>">
                                            <strong><?= $key ?>.</strong> <?= htmlspecialchars($val) ?> <?= $icon ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <?php if (!$is_correct && !empty($q['explanation'])): ?>
                                <div class="alert alert-warning mt-3 mb-0">
                                    <i class="mdi mdi-lightbulb-on"></i> <strong>Giải thích:</strong> 
                                    <?= htmlspecialchars($q['explanation']) ?>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>

                    <?php endwhile; ?>
                    <?php else: ?>
                        <div class="alert alert-info">Đề thi này không còn câu hỏi (có thể đã bị xóa).</div>
                    <?php endif; ?>
                </div>

                <div class="col-12 mt-3">
                    <a href="ListDiemThi.php?id=<?= $quiz_id ?>" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left"></i> Quay lại danh sách lớp
                    </a>
                </div>

            </div>
        </div>
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/footer.php"; ?>
    </div>
</div>
