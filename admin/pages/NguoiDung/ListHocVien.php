<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role'])) { header("Location: ../../index.php"); exit; }

$role = $_SESSION['user_role'];
$isAdmin = ($role == 'admin'); // Chỉ admin toàn quyền mới được sửa/xóa

$sql = "SELECT * FROM users WHERE role = 'student' ORDER BY id DESC";
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
                <h3 class="page-title"> Quản lý Học viên </h3>
            </div>
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title">Danh sách Học viên (<?= $result->num_rows ?>)</h4>
                                
                                <?php if ($isAdmin): ?>
                                <a href="AddHocVien.php" class="btn btn-sm btn-gradient-primary">
                                    <i class="mdi mdi-account-plus"></i> Thêm mới
                                </a>
                                <?php endif; ?>
                            </div>

                            <?php if ($message): ?>
                                <div class="alert alert-success"><?= $message ?></div>
                            <?php endif; ?>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Họ và Tên</th>
                                            <th>Email</th>
                                            <th>Ngày tham gia</th>
                                            <?php if ($isAdmin): ?>
                                            <th>Hành động</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $row['id'] ?></td>
                                            <td>
                                                <img src="../../assets/images/faces/face1.jpg" class="me-2" alt="image">
                                                <?= htmlspecialchars($row['fullname']) ?>
                                            </td>
                                            <td><?= htmlspecialchars($row['email']) ?></td>
                                            <td><?= date('d/m/Y', strtotime($row['created_at'] ?? 'now')) ?></td>
                                            
                                            <?php if ($isAdmin): ?>
                                            <td>
                                                <a href="EditHocVien.php?id=<?= $row['id'] ?>" class="btn btn-inverse-warning btn-sm btn-icon" title="Sửa">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <a href="DeleteHocVien.php?id=<?= $row['id'] ?>" 
                                                   class="btn btn-inverse-danger btn-sm btn-icon"
                                                   onclick="return confirm('Xóa học viên này sẽ xóa toàn bộ lịch sử học tập. Tiếp tục?')"
                                                   title="Xóa">
                                                    <i class="mdi mdi-delete"></i>
                                                </a>
                                            </td>
                                            <?php endif; ?>
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
