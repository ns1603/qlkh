<?php
session_start();
include(__DIR__ . '/../../../config.php');
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 'admins' && $_SESSION['user_role'] != 'admin')) {
    die("Truy cáº­p bá» tá»« chá»i!");
}

$sql = "SELECT tc.*, u.fullname, u.email 
        FROM teacher_contracts tc 
        JOIN users u ON tc.teacher_id = u.id 
        ORDER BY tc.created_at DESC";
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
                <h3 class="page-title"> Quáº£n lÃ½ Há»£p Äá»ng GiÃ¡o viÃªn </h3>
            </div>
            
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title">Danh sÃ¡ch Há»£p Äá»ng (<?= $result->num_rows ?>)</h4>
                                <a href="AddHopDongGV.php" class="btn btn-sm btn-gradient-primary">
                                    <i class="mdi mdi-file-document-box-plus"></i> Táº¡o Há»£p Äá»ng má»i
                                </a>
                            </div>

                            <?php if ($message): ?> <div class="alert alert-success"><?= $message ?></div> <?php endif; ?>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>MÃ£ HÄ</th>
                                            <th>GiÃ¡o viÃªn</th>
                                            <th>Tá»· lá» chia sáº»</th>
                                            <th>Hiá»u lá»±c</th>
                                            <th>File scan</th>
                                            <th>Tráº¡ng thÃ¡i</th>
                                            <th>HÃ nh Äá»ng</th>
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
                                                Äáº¿n <?= date('d/m/Y', strtotime($row['end_date'])) ?>
                                            </td>
                                            <td>
                                                <?php if(!empty($row['file_path'])): ?>
                                                    <a href="/qlkh/<?= $row['file_path'] ?>" target="_blank" class="btn btn-inverse-info btn-sm icon-btn">
                                                        <i class="mdi mdi-file-pdf"></i> Xem
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">ChÆ°a upload</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($row['status']=='active'): ?>
                                                    <label class="badge badge-success">Hiá»u lá»±c</label>
                                                <?php else: ?>
                                                    <label class="badge badge-danger">Háº¿t háº¡n</label>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="DeleteHopDongGV.php?id=<?= $row['id'] ?>" class="btn btn-inverse-danger btn-sm" onclick="return confirm('Xóa há»£p Äá»ng nÃ y?')">
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
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/footer.php"; ?>
    </div>
</div>
