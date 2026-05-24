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

<?php include $_SERVER['DOCUMENT_ROOT'] . "/Learning/admin/header.php"; ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/Learning/admin/navbar.php"; ?>

<div class="container-fluid page-body-wrapper">
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/Learning/admin/sidebar.php"; ?>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title"> Quản lý Đánh giá (Review) </h3>
            </div>
            
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Phản hồi từ học viên (<?= $result ? $result->num_rows : 0 ?>)</h4>
                            <?php if ($message): ?><div class="alert alert-success"><?= $message ?></div><?php endif; ?>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Học viên</th>
                                            <th>Khóa học</th>
                                            <th>Số sao</th>
                                            <th>Nhận xét</th>
                                            <th>Ngày đánh giá</th>
                                            <th>Hành động</th>
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
                                                    <a href="DeleteDanhGia.php?id=<?= $row['id'] ?>" 
                                                       class="btn btn-inverse-danger btn-sm btn-icon"
                                                       onclick="return confirm('Xóa đánh giá này?')"
                                                       title="Xóa">
                                                        <i class="mdi mdi-delete"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr><td colspan="6" class="text-center p-4">Chưa có đánh giá nào.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/Learning/admin/footer.php"; ?>
    </div>
</div>