<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role'])) {
    header("Location: ../../index.php");
    exit;
}

if ($_SESSION['user_role'] == 'admins') {
    die("Báº¡n khÃ´ng cÃ³ quyá»n thá»±c hiá»n hÃ nh Äá»ng nÃ y!");
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];
$error = '';

$quiz_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($quiz_id <= 0) {
    header("Location: ListDeThi.php");
    exit;
}

/* =======================
   Láº¤Y THÃNG TIN Äá» THI
======================= */
$sql_quiz = "SELECT q.*, c.teacher_id 
             FROM quizzes q 
             JOIN courses c ON q.course_id = c.id 
             WHERE q.id = $quiz_id";
$quiz = $conn->query($sql_quiz)->fetch_assoc();
if (!$quiz) {
    die("â Äá» thi khÃ´ng tá»n táº¡i");
}

/* =======================
   KIá»M TRA QUYá»N Sá» Há»®U
======================= */
if ($role == 'teacher' && $quiz['teacher_id'] != $user_id) {
    die("â Báº¡n khÃ´ng cÃ³ quyá»n chá»nh sá»­a Äá» thi nÃ y!");
}

/* =======================
   Láº¤Y DANH SÃCH KHÃA Há»C
======================= */
$sql_courses = "SELECT id, title FROM courses";
if ($role == 'teacher') {
    $sql_courses .= " WHERE teacher_id = $user_id";
}
$courses = $conn->query($sql_courses);

/* =======================
   Xá»¬ LÃ SUBMIT
======================= */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_id  = intval($_POST['course_id']);
    $title      = trim($_POST['title']);
    $time_limit = intval($_POST['time_limit']);

    if ($course_id <= 0 || $title === '') {
        $error = "Vui lÃ²ng nháº­p Äáº§y Äá»§ thÃ´ng tin!";
    } elseif ($time_limit <= 0) {
        $error = "Thá»i gian lÃ m bÃ i pháº£i lá»n hÆ¡n 0!";
    } else {

        // 1. Update thÃ´ng tin Äá» thi
        $stmt = $conn->prepare("
            UPDATE quizzes 
            SET course_id = ?, title = ?, time_limit = ?
            WHERE id = ?
        ");
        $stmt->bind_param("isii", $course_id, $title, $time_limit, $quiz_id);

        if ($stmt->execute()) {

            // 2. Import CSV (náº¿u cÃ³)
            if (isset($_FILES['quiz_file']) && $_FILES['quiz_file']['error'] === UPLOAD_ERR_OK) {
                require_once __DIR__ . '/ImportQuizHelper.php';
                $imported = import_quiz_from_csv(
                    $conn,
                    $quiz_id,
                    $_FILES['quiz_file']['tmp_name']
                );

                $_SESSION['status_message'] =
                    "âï¸ Cáº­p nháº­t Äá» thi thÃ nh cÃ´ng. Import thÃªm $imported cÃ¢u há»i.";
            } else {
                $_SESSION['status_message'] = "âï¸ Cáº­p nháº­t Äá» thi thÃ nh cÃ´ng.";
            }

            header("Location: ListDeThi.php");
            exit;
        } else {
            $error = "Lá»i cáº­p nháº­t: " . $conn->error;
        }
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
    <h3 class="page-title">Chá»nh sá»­a Äá» thi</h3>
</div>

<div class="row">
<div class="col-md-8 grid-margin stretch-card">
<div class="card">
<div class="card-body">

<h4 class="card-title">ThÃ´ng tin Äá» thi</h4>
<?php if($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

    <div class="form-group">
        <label>KhÃ³a há»c</label>
        <select name="course_id" class="form-select" required>
            <?php while($c = $courses->fetch_assoc()): ?>
                <option value="<?= $c['id'] ?>"
                    <?= $quiz['course_id'] == $c['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['title']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="form-group">
        <label>TÃªn Äá» thi</label>
        <input type="text" name="title" class="form-control"
               value="<?= htmlspecialchars($quiz['title']) ?>" required>
    </div>

    <div class="form-group">
        <label>Thá»i gian lÃ m bÃ i (phÃºt)</label>
        <input type="number" name="time_limit" class="form-control"
               min="1" max="180"
               value="<?= $quiz['time_limit'] ?>" required>
    </div>

    <hr>

    <div class="form-group">
        <label>Import thÃªm cÃ¢u há»i tá»« CSV</label>
        <input type="file" name="quiz_file"
               class="form-control form-control-sm"
               accept=".csv">
        <small class="text-muted">
            CSV: CÃ¢u há»i, A, B, C, D, ÄÃ¡p Ã¡n ÄÃºng (A/B/C/D)
        </small>
    </div>

    <button class="btn btn-gradient-primary">LÆ°u thay Äá»i</button>
    <a href="ListDeThi.php" class="btn btn-light">Há»§y</a>

</form>

</div>
</div>
</div>
</div>

</div>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/footer.php"; ?>
</div>
</div>
