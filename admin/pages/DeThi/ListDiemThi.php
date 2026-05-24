<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] == 'student') {
    header("Location: ../../index.php"); exit;
}

$sql = "SELECT r.*, u.fullname, q.title as exam_name, c.title as course_name
        FROM exam_results r
        JOIN users u ON r.user_id = u.id
        JOIN quizzes q ON r.quiz_id = q.id 
        JOIN courses c ON q.course_id = c.id";

// Náº¿u lÃ  giÃ¡o viÃªn, chá» hiá»n Äiá»m cá»§a khÃ³a mÃ¬nh
if ($_SESSION['user_role'] == 'teacher') {
    $teacher_id = $_SESSION['user_id'];
    $sql .= " WHERE c.teacher_id = $teacher_id";
}

$sql .= " ORDER BY r.created_at DESC";
$result = $conn->query($sql);
?>

<?php include ROOT_PATH . "/admin/header.php"; ?>
<?php include ROOT_PATH . "/admin/navbar.php"; ?>

<style>
    @media print {
        .navbar, .sidebar, .footer, .no-print {
            display: none !important;
        }
        .content-wrapper, .main-panel {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>

<div class="container-fluid page-body-wrapper">
    <?php include ROOT_PATH . "/admin/sidebar.php"; ?>
    <div class="main-panel">
        <div class="content-wrapper">
            
            <div class="page-header no-print">
                <h3 class="page-title"> Quáº£n lÃ½ Káº¿t quáº£ thi </h3>
                <nav aria-label="breadcrumb">
                    <button onclick="window.print()" class="btn btn-gradient-info btn-icon-text me-2">
                        <i class="mdi mdi-printer btn-icon-prepend"></i> In Danh SÃ¡ch
                    </button>
                    
                    <a href="ExportDiem.php" class="btn btn-gradient-success btn-icon-text">
                        <i class="mdi mdi-file-excel btn-icon-prepend"></i> Xuáº¥t Excel
                    </a>
                </nav>
            </div>

            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title text-center mb-4">Báº¢NG Tá»NG Há»¢P Káº¾T QUáº¢ THI</h4>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr class="table-primary text-center">
                                            <th>STT</th>
                                            <th>Há»c viÃªn</th>
                                            <th>BÃ i thi</th>
                                            <th>KhÃ³a há»c</th>
                                            <th>Äiá»m sá»</th>
                                            <th>NgÃ y ná»p</th>
                                            <th class="no-print">HÃ nh Äá»ng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($result->num_rows > 0): ?>
                                            <?php $i = 1; while($row = $result->fetch_assoc()): ?>
                                                <tr>
                                                    <td class="text-center"><?= $i++ ?></td>
                                                    <td class="font-weight-bold"><?= htmlspecialchars($row['fullname']) ?></td>
                                                    <td><?= htmlspecialchars($row['exam_name']) ?></td>
                                                    <td><?= htmlspecialchars($row['course_name']) ?></td>
                                                    
                                                    <td class="text-center">
                                                        <?php if($row['score'] >= 8): ?>
                                                            <span class="badge badge-success"><?= $row['score'] ?></span>
                                                        <?php elseif($row['score'] >= 5): ?>
                                                            <span class="badge badge-warning"><?= $row['score'] ?></span>
                                                        <?php else: ?>
                                                            <span class="badge badge-danger"><?= $row['score'] ?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                    
                                                    <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                                                    <td class="no-print text-center">
                                                        <a href="DetailKetQua.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">
                                                            <i class="mdi mdi-eye"></i> Xem chi tiáº¿t
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">ChÆ°a cÃ³ káº¿t quáº£ thi nÃ o.</td>
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
