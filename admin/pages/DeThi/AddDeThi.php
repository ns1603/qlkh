<?php
session_start();
include(__DIR__ . '/../../../config.php');

// 1. CHECK QUYỀN
if (!isset($_SESSION['user_role'])) {
    header("Location: ../../index.php");
    exit;
}

if ($_SESSION['user_role'] == 'admins') {
    die("Bạn không có quyền thực hiện hành động này!");
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];
$error = '';

// 2. LẤY DANH SÁCH KHÓA HỌC
$sql_courses = "SELECT id, title FROM courses";
if ($role == 'teacher') {
    $sql_courses .= " WHERE teacher_id = $user_id";
}
$courses = $conn->query($sql_courses);

// 3. XỬ LÝ KHI BẤM LƯU
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_id = intval($_POST['course_id']);
    
    // Check quyền sở hữu khóa học cho Teacher
    if ($role == 'teacher') {
        $check = $conn->query("SELECT id FROM courses WHERE id = $course_id AND teacher_id = $user_id");
        if ($check->num_rows === 0) {
            die("Bạn không có quyền thêm đề thi vào khóa học này!");
        }
    }

    $title = trim($_POST['title']);
    $time_limit = isset($_POST['time_limit']) ? intval($_POST['time_limit']) : 45;

    if (empty($title) || empty($course_id)) {
        $error = "Vui lòng nhập tên đề thi và chọn khóa học!";
    } elseif ($time_limit <= 0) {
        $error = "Thời gian làm bài phải lớn hơn 0 phút!";
    } else {
        // A. TẠO ĐỀ THI TRƯỚC
        $stmt = $conn->prepare("INSERT INTO quizzes (course_id, title, time_limit) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $course_id, $title, $time_limit);
        
        if ($stmt->execute()) {
            $quiz_id = $conn->insert_id; // Lấy ID đề thi vừa tạo
            $imported_count = 0;

            // B. XỬ LÝ FILE CSV (NẾU CÓ)
            if (isset($_FILES['quiz_file']) && $_FILES['quiz_file']['size'] > 0) {
                $filename = $_FILES['quiz_file']['tmp_name'];
                $file = fopen($filename, "r");

                // Bỏ qua dòng tiêu đề (Header)
                fgetcsv($file);

                while (($data = fgetcsv($file, 10000, ",")) !== FALSE) {
                    // Kiểm tra dòng dữ liệu có đủ tối thiểu 7 cột không (tránh dòng trống)
                    if (count($data) < 7) continue;

                    /* MAPPING CỘT (Theo chuẩn 8 cột)
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

                    // Insert câu hỏi vào DB
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

            // Thông báo kết quả
            if ($imported_count > 0) {
                $_SESSION['status_message'] = "Thêm đề thi thành công và đã import $imported_count câu hỏi!";
            } else {
                $_SESSION['status_message'] = "Thêm đề thi thành công! (Chưa có câu hỏi nào được thêm).";
            }

            header("Location: ListDeThi.php");
            exit;
        } else {
            $error = "Lỗi Database: " . $conn->error;
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
                <h3 class="page-title"> Tạo Đề thi mới </h3>
            </div>
            <div class="row">
                <div class="col-md-8 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Thông tin đề thi</h4>
                            <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

                            <form class="forms-sample" method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label>Chọn Khóa Học <span class="text-danger">*</span></label>
                                    <select class="form-select" name="course_id" required>
                                        <option value="">-- Chọn khóa học --</option>
                                        <?php while($c = $courses->fetch_assoc()): ?>
                                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['title']) ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Tên bài kiểm tra</label>
                                    <input type="text" class="form-control" name="title" placeholder="VD: Kiểm tra giữa kỳ..." required>
                                </div>

                                <div class="form-group">
                                    <label>Thời gian làm bài (phút) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="time_limit" value="45" min="1" max="180" required>
                                </div>

                                <div class="form-group border p-3 bg-light rounded mt-4">
                                    <label class="font-weight-bold text-primary">
                                        <i class="mdi mdi-file-import"></i> Import câu hỏi từ file (CSV)
                                    </label>
                                    
                                    <input type="file" name="quiz_file" class="form-control mt-2" accept=".csv">
                                    
                                    <div class="mt-2">
                                        <small class="text-muted d-block mb-1">
                                            <i class="mdi mdi-alert-circle-outline"></i> 
                                            Lưu ý: File phải lưu dưới dạng <b>UTF-8</b> để không lỗi font tiếng Việt.
                                        </small>
                                        
                                        <small class="text-muted d-block">
                                            Thứ tự cột bắt buộc (8 cột): <br>
                                            <code style="background: #e9ecef; padding: 2px 5px; border-radius: 4px; color: #d63384;">
                                                Level, Question, A, B, C, D, Correct, Explain
                                            </code>
                                        </small>

                                    </div>
                                </div>

                                <button type="submit" class="btn btn-gradient-primary me-2 mt-3">Lưu & Tiếp tục</button>
                                <a href="ListDeThi.php" class="btn btn-light mt-3">Hủy</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include ROOT_PATH . "/admin/footer.php"; ?>
    </div>
</div>
