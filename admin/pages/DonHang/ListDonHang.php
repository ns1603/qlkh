<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 'admins' && $_SESSION['user_role'] != 'admin')) {
    die("Truy cập bị từ chối!");
}

$sql = "SELECT o.*, u.fullname, c.title as course_name 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        JOIN courses c ON o.course_id = c.id 
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);

$message = isset($_SESSION['status_message']) ? $_SESSION['status_message'] : '';
unset($_SESSION['status_message']);
?>

<?php 
include $_SERVER['DOCUMENT_ROOT'] . "/Learning/admin/header.php"; 
include $_SERVER['DOCUMENT_ROOT'] . "/Learning/admin/navbar.php"; 
?>

<div class="container-fluid page-body-wrapper">
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/Learning/admin/sidebar.php"; ?>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title"> Quản lý Doanh thu & Đơn hàng </h3>
            </div>

            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title">Lịch sử giao dịch (<?= $result ? $result->num_rows : 0 ?>)</h4>
                                <button class="btn btn-sm btn-outline-success">
                                    <i class="mdi mdi-file-excel"></i> Xuất Excel
                                </button>
                            </div>

                            <?php if ($message): ?>
                                <div class="alert alert-success"><?= $message ?></div>
                            <?php endif; ?>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Mã ĐH</th>
                                            <th>Học viên</th>
                                            <th>Khóa học</th>
                                            <th>Số tiền</th>
                                            <th>Phương thức</th>
                                            <th>Trạng thái</th>
                                            <th>Ngày mua</th>
                                            <th>Xử lý</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($result && $result->num_rows > 0): ?>
                                            <?php while($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td>#<?= $row['id'] ?></td>
                                                <td>
                                                    <strong><?= htmlspecialchars($row['fullname']) ?></strong>
                                                </td>
                                                <td>
                                                    <span class="text-muted"><?= htmlspecialchars($row['course_name']) ?></span>
                                                </td>
                                                <td class="text-success fw-bold">
                                                    <?= number_format($row['total_amount'], 0, ',', '.') ?> đ
                                                </td>
                                                <td>
                                                    <?= ucfirst($row['payment_method']) ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                        $stt = $row['status'];
                                                        if($stt == 'completed') echo '<label class="badge badge-success">Thành công</label>';
                                                        elseif($stt == 'cancelled') echo '<label class="badge badge-danger">Đã hủy</label>';
                                                        else echo '<label class="badge badge-warning">Chờ duyệt</label>';
                                                    ?>
                                                </td>
                                                <td>
                                                    <?= date('d/m/Y H:i', strtotime($row['created_at'])) ?>
                                                </td>
                                                <td>
                                                    <a href="EditDonHang.php?id=<?= $row['id'] ?>" class="btn btn-inverse-info btn-sm btn-icon" title="Cập nhật trạng thái">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </a>
                                                    <a href="DeleteDonHang.php?id=<?= $row['id'] ?>" 
                                                       class="btn btn-inverse-danger btn-sm btn-icon"
                                                       onclick="return confirm('Bạn có chắc muốn xóa lịch sử giao dịch này?');" 
                                                       title="Xóa">
                                                        <i class="mdi mdi-delete"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr><td colspan="8" class="text-center p-4">Chưa có giao dịch nào.</td></tr>
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