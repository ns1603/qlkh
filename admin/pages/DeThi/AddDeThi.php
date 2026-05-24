<?php
session_start();
include(__DIR__ . '/../../../config.php');

// 1. CHECK QUYá»N
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

// 2. Láº¤Y DANH SÃCH KHÃA Há»C
$sql_courses = "SELECT id, title FROM courses";
if ($role == 'teacher') {
    $sql_courses .= " WHERE teacher_id = $user_id";
}
$courses = $conn->query($sql_courses);

// 3. Xá»¬ LÃ KHI Báº¤M LÆ¯U
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_id = intval($_POST['course_id']);
    
    // Check quyá»n sá» há»¯u khÃ³a há»c cho Teacher
    if ($role == 'teacher') {
        $check = $conn->query("SELECT id FROM courses WHERE id = $course_id AND teacher_id = $user_id");
        if ($check->num_rows === 0) {
            die("Báº¡n khÃ´ng cÃ³ quyá»n thÃªm Äá» thi vÃ o khÃ³a há»c nÃ y!");
        }
    }

    $title = trim($_POST['title']);
    $time_limit = isset($_POST['time_limit']) ? intval($_POST['time_limit']) : 45;

    if (empty($title) || empty($course_id)) {
        $error = "Vui lÃ²ng nháº­p tÃªn Äá» thi vÃ  chá»n khÃ³a há»c!";
    } elseif ($time_limit <= 0) {
        $error = "Thá»i gian lÃ m bÃ i pháº£i lá»n hÆ¡n 0 phÃºt!";
    } else {
        // A. Táº O Äá» THI TRÆ¯á»C
        $stmt = $conn->prepare("INSERT INTO quizzes (course_id, title, time_limit) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $course_id, $title, $time_limit);
        
        if ($stmt->execute()) {
            $quiz_id = $conn->insert_id; // Láº¥y ID Äá» thi vá»«a táº¡o
            $imported_count = 0;

            // B. Xá»¬ LÃ FILE CSV (Náº¾U CÃ)
            if (isset($_FILES['quiz_file']) && $_FILES['quiz_file']['size'] > 0) {
                $filename = $_FILES['quiz_file']['tmp_name'];
                $file = fopen($filename, "r");

                // Bá» qua dÃ²ng tiÃªu Äá» (Header)
                fgetcsv($file);

                while (($data = fgetcsv($file, 10000, ",")) !== FALSE) {
                    // Kiá»m tra dÃ²ng dá»¯ liá»u cÃ³ Äá»§ tá»i thiá»u 7 cá»t khÃ´ng (trÃ¡nh dÃ²ng trá»ng)
                    if (count($data) < 7) continue;

                    /* MAPPING Cá»T (Theo chuáº©n 8 cá»t)
                       0: Level | 1: Question | 2: A | 3: B | 4: C | 5: D | 6: Correct | 7: Explain
                    */
                    $level          = $data[0] ?? 'easy';
                    $question_text  = $data[1];
                    $option_a       = $data[2];
                    $option_b       = $data[3];
                    $option_c       = $data[4];
                    $option_d       = $data[5];
                    $correct_answer = strtoupper(trim($data[6])); // A, B, C, D
                    $explanation    = $data[7] ?? '';

                    // Insert cÃ¢u há»i vÃ o DB
                    $sql_q = "INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_answer, explanation, level) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt_q = $conn->prepare($sql_q);
                    $stmt_q->bind_param("issssssss", $quiz_id, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer, $explanation, $level);
                    
                    if ($stmt_q->execute()) {
                        $imported_count++;
                    }
                }
                fclose($file);
            }

            // ThÃ´ng bÃ¡o káº¿t quáº£
            if ($imported_count > 0) {
                $_SESSION['status_message'] = "Thêm Äá» thi thÃ nh cÃ´ng vÃ  ÄÃ£ import $imported_count cÃ¢u há»i!";
            } else {
                $_SESSION['status_message'] = "Thêm Äá» thi thÃ nh cÃ´ng! (ChÆ°a cÃ³ cÃ¢u há»i nÃ o ÄÆ°á»£c thÃªm).";
            }

            header("Location: ListDeThi.php");
            exit;
        } else {
            $error = "Lá»i Database: " . $conn->error;
        }
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
                <h3 class="page-title"> Táº¡o Äá» thi má»i </h3>
            </div>
            <div class="row">
                <div class="col-md-8 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">ThÃ´ng tin Äá» thi</h4>
                            <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

                            <form class="forms-sample" method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label>Chá»n KhÃ³a Há»c <span class="text-danger">*</span></label>
                                    <select class="form-select" name="course_id" required>
                                        <option value="">-- Chá»n khÃ³a há»c --</option>
                                        <?php while($c = $courses->fetch_assoc()): ?>
                                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['title']) ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>TÃªn bÃ i kiá»m tra</label>
                                    <input type="text" class="form-control" name="title" placeholder="VD: Kiá»m tra giá»¯a ká»³..." required>
                                </div>

                                <div class="form-group">
                                    <label>Thá»i gian lÃ m bÃ i (phÃºt) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="time_limit" value="45" min="1" max="180" required>
                                </div>

                                <div class="form-group border p-3 bg-light rounded mt-4">
                                    <label class="font-weight-bold text-primary">
                                        <i class="mdi mdi-file-import"></i> Import cÃ¢u há»i tá»« file (CSV)
                                    </label>
                                    
                                    <input type="file" name="quiz_file" class="form-control mt-2" accept=".csv">
                                    
                                    <div class="mt-2">
                                        <small class="text-muted d-block mb-1">
                                            <i class="mdi mdi-alert-circle-outline"></i> 
                                            LÆ°u Ã½: File pháº£i lÆ°u dÆ°á»i dáº¡ng <b>UTF-8</b> Äá» khÃ´ng lá»i font tiáº¿ng Viá»t.
                                        </small>
                                        
                                        <small class="text-muted d-block">
                                            Thá»© tá»± cá»t báº¯t buá»c (8 cá»t): <br>
                                            <code style="background: #e9ecef; padding: 2px 5px; border-radius: 4px; color: #d63384;">
                                                Level, Question, A, B, C, D, Correct, Explain
                                            </code>
                                        </small>

                                    </div>
                                </div>

                                <button type="submit" class="btn btn-gradient-primary me-2 mt-3">LÆ°u & Tiáº¿p tá»¥c</button>
                                <a href="ListDeThi.php" class="btn btn-light mt-3">Há»§y</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include ROOT_PATH . "/admin/footer.php"; ?>
    </div>
</div>
