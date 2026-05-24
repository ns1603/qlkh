<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 'admins' && $_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'teacher')) {
    header("Location: ../../index.php"); 
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];

$sql = "SELECT lessons.*, courses.title as course_name 
        FROM lessons 
        JOIN courses ON lessons.course_id = courses.id";

if ($role === 'teacher') {
    $sql .= " WHERE courses.teacher_id = $user_id";
}

$sql .= " ORDER BY lessons.created_at DESC";

$result = $conn->query($sql);

// Xá»­ lÃ½ thÃ´ng bÃ¡o (Flash message)
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
                <h3 class="page-title"> Quáº£n lÃ½ BÃ i giáº£ng (Video) </h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Danh sÃ¡ch bÃ i giáº£ng</li>
                    </ol>
                </nav>
            </div>

            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title">Danh sÃ¡ch Video bÃ i há»c (<?= $result ? $result->num_rows : 0 ?>)</h4>
                                <?php if ($role != 'admins'): ?>
                                <a href="AddBaiGiang.php" class="btn btn-sm btn-gradient-primary">
                                    <i class="mdi mdi-video-plus"></i> Thêm bÃ i giáº£ng má»i
                                </a>
                                <?php endif; ?>
                            </div>

                            <?php if ($message): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?= $message ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>TÃªn BÃ i Giáº£ng</th>
                                            <th>Thuá»c KhÃ³a Há»c</th>
                                            <th>Video / TÃ i liá»u</th>
                                            <th>NgÃ y ÄÄng</th>
                                            <th>HÃ nh Äá»ng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($result && $result->num_rows > 0): ?>
                                            <?php while($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $row['id'] ?></td>
                                                
                                                <td style="max-width: 250px; white-space: normal;">
                                                    <strong><?= htmlspecialchars($row['title']) ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?= htmlspecialchars(substr(strip_tags($row['content']), 0, 50)) ?>...
                                                    </small>
                                                </td>

                                                <td>
                                                    <span class="badge badge-outline-info">
                                                        <?= htmlspecialchars($row['course_name']) ?>
                                                    </span>
                                                </td>

                                                <td>
                                                    <?php if (!empty($row['video_url'])): ?>
                                                        <a href="<?= htmlspecialchars($row['video_url']) ?>" target="_blank" class="btn btn-inverse-danger btn-sm btn-rounded" title="Xem Video">
                                                            <i class="mdi mdi-youtube"></i> Xem
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted text-small">KhÃ´ng cÃ³ video</span>
                                                    <?php endif; ?>

                                                    <?php if (!empty($row['attachment'])): ?>
                                                        <a href="/qlkh/<?= htmlspecialchars($row['attachment']) ?>" target="_blank" class="ms-2 text-primary" title="Táº£i tÃ i liá»u">
                                                            <i class="mdi mdi-paperclip"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </td>

                                                <td>
                                                    <?= date('d/m/Y', strtotime($row['created_at'])) ?>
                                                </td>

                                                <td>
                                                    <?php if ($role != 'admins'): ?>
                                                    <a href="EditBaiGiang.php?id=<?= $row['id'] ?>" class="btn btn-inverse-warning btn-sm btn-icon" title="Sá»­a">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </a>
                                                    <a href="DeleteBaiGiang.php?id=<?= $row['id'] ?>" 
                                                       class="btn btn-inverse-danger btn-sm btn-icon"
                                                       onclick="return confirm('Báº¡n cÃ³ cháº¯c cháº¯n muá»n xÃ³a bÃ i giáº£ng nÃ y khÃ´ng?');" 
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
                                                    ChÆ°a cÃ³ bÃ i giáº£ng nÃ o. HÃ£y táº¡o KhÃ³a há»c trÆ°á»c rá»i thÃªm BÃ i giáº£ng!
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
</div>s
