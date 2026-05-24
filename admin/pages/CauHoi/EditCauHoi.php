<?php
session_start();
include(__DIR__ . '/../../../config.php');

// Check quyá»n
if (!isset($_SESSION['user_role'])) { header("Location: ../../index.php"); exit; }

if ($_SESSION['user_role'] == 'admins') {
    die("Báº¡n khÃ´ng cÃ³ quyá»n thá»±c hiá»n hÃ nh Äá»ng nÃ y!");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$quiz_id = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;
$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];

// 1. Láº¤Y Dá»® LIá»U CÃU Há»I & CHECK QUYá»N
$q_sql = "SELECT q.*, qu.course_id, c.teacher_id 
          FROM questions q 
          JOIN quizzes qu ON q.quiz_id = qu.id 
          JOIN courses c ON qu.course_id = c.id 
          WHERE q.id = $id";
$q_stmt = $conn->query($q_sql);
$question = $q_stmt->fetch_assoc();

if (!$question) die("CÃ¢u há»i khÃ´ng tá»n táº¡i!");

if ($role == 'teacher' && $question['teacher_id'] != $user_id) {
    die("â Báº¡n khÃ´ng cÃ³ quyá»n chá»nh sá»­a cÃ¢u há»i nÃ y!");
}

// 2. Xá»¬ LÃ Cáº¬P NHáº¬T
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $question_text  = trim($_POST['question_text']);
    $option_a       = trim($_POST['option_a']);
    $option_b       = trim($_POST['option_b']);
    $option_c       = trim($_POST['option_c']);
    $option_d       = trim($_POST['option_d']);
    $correct_answer = $_POST['correct_answer']; // A, B, C, D
    $explanation    = trim($_POST['explanation']);
    $level          = $_POST['level']; // easy, medium, hard

    // Cáº­p nháº­t vÃ o DB
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
        $_SESSION['status_message'] = "Cáº­p nháº­t cÃ¢u há»i thÃ nh cÃ´ng!";
        header("Location: ListCauHoi.php?quiz_id=$quiz_id");
        exit;
    } else {
        $error = "Lá»i cáº­p nháº­t: " . $conn->error;
    }
}
?>

<?php 
include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/header.php"; 
include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/navbar.php"; 
?>

<div class="container-fluid page-body-wrapper">
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/sidebar.php"; ?>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title"> Sá»­a CÃ¢u há»i </h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="ListCauHoi.php?quiz_id=<?= $quiz_id ?>">Quay láº¡i danh sÃ¡ch</a></li>
                        <li class="breadcrumb-item active">Sá»­a</li>
                    </ol>
                </nav>
            </div>

            <div class="row">
                <div class="col-md-10 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title text-primary mb-4">Chá»nh sá»­a ná»i dung</h4>
                            
                            <?php if(isset($error)): ?>
                                <div class="alert alert-danger"><?= $error ?></div>
                            <?php endif; ?>

                            <form class="forms-sample" method="POST">
                                
                                <div class="form-group">
                                    <label class="font-weight-bold">Ná»i dung cÃ¢u há»i <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="question_text" rows="3" required><?= htmlspecialchars($question['question_text']) ?></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Má»©c Äá» khÃ³</label>
                                            <select class="form-select" name="level">
                                                <option value="easy" <?= $question['level']=='easy'?'selected':'' ?>>Dá» (Easy)</option>
                                                <option value="medium" <?= $question['level']=='medium'?'selected':'' ?>>Trung bÃ¬nh (Medium)</option>
                                                <option value="hard" <?= $question['level']=='hard'?'selected':'' ?>>KhÃ³ (Hard)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <label class="font-weight-bold mb-3">CÃ¡c phÆ°Æ¡ng Ã¡n tráº£ lá»i:</label>

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
                                    <label class="font-weight-bold">Giáº£i thÃ­ch ÄÃ¡p Ã¡n (Optional)</label>
                                    <textarea class="form-control" name="explanation" rows="2" placeholder="Nháº­p lá»i giáº£i thÃ­ch náº¿u cÃ³..."><?= htmlspecialchars($question['explanation']) ?></textarea>
                                </div>

                                <button type="submit" class="btn btn-gradient-warning me-2">
                                    <i class="mdi mdi-content-save"></i> Cáº­p nháº­t
                                </button>
                                <a href="ListCauHoi.php?quiz_id=<?= $quiz_id ?>" class="btn btn-light">Há»§y</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/footer.php"; ?>
    </div>
</div>
