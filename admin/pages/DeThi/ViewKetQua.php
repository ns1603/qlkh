<?php
session_start();
include(__DIR__ . '/../../../config.php');

// 1. CHECK QUYỀN
if (!isset($_SESSION['user_id']) || 
   ($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'admins' && $_SESSION['user_role'] != 'teacher')) {
    die("Truy cập bị từ chối!");
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];
$quiz_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 2. LẤY THÔNG TIN ĐỀ THI & CHECK QUYỀN SỞ HỮU (Nếu là GV)
// Join bảng quizzes -> courses để lấy teacher_id
$sql_quiz = "SELECT q.title, c.title as course_name, c.teacher_id 
             FROM quizzes q 
             JOIN courses c ON q.course_id = c.id 
             WHERE q.id = $quiz_id";
$quiz_info = $conn->query($sql_quiz)->fetch_assoc();

if (!$quiz_info) {
    die("Đề thi không tồn tại.");
}
if ($role == 'teacher' && $quiz_info['teacher_id'] != $user_id) {
    die("Bạn không có quyền xem kết quả của đề thi này (Thuộc giảng viên khác).");
}
$sql_results = "SELECT er.*, u.fullname, u.email 
                FROM exam_results er 
                JOIN users u ON er.user_id = u.id 
                WHERE er.quiz_id = $quiz_id 
                ORDER BY er.score DESC, er.created_at DESC"; // Sắp xếp điểm cao lên đầu
$results = $conn->query($sql_results);
?>

<?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/header.php"; ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/navbar.php"; ?>

<div class="container-fluid page-body-wrapper">
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/sidebar.php"; ?>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title"> Kết quả thi: <?= htmlspecialchars($quiz_info['title']) ?> </h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="ListDeThi.php">Danh sách đề thi</a></li>
                        <li class="breadcrumb-item active">Kết quả</li>
                    </ol>
                </nav>
            </div>
            
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Khóa học: <?= htmlspecialchars($quiz_info['course_name']) ?></h4>
                            <p class="card-description"> Tổng số bài nộp: <strong><?= $results->num_rows ?></strong> </p>

                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Hạng</th>
                                            <th>Học viên</th>
                                            <th>Email</th>
                                            <th>Điểm số</th>
                                            <th>Kết quả</th>
                                            <th>Ngày nộp bài</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($results->num_rows > 0): ?>
                                            <?php $rank = 1; while($row = $results->fetch_assoc()): 
                                                // Tính phần trăm điểm
                                                $percent = ($row['score'] / $row['total_questions']) * 100;
                                                $status_class = ($percent >= 50) ? 'badge-success' : 'badge-danger';
                                                $status_text = ($percent >= 50) ? 'ĐẠT' : 'TRƯỢT';
                                            ?>
                                            <tr>
                                                <td>#<?= $rank++ ?></td>
                                                <td class="font-weight-bold">
                                                    <?= htmlspecialchars($row['fullname']) ?>
                                                </td>
                                                <td><?= htmlspecialchars($row['email']) ?></td>
                                                <td>
                                                    <h4 class="text-primary m-0">
                                                        <?= $row['score'] ?> / <?= $row['total_questions'] ?>
                                                    </h4>
                                                </td>
                                                <td>
                                                    <label class="badge <?= $status_class ?>"><?= $status_text ?></label>
                                                </td>
                                                <td>
                                                    <?= date('d/m/Y H:i', strtotime($row['created_at'])) ?>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center">Chưa có học viên nào làm bài thi này.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/footer.php"; ?>
    </div>
</div>
