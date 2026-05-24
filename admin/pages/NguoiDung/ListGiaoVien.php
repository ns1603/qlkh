<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 'admins' && $_SESSION['user_role'] != 'admin')) {
    die("Truy cáº­p bá» tá»« chá»i! Chá» Admin má»i ÄÆ°á»£c quáº£n lÃ½ GiÃ¡o viÃªn.");
}

$sql = "SELECT * FROM users WHERE role = 'teacher' OR role = 'admin' ORDER BY id DESC";
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
                <h3 class="page-title"> Quáº£n lÃ½ GiÃ¡o viÃªn </h3>
            </div>
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title">Danh sÃ¡ch GiÃ¡o viÃªn (<?= $result->num_rows ?>)</h4>
                                <a href="AddGiaoVien.php" class="btn btn-sm btn-gradient-danger">
                                    <i class="mdi mdi-account-plus"></i> Thêm GiÃ¡o viÃªn
                                </a>
                            </div>
                            <?php if ($message): ?> <div class="alert alert-success"><?= $message ?></div> <?php endif; ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Há» tÃªn</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>HÃ nh Äá»ng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $row['id'] ?></td>
                                            <td><?= htmlspecialchars($row['fullname']) ?></td>
                                            <td><?= htmlspecialchars($row['email']) ?></td>
                                            <td><label class="badge badge-info"><?= $row['role'] ?></label></td>
                                            <td>
                                                <?php if ($_SESSION['user_role'] != 'admins'): ?>
                                                <a href="EditGiaoVien.php?id=<?= $row['id'] ?>" class="btn btn-inverse-warning btn-sm btn-icon"><i class="mdi mdi-pencil"></i></a>
                                                <a href="DeleteGiaoVien.php?id=<?= $row['id'] ?>" class="btn btn-inverse-danger btn-sm btn-icon" onclick="return confirm('Xóa giÃ¡o viÃªn nÃ y?')"><i class="mdi mdi-delete"></i></a>
                                                <?php else: ?>
                                                <span class="text-muted small">Read-only</span>
                                                <?php endif; ?>
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
