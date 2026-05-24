<?php
session_start();
include(__DIR__ . '/../../../config.php');
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'teacher') {
    die("Truy cập bị từ chối!");
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM teacher_contracts WHERE teacher_id = $user_id ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<?php include $_SERVER['DOCUMENT_ROOT'] . "/Learning/admin/header.php"; ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/Learning/admin/navbar.php"; ?>

<div class="container-fluid page-body-wrapper">
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/Learning/admin/sidebar.php"; ?>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title"> Hợp đồng lao động </h3>
            </div>
            
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Danh sách Hợp đồng của tôi</h4>
                            <p class="card-description">Thông tin chi tiết về thỏa thuận hợp tác.</p>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Mã Hợp đồng</th>
                                            <th>Tỷ lệ chia sẻ</th>
                                            <th>Thời hạn</th>
                                            <th>Trạng thái</th>
                                            <th>Tài liệu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($result && $result->num_rows > 0): ?>
                                            <?php while($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($row['contract_code']) ?></strong></td>
                                                
                                                <td class="text-success fw-bold">
                                                    <?= $row['revenue_share'] ?>% (Giáo viên nhận)
                                                </td>
                                                
                                                <td>
                                                    Từ: <?= date('d/m/Y', strtotime($row['start_date'])) ?> <br>
                                                    Đến: <?= date('d/m/Y', strtotime($row['end_date'])) ?>
                                                </td>
                                                
                                                <td>
                                                    <?php 
                                                        $today = date('Y-m-d');
                                                        if ($row['end_date'] < $today) {
                                                            echo '<label class="badge badge-danger">Đã hết hạn</label>';
                                                        } elseif ($row['status'] == 'active') {
                                                            echo '<label class="badge badge-success">Đang hiệu lực</label>';
                                                        } else {
                                                            echo '<label class="badge badge-warning">Đã chấm dứt</label>';
                                                        }
                                                    ?>
                                                </td>
                                                
                                                <td>
                                                    <?php if(!empty($row['file_path'])): ?>
                                                        <a href="/Learning/<?= $row['file_path'] ?>" target="_blank" class="btn btn-gradient-info btn-sm">
                                                            <i class="mdi mdi-download"></i> Tải về / Xem
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted">Không có file</span>
                                                    <?php endif; ?>
                                                </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center">Bạn chưa có hợp đồng nào. Vui lòng liên hệ Admin.</td>
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
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/Learning/admin/footer.php"; ?>
    </div>
</div>