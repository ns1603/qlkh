<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role'])) { header("Location: ../../index.php"); exit; }

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];

$sql = "SELECT r.*, u.fullname, c.title as course_name, c.teacher_id
        FROM ratings r
        JOIN users u ON r.user_id = u.id
        JOIN courses c ON r.course_id = c.id";

if ($role == 'teacher') {
    $sql .= " WHERE c.teacher_id = $user_id";
}

$sql .= " ORDER BY r.created_at DESC";
$result = $conn->query($sql);

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
                <h3 class="page-title"> Quáº£n lÃ½ ÄÃ¡nh giÃ¡ (Review) </h3>
            </div>
            
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Pháº£n há»i tá»« há»c viÃªn (<?= $result ? $result->num_rows : 0 ?>)</h4>
                            <?php if ($message): ?><div class="alert alert-success"><?= $message ?></div><?php endif; ?>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Há»c viÃªn</th>
                                            <th>KhÃ³a há»c</th>
                                            <th>Sá» sao</th>
                                            <th>Nháº­n xÃ©t</th>
                                            <th>NgÃ y ÄÃ¡nh giÃ¡</th>
                                            <th>HÃ nh Äá»ng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($result && $result->num_rows > 0): ?>
                                            <?php while($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($row['fullname']) ?></td>
                                                <td><span class="badge badge-outline-info"><?= htmlspecialchars($row['course_name']) ?></span></td>
                                                
                                                <td>
                                                    <div class="text-warning">
                                                        <?php 
                                                            $stars = intval($row['rating']);
                                                            for($i=1; $i<=5; $i++) {
                                                                if($i <= $stars) echo '<i class="mdi mdi-star"></i>';
                                                                else echo '<i class="mdi mdi-star-outline text-muted"></i>';
                                                            }
                                                        ?>
                                                        <span class="text-dark ms-1 small">(<?= $stars ?>/5)</span>
                                                    </div>
                                                </td>

                                                <td style="max-width: 250px; white-space: normal;">
                                                    <i>"<?= htmlspecialchars($row['review']) ?>"</i>
                                                </td>
                                                
                                                <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                                                
                                                <td>
                                                    <?php if ($role != 'admins'): ?>
                                                    <a href="DeleteDanhGia.php?id=<?= $row['id'] ?>" 
                                                       class="btn btn-inverse-danger btn-sm btn-icon"
                                                       onclick="return confirm('Xóa ÄÃ¡nh giÃ¡ nÃ y?')"
                                                       title="Xóa">
                                                        <i class="mdi mdi-delete"></i>
                                                    </a>
                                                    <?php else: ?>
                                                    <span class="text-muted small">Read-only</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr><td colspan="6" class="text-center p-4">ChÆ°a cÃ³ ÄÃ¡nh giÃ¡ nÃ o.</td></tr>
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
