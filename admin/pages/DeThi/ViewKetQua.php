<?php
session_start();
include(__DIR__ . '/../../../config.php');

// 1. CHECK QUYá»N
if (!isset($_SESSION['user_id']) || 
   ($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'admins' && $_SESSION['user_role'] != 'teacher')) {
    die("Truy cáº­p bá» tá»« chá»i!");
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];
$quiz_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 2. Láº¤Y THÃNG TIN Äá» THI & CHECK QUYá»N Sá» Há»®U (Náº¿u lÃ  GV)
// Join báº£ng quizzes -> courses Äá» láº¥y teacher_id
$sql_quiz = "SELECT q.title, c.title as course_name, c.teacher_id 
             FROM quizzes q 
             JOIN courses c ON q.course_id = c.id 
             WHERE q.id = $quiz_id";
$quiz_info = $conn->query($sql_quiz)->fetch_assoc();

if (!$quiz_info) {
    die("Äá» thi khÃ´ng tá»n táº¡i.");
}
if ($role == 'teacher' && $quiz_info['teacher_id'] != $user_id) {
    die("Báº¡n khÃ´ng cÃ³ quyá»n xem káº¿t quáº£ cá»§a Äá» thi nÃ y (Thuá»c giáº£ng viÃªn khÃ¡c).");
}
$sql_results = "SELECT er.*, u.fullname, u.email 
                FROM exam_results er 
                JOIN users u ON er.user_id = u.id 
                WHERE er.quiz_id = $quiz_id 
                ORDER BY er.score DESC, er.created_at DESC"; // Sáº¯p xáº¿p Äiá»m cao lÃªn Äáº§u
$results = $conn->query($sql_results);
?>

<?php include ROOT_PATH . "/admin/header.php"; ?>
<?php include ROOT_PATH . "/admin/navbar.php"; ?>

<div class="container-fluid page-body-wrapper">
    <?php include ROOT_PATH . "/admin/sidebar.php"; ?>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title"> Káº¿t quáº£ thi: <?= htmlspecialchars($quiz_info['title']) ?> </h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="ListDeThi.php">Danh sÃ¡ch Äá» thi</a></li>
                        <li class="breadcrumb-item active">Káº¿t quáº£</li>
                    </ol>
                </nav>
            </div>
            
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">KhÃ³a há»c: <?= htmlspecialchars($quiz_info['course_name']) ?></h4>
                            <p class="card-description"> Tá»ng sá» bÃ i ná»p: <strong><?= $results->num_rows ?></strong> </p>

                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Háº¡ng</th>
                                            <th>Há»c viÃªn</th>
                                            <th>Email</th>
                                            <th>Äiá»m sá»</th>
                                            <th>Káº¿t quáº£</th>
                                            <th>NgÃ y ná»p bÃ i</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($results->num_rows > 0): ?>
                                            <?php $rank = 1; while($row = $results->fetch_assoc()): 
                                                // TÃ­nh pháº§n trÄm Äiá»m
                                                $percent = ($row['score'] / $row['total_questions']) * 100;
                                                $status_class = ($percent >= 50) ? 'badge-success' : 'badge-danger';
                                                $status_text = ($percent >= 50) ? 'Äáº T' : 'TRÆ¯á»¢T';
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
                                                <td colspan="6" class="text-center">ChÆ°a cÃ³ há»c viÃªn nÃ o lÃ m bÃ i thi nÃ y.</td>
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
        <?php include ROOT_PATH . "/admin/footer.php"; ?>
    </div>
</div>
