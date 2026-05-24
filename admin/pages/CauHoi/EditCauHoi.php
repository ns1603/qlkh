<?php
session_start();
include(__DIR__ . '/../../../config.php');

// Check quyền
if (!isset($_SESSION['user_role'])) { header("Location: ../../index.php"); exit; }

if ($_SESSION['user_role'] == 'admins') {
    die("Bạn không có quyền thực hiện hành động này!");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$quiz_id = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;
$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];

// 1. LẤY DỮ LIỆU CÂU HỎI & CHECK QUYỀN
$q_sql = "SELECT q.*, qu.course_id, c.teacher_id 
          FROM questions q 
          JOIN quizzes qu ON q.quiz_id = qu.id 
          JOIN courses c ON qu.course_id = c.id 
          WHERE q.id = $id";
$q_stmt = $conn->query($q_sql);
$question = $q_stmt->fetch_assoc();

if (!$question) die("Câu hỏi không tồn tại!");

if ($role == 'teacher' && $question['teacher_id'] != $user_id) {
    die("❌ Bạn không có quyền chỉnh sửa câu hỏi này!");
}

// 2. XỬ LÝ CẬP NHẬT
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $question_text  = trim($_POST['question_text']);
    $option_a       = trim($_POST['option_a']);
    $option_b       = trim($_POST['option_b']);
    $option_c       = trim($_POST['option_c']);
    $option_d       = trim($_POST['option_d']);
    $correct_answer = $_POST['correct_answer']; // A, B, C, D
    $explanation    = trim($_POST['explanation']);
    $level          = $_POST['level']; // easy, medium, hard

    // Cập nhật vào DB
    $sql = "UPDATE questions SET 
            question_text = ?, 
            option_a = ?, option_b = ?, option_c = ?, option_d = ?, 
            correct_answer = ?, explanation = ?, level = ? 
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssi", 
        $question_text, 
        $option_a, $option_b, $option_c, $option_d, 
        $correct_answer, $explanation, $level, 
        $id
    );

    if ($stmt->execute()) {
        $_SESSION['status_message'] = "Cập nhật câu hỏi thành công!";
        header("Location: ListCauHoi.php?quiz_id=$quiz_id");
        exit;
    } else {
        $error = "Lỗi cập nhật: " . $conn->error;
    }
}
?>

<?php 
include ROOT_PATH . "/admin/header.php"; 
include ROOT_PATH . "/admin/navbar.php"; 
?>

<div class="container-fluid page-body-wrapper">
    <?php include ROOT_PATH . "/admin/sidebar.php"; ?>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title"> Sửa Câu hỏi </h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="ListCauHoi.php?quiz_id=<?= $quiz_id ?>">Quay lại danh sách</a></li>
                        <li class="breadcrumb-item active">Sửa</li>
                    </ol>
                </nav>
            </div>

            <div class="row">
                <div class="col-md-10 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title text-primary mb-4">Chỉnh sửa nội dung</h4>
                            
                            <?php if(isset($error)): ?>
                                <div class="alert alert-danger"><?= $error ?></div>
                            <?php endif; ?>

                            <form class="forms-sample" method="POST">
                                
                                <div class="form-group">
                                    <label class="font-weight-bold">Nội dung câu hỏi <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="question_text" rows="3" required><?= htmlspecialchars($question['question_text']) ?></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Mức độ khó</label>
                                            <select class="form-select" name="level">
                                                <option value="easy" <?= $question['level']=='easy'?'selected':'' ?>>Dễ (Easy)</option>
                                                <option value="medium" <?= $question['level']=='medium'?'selected':'' ?>>Trung bình (Medium)</option>
                                                <option value="hard" <?= $question['level']=='hard'?'selected':'' ?>>Khó (Hard)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <label class="font-weight-bold mb-3">Các phương án trả lời:</label>

                                <div class="input-group mb-3">
                                    <div class="input-group-text bg-light">
                                        <input class="form-check-input mt-0" type="radio" name="correct_answer" value="A" <?= $question['correct_answer']=='A'?'checked':'' ?> required>
                                        &nbsp; <strong class="text-primary">A</strong>
                                    </div>
                                    <input type="text" class="form-control" name="option_a" value="<?= htmlspecialchars($question['option_a']) ?>" required>
                                </div>

                                <div class="input-group mb-3">
                                    <div class="input-group-text bg-light">
                                        <input class="form-check-input mt-0" type="radio" name="correct_answer" value="B" <?= $question['correct_answer']=='B'?'checked':'' ?> required>
                                        &nbsp; <strong class="text-primary">B</strong>
                                    </div>
                                    <input type="text" class="form-control" name="option_b" value="<?= htmlspecialchars($question['option_b']) ?>" required>
                                </div>

                                <div class="input-group mb-3">
                                    <div class="input-group-text bg-light">
                                        <input class="form-check-input mt-0" type="radio" name="correct_answer" value="C" <?= $question['correct_answer']=='C'?'checked':'' ?> required>
                                        &nbsp; <strong class="text-primary">C</strong>
                                    </div>
                                    <input type="text" class="form-control" name="option_c" value="<?= htmlspecialchars($question['option_c']) ?>" required>
                                </div>

                                <div class="input-group mb-3">
                                    <div class="input-group-text bg-light">
                                        <input class="form-check-input mt-0" type="radio" name="correct_answer" value="D" <?= $question['correct_answer']=='D'?'checked':'' ?> required>
                                        &nbsp; <strong class="text-primary">D</strong>
                                    </div>
                                    <input type="text" class="form-control" name="option_d" value="<?= htmlspecialchars($question['option_d']) ?>" required>
                                </div>

                                <hr>

                                <div class="form-group mt-3">
                                    <label class="font-weight-bold">Giải thích đáp án (Optional)</label>
                                    <textarea class="form-control" name="explanation" rows="2" placeholder="Nhập lời giải thích nếu có..."><?= htmlspecialchars($question['explanation']) ?></textarea>
                                </div>

                                <button type="submit" class="btn btn-gradient-warning me-2">
                                    <i class="mdi mdi-content-save"></i> Cập nhật
                                </button>
                                <a href="ListCauHoi.php?quiz_id=<?= $quiz_id ?>" class="btn btn-light">Hủy</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include ROOT_PATH . "/admin/footer.php"; ?>
    </div>
</div>
