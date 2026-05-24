<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role'])) { header("Location: ../../index.php"); exit; }

$role = $_SESSION['user_role'];
$isAdmin = ($role == 'admin'); // Chá» admin toÃ n quyá»n má»i ÄÆ°á»£c sá»­a/xÃ³a

$sql = "SELECT * FROM users WHERE role = 'student' ORDER BY id DESC";
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
                <h3 class="page-title"> Quáº£n lÃ½ Há»c viÃªn </h3>
            </div>
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title">Danh sÃ¡ch Há»c viÃªn (<?= $result->num_rows ?>)</h4>
                                
                                <?php if ($isAdmin): ?>
                                <a href="AddHocVien.php" class="btn btn-sm btn-gradient-primary">
                                    <i class="mdi mdi-account-plus"></i> Thêm má»i
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
                                            <th>Há» vÃ  TÃªn</th>
                                            <th>Email</th>
                                            <th>NgÃ y tham gia</th>
                                            <?php if ($isAdmin): ?>
                                            <th>HÃ nh Äá»ng</th>
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
                                                <a href="EditHocVien.php?id=<?= $row['id'] ?>" class="btn btn-inverse-warning btn-sm btn-icon" title="Sá»­a">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <a href="DeleteHocVien.php?id=<?= $row['id'] ?>" 
                                                   class="btn btn-inverse-danger btn-sm btn-icon"
                                                   onclick="return confirm('Xóa há»c viÃªn nÃ y sáº½ xÃ³a toÃ n bá» lá»ch sá»­ há»c táº­p. Tiáº¿p tá»¥c?')"
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
        <?php include ROOT_PATH . "/admin/footer.php"; ?>
    </div>
</div>
