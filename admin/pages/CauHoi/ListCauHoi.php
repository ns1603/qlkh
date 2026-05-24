<?php
session_start();
include(__DIR__ . '/../../../config.php');

// 1. CHECK QUYá»N
if (!isset($_SESSION['user_role'])) {
    header("Location: ../../index.php");
    exit;
}

$quiz_id = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;
$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];

// 2. Láº¤Y THÃNG TIN Äá» THI
$sql_quiz = "SELECT q.title, c.teacher_id, c.title as course_title 
             FROM quizzes q 
             JOIN courses c ON q.course_id = c.id 
             WHERE q.id = $quiz_id";
$quiz_info = $conn->query($sql_quiz)->fetch_assoc();

if (!$quiz_info) {
    die("Äá» thi khÃ´ng tá»n táº¡i!");
}
if ($role == 'teacher' && $quiz_info['teacher_id'] != $user_id) {
    die("Báº¡n khÃ´ng cÃ³ quyá»n xem Äá» thi nÃ y!");
}

// 3. Láº¤Y DANH SÃCH CÃU Há»I
$sql_questions = "SELECT * FROM questions WHERE quiz_id = $quiz_id ORDER BY id ASC";
$questions = $conn->query($sql_questions);

$message = isset($_SESSION['status_message']) ? $_SESSION['status_message'] : '';
unset($_SESSION['status_message']);
?>

<?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/header.php"; ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/navbar.php"; ?>

<div class="container-fluid page-body-wrapper">
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/sidebar.php"; ?>
    <div class="main-panel">
        <div class="content-wrapper">
            
            <div class="page-header">
                <h3 class="page-title"> NgÃ¢n hÃ ng cÃ¢u há»i </h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../DeThi/ListDeThi.php">DS Äá» thi</a></li>
                        <li class="breadcrumb-item active">Chi tiáº¿t Äá» #<?= $quiz_id ?></li>
                    </ol>
                </nav>
            </div>

            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title text-primary">Äá» thi: <?= htmlspecialchars($quiz_info['title']) ?></h4>
                            <p class="card-description">Thuá»c khÃ³a há»c: <strong><?= htmlspecialchars($quiz_info['course_title']) ?></strong></p>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <span>Tá»ng sá» cÃ¢u: <strong><?= $questions->num_rows ?></strong></span>
                                <?php if ($role != 'admins'): ?>
                                <div>
                                    <a href="AddCauHoi.php?quiz_id=<?= $quiz_id ?>" class="btn btn-sm btn-gradient-success">
                                        <i class="mdi mdi-plus-circle"></i> Thêm thá»§ cÃ´ng
                                    </a>
                                    <a href="../DeThi/ImportDeThi.php" class="btn btn-sm btn-gradient-info">
                                        <i class="mdi mdi-upload"></i> Import thÃªm tá»« Excel
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>

                            <?php if ($message): ?>
                                <div class="alert alert-success"><?= $message ?></div>
                            <?php endif; ?>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="5%" class="text-center">ID</th>
                                            <th width="10%" class="text-center">Má»©c Äá»</th>
                                            <th width="70%">Ná»i dung cÃ¢u há»i & ÄÃ¡p Ã¡n</th>
                                            <th width="15%" class="text-center">HÃ nh Äá»ng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($questions->num_rows > 0): ?>
                                            <?php while($row = $questions->fetch_assoc()): ?>
                                            <tr>
                                                <td class="text-center font-weight-bold"><?= $row['id'] ?></td>
                                                
                                                <td class="text-center">
                                                    <?php 
                                                        $lv = strtolower($row['level']);
                                                        if($lv == 'easy') echo '<span class="badge badge-success">Dá»</span>';
                                                        elseif($lv == 'medium') echo '<span class="badge badge-warning">TB</span>';
                                                        elseif($lv == 'hard') echo '<span class="badge badge-danger">KhÃ³</span>';
                                                        else echo '<span class="badge badge-secondary">'.$lv.'</span>';
                                                    ?>
                                                </td>

                                                <td>
                                                    <p class="mb-2 fw-bold" style="font-size: 1.1em;">
                                                        <?= htmlspecialchars($row['question_text']) ?>
                                                    </p>
                                                    
                                                    <div class="row">
                                                        <?php 
                                                            // Máº£ng cÃ¡c ÄÃ¡p Ã¡n A, B, C, D
                                                            $options = [
                                                                'A' => $row['option_a'],
                                                                'B' => $row['option_b'],
                                                                'C' => $row['option_c'],
                                                                'D' => $row['option_d']
                                                            ];
                                                            $correct = strtoupper(trim($row['correct_answer'])); // VÃ­ dá»¥: 'A'
                                                        ?>
                                                        
                                                        <?php foreach($options as $key => $val): ?>
                                                            <?php 
                                                                // Kiá»m tra náº¿u ÄÃ¢y lÃ  ÄÃ¡p Ã¡n ÄÃºng
                                                                $is_correct = ($key == $correct);
                                                                $style = $is_correct ? "color: #198754; font-weight: bold; background: #e8f5e9; border-radius: 4px;" : "color: #555;";
                                                                $icon = $is_correct ? '<i class="mdi mdi-check-circle"></i>' : '<i class="mdi mdi-checkbox-blank-circle-outline"></i>';
                                                            ?>
                                                            <div class="col-md-6 mb-1" style="<?= $style ?> padding: 5px;">
                                                                <?= $icon ?> <strong><?= $key ?>.</strong> <?= htmlspecialchars($val) ?>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>

                                                    <?php if(!empty($row['explanation'])): ?>
                                                        <div class="mt-2 p-2 bg-light border rounded text-muted" style="font-size: 0.9em;">
                                                            <i class="mdi mdi-lightbulb-on text-warning"></i> 
                                                            <strong>Giáº£i thÃ­ch:</strong> <?= htmlspecialchars($row['explanation']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>

                                                <td class="text-center">
                                                    <?php if ($role != 'admins'): ?>
                                                    <a href="EditCauHoi.php?id=<?= $row['id'] ?>&quiz_id=<?= $quiz_id ?>" 
                                                       class="btn btn-inverse-warning btn-sm btn-icon" title="Sá»­a">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </a>
                                                    <a href="DeleteCauHoi.php?id=<?= $row['id'] ?>&quiz_id=<?= $quiz_id ?>" 
                                                       class="btn btn-inverse-danger btn-sm btn-icon"
                                                       onclick="return confirm('Báº¡n cÃ³ cháº¯c cháº¯n muá»n xÃ³a cÃ¢u há»i nÃ y?')" title="Xóa">
                                                        <i class="mdi mdi-delete"></i>
                                                    </a>
                                                    <?php else: ?>
                                                    <span class="text-muted small">Read-only</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr><td colspan="4" class="text-center p-5 text-muted">ChÆ°a cÃ³ cÃ¢u há»i nÃ o. HÃ£y import hoáº·c thÃªm má»i!</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-4">
                                <a href="../DeThi/ListDeThi.php" class="btn btn-light">Quay láº¡i danh sÃ¡ch Äá» thi</a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/footer.php"; ?>
    </div>
</div>
