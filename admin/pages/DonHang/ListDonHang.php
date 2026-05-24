<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 'admins' && $_SESSION['user_role'] != 'admin')) {
    die("Truy cáº­p bá» tá»« chá»i!");
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
include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/header.php"; 
include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/navbar.php"; 
?>

<div class="container-fluid page-body-wrapper">
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/sidebar.php"; ?>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title"> Quáº£n lÃ½ Doanh thu & ÄÆ¡n hÃ ng </h3>
            </div>

            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title">Lá»ch sá»­ giao dá»ch (<?= $result ? $result->num_rows : 0 ?>)</h4>
                                <button class="btn btn-sm btn-outline-success">
                                    <i class="mdi mdi-file-excel"></i> Xuáº¥t Excel
                                </button>
                            </div>

                            <?php if ($message): ?>
                                <div class="alert alert-success"><?= $message ?></div>
                            <?php endif; ?>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>MÃ£ ÄH</th>
                                            <th>Há»c viÃªn</th>
                                            <th>KhÃ³a há»c</th>
                                            <th>Sá» tiá»n</th>
                                            <th>PhÆ°Æ¡ng thá»©c</th>
                                            <th>Tráº¡ng thÃ¡i</th>
                                            <th>NgÃ y mua</th>
                                            <th>Xá»­ lÃ½</th>
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
                                                    <?= number_format($row['total_amount'], 0, ',', '.') ?> Ä
                                                </td>
                                                <td>
                                                    <?= ucfirst($row['payment_method']) ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                        $stt = $row['status'];
                                                        if($stt == 'completed') echo '<label class="badge badge-success">ThÃ nh cÃ´ng</label>';
                                                        elseif($stt == 'cancelled') echo '<label class="badge badge-danger">ÄÃ£ há»§y</label>';
                                                        else echo '<label class="badge badge-warning">Chá» duyá»t</label>';
                                                    ?>
                                                </td>
                                                <td>
                                                    <?= date('d/m/Y H:i', strtotime($row['created_at'])) ?>
                                                </td>
                                                <td>
                                                    <?php if ($_SESSION['user_role'] != 'admins'): ?>
                                                    <a href="EditDonHang.php?id=<?= $row['id'] ?>" class="btn btn-inverse-info btn-sm btn-icon" title="Cáº­p nháº­t tráº¡ng thÃ¡i">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </a>
                                                    <a href="DeleteDonHang.php?id=<?= $row['id'] ?>" 
                                                       class="btn btn-inverse-danger btn-sm btn-icon"
                                                       onclick="return confirm('Báº¡n cÃ³ cháº¯c muá»n xÃ³a lá»ch sá»­ giao dá»ch nÃ y?');" 
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
                                            <tr><td colspan="8" class="text-center p-4">ChÆ°a cÃ³ giao dá»ch nÃ o.</td></tr>
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
