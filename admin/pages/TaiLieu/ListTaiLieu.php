<?php
session_start();
include(__DIR__ . '/../../../config.php');
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 'admins' && $_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'teacher')) {
    header("Location: ../../index.php"); 
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];

$sql = "SELECT m.*, l.title as lesson_name, c.title as course_name 
        FROM lesson_materials m
        JOIN lessons l ON m.lesson_id = l.id 
        JOIN courses c ON l.course_id = c.id";

if ($role === 'teacher') {
    $sql .= " WHERE c.teacher_id = $user_id";
}

$sql .= " ORDER BY m.uploaded_at DESC";

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
                <h3 class="page-title"> Quáº£n lÃ½ TÃ i liá»u ÄÃ­nh kÃ¨m </h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Danh sÃ¡ch tÃ i liá»u</li>
                    </ol>
                </nav>
            </div>

            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title">Kho tÃ i liá»u (<?= $result ? $result->num_rows : 0 ?>)</h4>
                                <a href="AddTaiLieu.php" class="btn btn-sm btn-gradient-info">
                                    <i class="mdi mdi-cloud-upload"></i> Upload tÃ i liá»u má»i
                                </a>
                            </div>

                            <?php if ($message): ?>
                                <div class="alert alert-success alert-dismissible fade show">
                                    <?= $message ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>TÃªn File</th>
                                            <th>Thuá»c BÃ i giáº£ng</th>
                                            <th>KhÃ³a há»c</th>
                                            <th>NgÃ y upload</th>
                                            <th>HÃ nh Äá»ng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($result && $result->num_rows > 0): ?>
                                            <?php while($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $row['id'] ?></td>
                                                
                                                <td>
                                                    <i class="mdi mdi-file-document text-primary me-2"></i>
                                                    <strong><?= htmlspecialchars($row['file_name']) ?></strong>
                                                </td>

                                                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                                                    <?= htmlspecialchars($row['lesson_name']) ?>
                                                </td>

                                                <td>
                                                    <span class="badge badge-outline-secondary">
                                                        <?= htmlspecialchars($row['course_name']) ?>
                                                    </span>
                                                </td>

                                                <td>
                                                    <?= date('d/m/Y H:i', strtotime($row['uploaded_at'])) ?>
                                                </td>

                                                <td>
                                                    <a href="<?= BASE_PATH ?>/<?= htmlspecialchars($row['file_path']) ?>" target="_blank" class="btn btn-inverse-success btn-sm btn-icon" title="Táº£i xuá»ng">
                                                        <i class="mdi mdi-download"></i>
                                                    </a>
                                                    
                                                    <a href="DeleteTaiLieu.php?id=<?= $row['id'] ?>" 
                                                       class="btn btn-inverse-danger btn-sm btn-icon"
                                                       onclick="return confirm('Báº¡n cháº¯c cháº¯n muá»n xÃ³a file nÃ y vÄ©nh viá»n?');" 
                                                       title="Xóa file">
                                                        <i class="mdi mdi-delete"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center p-4">
                                                    ChÆ°a cÃ³ tÃ i liá»u nÃ o. HÃ£y upload file cho cÃ¡c bÃ i giáº£ng!
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
        <?php include ROOT_PATH . "/admin/footer.php"; ?>
    </div>
</div>
