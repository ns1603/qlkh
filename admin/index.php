<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include(__DIR__ . '/../config.php'); 

if (!isset($_SESSION['user_role']) || 
   ($_SESSION['user_role'] != 'admins' && $_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'teacher')) {
    header("Location: Login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role']; 
$is_admin = ($role === 'admins' || $role === 'admin');

$stats = [
    'revenue'     => 0,
    'avg_rating'  => 0,
    'certificates'=> 0, 
    'students'    => 0,
    'courses'     => 0,
    'lessons'     => 0,
    'quizzes'     => 0,
    'questions'   => 0
];

if ($is_admin) {
    $q = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE status = 'completed'");
    $stats['revenue'] = $q->fetch_assoc()['total'] ?? 0;
    $q = $conn->query("SELECT AVG(rating) as avg_rate FROM ratings");
    $stats['avg_rating'] = round($q->fetch_assoc()['avg_rate'] ?? 0, 1);

    $q = $conn->query("SELECT COUNT(*) as total FROM certificates");
    $stats['certificates'] = $q->fetch_assoc()['total'] ?? 0;
    $stats['courses']   = $conn->query("SELECT COUNT(*) as total FROM courses")->fetch_assoc()['total'] ?? 0;
    $stats['students']  = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'student'")->fetch_assoc()['total'] ?? 0;
    $stats['lessons']   = $conn->query("SELECT COUNT(*) as total FROM lessons")->fetch_assoc()['total'] ?? 0;
    $stats['quizzes']   = $conn->query("SELECT COUNT(*) as total FROM quizzes")->fetch_assoc()['total'] ?? 0;
    $stats['questions'] = $conn->query("SELECT COUNT(*) as total FROM questions")->fetch_assoc()['total'] ?? 0;
    $sql_recent_main = "SELECT users.fullname, courses.title as course_name, orders.total_amount, orders.status, orders.created_at 
                        FROM orders 
                        JOIN users ON orders.user_id = users.id 
                        JOIN courses ON orders.course_id = courses.id 
                        ORDER BY orders.created_at DESC LIMIT 6";
} 
else {
    $sql = "SELECT SUM(orders.total_amount) as total 
            FROM orders 
            JOIN courses ON orders.course_id = courses.id 
            WHERE courses.teacher_id = $user_id AND orders.status = 'completed'";
    $q = $conn->query($sql);
    $stats['revenue'] = $q->fetch_assoc()['total'] ?? 0;
    $sql = "SELECT AVG(ratings.rating) as avg_rate 
            FROM ratings 
            JOIN courses ON ratings.course_id = courses.id 
            WHERE courses.teacher_id = $user_id";
    $q = $conn->query($sql);
    $stats['avg_rating'] = round($q->fetch_assoc()['avg_rate'] ?? 0, 1);
    $sql = "SELECT COUNT(certificates.id) as total 
            FROM certificates 
            JOIN courses ON certificates.course_id = courses.id 
            WHERE courses.teacher_id = $user_id";
    $q = $conn->query($sql);
    $stats['certificates'] = $q->fetch_assoc()['total'] ?? 0;
    
    $stats['courses'] = $conn->query("SELECT COUNT(*) as total FROM courses WHERE teacher_id = $user_id")->fetch_assoc()['total'] ?? 0;
    
    $sql = "SELECT COUNT(DISTINCT enrollments.user_id) as total FROM enrollments JOIN courses ON enrollments.course_id = courses.id WHERE courses.teacher_id = $user_id";
    
    $stats['students'] = $conn->query($sql)->fetch_assoc()['total'] ?? 0;
    
    $sql = "SELECT COUNT(lessons.id) as total FROM lessons JOIN courses ON lessons.course_id = courses.id WHERE courses.teacher_id = $user_id";
    
    $stats['lessons'] = $conn->query($sql)->fetch_assoc()['total'] ?? 0;
    
    $sql = "SELECT COUNT(questions.id) as total FROM questions JOIN quizzes ON questions.quiz_id = quizzes.id JOIN courses ON quizzes.course_id = courses.id WHERE courses.teacher_id = $user_id";
    
    $stats['questions'] = $conn->query($sql)->fetch_assoc()['total'] ?? 0;
    
    $sql_recent_main = "SELECT users.fullname, lessons.title as lesson_name, courses.title as course_name, progress.status, progress.updated_at as created_at
                        FROM progress 
                        JOIN users ON progress.user_id = users.id 
                        JOIN lessons ON progress.lesson_id = lessons.id 
                        JOIN courses ON lessons.course_id = courses.id 
                        WHERE courses.teacher_id = $user_id
                        ORDER BY progress.updated_at DESC LIMIT 6";
}

$where_review = $is_admin ? "" : "WHERE courses.teacher_id = $user_id";
$sql_reviews = "SELECT ratings.rating, ratings.review, users.fullname, courses.title as course_name, ratings.created_at
                FROM ratings 
                JOIN users ON ratings.user_id = users.id 
                JOIN courses ON ratings.course_id = courses.id 
                $where_review
                ORDER BY ratings.created_at DESC LIMIT 5";

$result_main = $conn->query($sql_recent_main);
$result_reviews = $conn->query($sql_reviews);
?>

<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>

<div class="container-fluid page-body-wrapper">
    <?php include 'sidebar.php'; ?>
    <div class="main-panel">
        <div class="content-wrapper">
            
            <div class="page-header">
                <h3 class="page-title">
                    <span class="page-title-icon bg-gradient-primary text-white me-2">
                        <i class="mdi mdi-home"></i>
                    </span> 
                    Dashboard - <?php echo $is_admin ? "Quản trị viên" : "Giảng viên"; ?>
                </h3>
            </div>

            <div class="row">
                <div class="col-md-3 stretch-card grid-margin">
                    <div class="card bg-gradient-success card-img-holder text-white">
                        <div class="card-body" style="padding: 1.5rem;">
                            <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                            <h4 class="font-weight-normal mb-3">Doanh Thu <i class="mdi mdi-cash-usd mdi-24px float-end"></i></h4>
                            <h2 class="mb-5"><?php echo number_format($stats['revenue']); ?> đ</h2>
                            <h6 class="card-text">Thu nhập thực tế</h6>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 stretch-card grid-margin">
                    <div class="card bg-gradient-warning card-img-holder text-white">
                        <div class="card-body" style="padding: 1.5rem;">
                            <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                            <h4 class="font-weight-normal mb-3">Đánh Giá <i class="mdi mdi-star mdi-24px float-end"></i></h4>
                            <h2 class="mb-5"><?php echo $stats['avg_rating']; ?> / 5</h2>
                            <h6 class="card-text">Chất lượng khóa học</h6>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 stretch-card grid-margin">
                    <div class="card bg-gradient-info card-img-holder text-white">
                        <div class="card-body" style="padding: 1.5rem;">
                            <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                            <h4 class="font-weight-normal mb-3">Học Viên <i class="mdi mdi-account-group mdi-24px float-end"></i></h4>
                            <h2 class="mb-5"><?php echo $stats['students']; ?></h2>
                            <h6 class="card-text">Đang theo học</h6>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 stretch-card grid-margin">
                    <div class="card bg-gradient-danger card-img-holder text-white">
                        <div class="card-body" style="padding: 1.5rem;">
                            <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                            <h4 class="font-weight-normal mb-3">Khóa Học <i class="mdi mdi-book-open-page-variant mdi-24px float-end"></i></h4>
                            <h2 class="mb-5"><?php echo $stats['courses']; ?></h2>
                            <h6 class="card-text">Lớp đang mở</h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 stretch-card grid-margin">
                    <div class="card card-img-holder text-dark shadow-sm">
                        <div class="card-body text-center p-3">
                            <i class="mdi mdi-certificate mdi-36px text-warning mb-2"></i>
                            <h4><?php echo $stats['certificates']; ?></h4>
                            <p class="text-muted mb-0">Chứng chỉ đã cấp</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 stretch-card grid-margin">
                    <div class="card card-img-holder text-dark shadow-sm">
                        <div class="card-body text-center p-3">
                            <i class="mdi mdi-video mdi-36px text-primary mb-2"></i>
                            <h4><?php echo $stats['lessons']; ?></h4>
                            <p class="text-muted mb-0">Video bài giảng</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 stretch-card grid-margin">
                    <div class="card card-img-holder text-dark shadow-sm">
                        <div class="card-body text-center p-3">
                            <i class="mdi mdi-help-circle mdi-36px text-danger mb-2"></i>
                            <h4><?php echo $stats['questions']; ?></h4>
                            <p class="text-muted mb-0">Câu hỏi trắc nghiệm</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 stretch-card grid-margin">
                    <div class="card card-img-holder text-dark shadow-sm">
                        <div class="card-body text-center p-3">
                            <i class="mdi mdi-file-document-box mdi-36px text-success mb-2"></i>
                            <h4><?php echo $stats['quizzes']; ?></h4>
                            <p class="text-muted mb-0">Đề thi / Bài tập</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                
                <div class="col-md-8 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">
                                <?php echo $is_admin ? "Đơn hàng mới nhất (Orders)" : "Hoạt động học tập (Progress)"; ?>
                            </h4>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th> Người dùng </th>
                                            <th> <?php echo $is_admin ? "Khóa học đã mua" : "Bài đang học"; ?> </th>
                                            <th> <?php echo $is_admin ? "Thành tiền" : "Trạng thái"; ?> </th>
                                            <th> Thời gian </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($result_main && $result_main->num_rows > 0): ?>
                                            <?php while($row = $result_main->fetch_assoc()): ?>
                                                <tr>
                                                    <td>
                                                        <img src="assets/images/faces/face1.jpg" class="me-2" alt="image">
                                                        <?php echo htmlspecialchars($row['fullname']); ?>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                            if($is_admin) echo htmlspecialchars($row['course_name']);
                                                            else echo htmlspecialchars($row['lesson_name']) . " <br><small class='text-muted'>".htmlspecialchars($row['course_name'])."</small>";
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                            if ($is_admin) {
                                                                echo number_format($row['total_amount']) . ' đ';
                                                                echo ($row['status'] == 'completed') ? ' <i class="mdi mdi-check-circle text-success"></i>' : ' <i class="mdi mdi-clock text-warning"></i>';
                                                            } else {
                                                                if ($row['status'] == 'completed') echo '<label class="badge badge-gradient-success">Đã xong</label>';
                                                                elseif ($row['status'] == 'in_progress') echo '<label class="badge badge-gradient-warning">Đang học</label>';
                                                                else echo '<label class="badge badge-gradient-secondary">Mới vào</label>';
                                                            }
                                                        ?>
                                                    </td>
                                                    <td class="text-muted">
                                                        <?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr><td colspan="4" class="text-center">Chưa có dữ liệu.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Đánh giá mới nhất</h4>
                            <div class="list-wrapper">
                                <ul class="d-flex flex-column-reverse todo-list todo-list-custom">
                                    <?php if ($result_reviews && $result_reviews->num_rows > 0): ?>
                                        <?php while($rv = $result_reviews->fetch_assoc()): ?>
                                            <li style="border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 10px; display:block;">
                                                <div class="d-flex justify-content-between">
                                                    <strong><?php echo htmlspecialchars($rv['fullname']); ?></strong>
                                                    <div class="text-warning">
                                                        <?php 
                                                            // Vẽ ngôi sao
                                                            for($i=1; $i<=5; $i++) {
                                                                if($i <= $rv['rating']) echo '<i class="mdi mdi-star"></i>';
                                                                else echo '<i class="mdi mdi-star-outline"></i>';
                                                            }
                                                        ?>
                                                    </div>
                                                </div>
                                                <small class="text-primary d-block mt-1 mb-1"><?php echo htmlspecialchars($rv['course_name']); ?></small>
                                                <p class="text-secondary mb-1" style="font-size: 0.9em; font-style: italic;">
                                                    "<?php echo htmlspecialchars($rv['review']); ?>"
                                                </p>
                                                <small class="text-muted"><i class="mdi mdi-clock"></i> <?php echo date('d/m/Y', strtotime($rv['created_at'])); ?></small>
                                            </li>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <p class="text-center text-muted mt-3">Chưa có đánh giá nào.</p>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
