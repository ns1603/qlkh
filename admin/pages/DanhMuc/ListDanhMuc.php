<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 'admins' && $_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'teacher')) {
    header("Location: ../../index.php"); 
    exit;
}

$role = $_SESSION['user_role'];
$sql = "SELECT * FROM categories ORDER BY id DESC";
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
                <h3 class="page-title"> Quáº£n lÃ½ Danh má»¥c </h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Danh má»¥c khÃ³a há»c</li>
                    </ol>
                </nav>
            </div>

            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title">Danh sÃ¡ch Danh má»¥c (<?= $result ? $result->num_rows : 0 ?>)</h4>
                                <?php if ($role != 'admins'): ?>
                                <a href="AddDanhMuc.php" class="btn btn-sm btn-gradient-primary">
                                    <i class="mdi mdi-plus-box"></i> Thêm má»i
                                </a>
                                <?php endif; ?>
                            </div>

                            <?php if ($message): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?= $message ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>TÃªn Danh má»¥c</th>
                                            <th>Slug (ÄÆ°á»ng dáº«n)</th>
                                            <th>MÃ´ táº£</th>
                                            <th>NgÃ y táº¡o</th>
                                            <th>HÃ nh Äá»ng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($result && $result->num_rows > 0): ?>
                                            <?php while($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $row['id'] ?></td>
                                                <td>
                                                    <strong><?= htmlspecialchars($row['name']) ?></strong>
                                                </td>
                                                <td>
                                                    <span class="badge badge-outline-info">
                                                        <?= htmlspecialchars($row['slug']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?= htmlspecialchars(substr($row['description'], 0, 50)) . (strlen($row['description']) > 50 ? '...' : '') ?>
                                                </td>
                                                <td>
                                                    <?= date('d/m/Y', strtotime($row['created_at'])) ?>
                                                </td>
                                                <td>
                                                    <?php if ($role != 'admins'): ?>
                                                    <a href="EditDanhMuc.php?id=<?= $row['id'] ?>" class="btn btn-inverse-warning btn-sm btn-icon" title="Sá»­a">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </a>
                                                    
                                                    <a href="DeleteDanhMuc.php?id=<?= $row['id'] ?>" 
                                                       class="btn btn-inverse-danger btn-sm btn-icon" 
                                                       onclick="return confirm('Báº¡n cÃ³ cháº¯c muá»n xÃ³a danh má»¥c nÃ y? CÃ¡c khÃ³a há»c thuá»c danh má»¥c nÃ y sáº½ bá» máº¥t liÃªn káº¿t!');" 
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
                                            <tr>
                                                <td colspan="6" class="text-center p-4">
                                                    <p class="text-muted">ChÆ°a cÃ³ danh má»¥c nÃ o. HÃ£y báº¥m "Thêm má»i"!</p>
                                                </td>
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
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/qlkh/admin/footer.php"; ?>
    </div>
    </div>
