<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 'admins' && $_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'teacher')) {
    header("Location: ../../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];

$sql = "SELECT q.*, c.title as course_name 
        FROM quizzes q 
        JOIN courses c ON q.course_id = c.id";

if ($role == 'teacher') {
    $sql .= " WHERE c.teacher_id = $user_id";
}

$sql .= " ORDER BY q.id DESC";
$result = $conn->query($sql);

$message = isset($_SESSION['status_message']) ? $_SESSION['status_message'] : '';
unset($_SESSION['status_message']);
?>

<?php 
include ROOT_PATH . "/admin/header.php"; 
include ROOT_PATH . "/admin/navbar.php"; 
?>

<div class="container-fluid page-body-wrapper">
    <?php include ROOT_PATH . "/admin/sidebar.php"; ?>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title"> Quáº£n lÃ½ Äá» thi (Quiz) </h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Danh sÃ¡ch Äá» thi</li>
                    </ol>
                </nav>
            </div>

            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title">Danh sÃ¡ch Äá» thi (<?= $result ? $result->num_rows : 0 ?>)</h4>
                                <?php if ($role != 'admins'): ?>
                                <a href="AddDeThi.php" class="btn btn-sm btn-gradient-primary">
                                    <i class="mdi mdi-plus-box"></i> Táº¡o Äá» thi má»i
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
                                            <th>TÃªn Äá» Thi</th>
                                            <th>Thuá»c KhÃ³a Há»c</th>
                                            <th>Thá»i gian</th>
                                            <th>CÃ¢u há»i</th>
                                            <th>HÃ nh Äá»ng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($result && $result->num_rows > 0): ?>
                                            <?php while($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $row['id'] ?></td>
                                                <td><strong><?= htmlspecialchars($row['title']) ?></strong></td>
                                                <td>
                                                    <span class="badge badge-outline-info">
                                                        <?= htmlspecialchars($row['course_name']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if (isset($row['time_limit']) && $row['time_limit'] > 0): ?>
                                                        <span class="badge badge-outline-success">
                                                            <i class="mdi mdi-timer"></i> <?= $row['time_limit'] ?> phÃºt
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge badge-outline-warning">
                                                            <i class="mdi mdi-timer-off"></i> ChÆ°a thiáº¿t láº­p
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="ViewKetQua.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm btn-icon-text" title="Xem danh sÃ¡ch Äiá»m">
                                                        <i class="mdi mdi-chart-bar btn-icon-prepend"></i> Xem Äiá»m
                                                    </a>

                                                    <a href="../CauHoi/ListCauHoi.php?quiz_id=<?= $row['id'] ?>" class="btn btn-success btn-sm btn-icon-text" title="Soáº¡n cÃ¢u há»i">
                                                        <i class="mdi mdi-playlist-plus btn-icon-prepend"></i> CÃ¢u há»i
                                                    </a>

                                                    <?php if ($role != 'admins'): ?>
                                                    <a href="EditDeThi.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm btn-icon">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </a>
                                                    
                                                    <a href="DeleteDeThi.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm btn-icon" onclick="return confirm('Xóa Äá» thi nÃ y?')">
                                                        <i class="mdi mdi-delete"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr><td colspan="6" class="text-center p-4">ChÆ°a cÃ³ Äá» thi nÃ o.</td></tr>
                                        <?php endif; ?>
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
