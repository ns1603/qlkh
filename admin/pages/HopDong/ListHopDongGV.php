<?php
session_start();
include(__DIR__ . '/../../../config.php');
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 'admins' && $_SESSION['user_role'] != 'admin')) {
    die("Truy cập bị từ chối!");
}

$sql = "SELECT tc.*, u.fullname, u.email 
        FROM teacher_contracts tc 
        JOIN users u ON tc.teacher_id = u.id 
        ORDER BY tc.created_at DESC";
$result = $conn->query($sql);

$message = isset($_SESSION['status_message']) ? $_SESSION['status_message'] : '';
unset($_SESSION['status_message']);
?>

<?php include ROOT_PATH . "/admin/header.php"; ?>
<?php include ROOT_PATH . "/admin/navbar.php"; ?>

<div class="container-fluid page-body-wrapper">
    <?php include ROOT_PATH . "/admin/sidebar.php"; ?>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title"> Quản lý Hợp đồng Giáo viên </h3>
            </div>
            
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title">Danh sách Hợp đồng (<?= $result->num_rows ?>)</h4>
                                <a href="AddHopDongGV.php" class="btn btn-sm btn-gradient-primary">
                                    <i class="mdi mdi-file-document-box-plus"></i> Tạo Hợp đồng mới
                                </a>
                            </div>

                            <?php if ($message): ?> <div class="alert alert-success"><?= $message ?></div> <?php endif; ?>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Mã HĐ</th>
                                            <th>Giáo viên</th>
                                            <th>Tỷ lệ chia sẻ</th>
                                            <th>Hiệu lực</th>
                                            <th>File scan</th>
                                            <th>Trạng thái</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($row['contract_code']) ?></strong></td>
                                            <td>
                                                <?= htmlspecialchars($row['fullname']) ?><br>
                                                <small class="text-muted"><?= $row['email'] ?></small>
                                            </td>
                                            <td class="text-primary fw-bold"><?= $row['revenue_share'] ?>%</td>
                                            <td>
                                                <?= date('d/m/Y', strtotime($row['start_date'])) ?> <br>
                                                đến <?= date('d/m/Y', strtotime($row['end_date'])) ?>
                                            </td>
                                            <td>
                                                <?php if(!empty($row['file_path'])): ?>
                                                    <a href="<?= BASE_PATH ?>/<?= $row['file_path'] ?>" target="_blank" class="btn btn-inverse-info btn-sm icon-btn">
                                                        <i class="mdi mdi-file-pdf"></i> Xem
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">Chưa upload</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($row['status']=='active'): ?>
                                                    <label class="badge badge-success">Hiệu lực</label>
                                                <?php else: ?>
                                                    <label class="badge badge-danger">Hết hạn</label>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="DeleteHopDongGV.php?id=<?= $row['id'] ?>" class="btn btn-inverse-danger btn-sm" onclick="return confirm('Xóa hợp đồng này?')">
                                                    <i class="mdi mdi-delete"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
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
