<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role'])) { header("Location: ../../index.php"); exit; }

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];

$sql = "SELECT cmt.id, cmt.comment_text, cmt.created_at, 
               u.fullname, 
               l.title as lesson_name, 
               c.title as course_name, c.teacher_id
        FROM comments cmt
        JOIN users u ON cmt.user_id = u.id
        JOIN lessons l ON cmt.lesson_id = l.id
        JOIN courses c ON l.course_id = c.id";

if ($role == 'teacher') {
    $sql .= " WHERE c.teacher_id = $user_id";
}

$sql .= " ORDER BY cmt.created_at DESC";
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
                <h3 class="page-title"> Quáº£n lÃ½ BÃ¬nh luáº­n & Há»i ÄÃ¡p </h3>
            </div>
            
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Danh sÃ¡ch bÃ¬nh luáº­n (<?= $result ? $result->num_rows : 0 ?>)</h4>
                            
                            <?php if ($message): ?>
                                <div class="alert alert-success"><?= $message ?></div>
                            <?php endif; ?>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>NgÆ°á»i gá»­i</th>
                                            <th>Ná»i dung</th>
                                            <th>Táº¡i bÃ i há»c</th>
                                            <th>Thá»i gian</th>
                                            <th>HÃ nh Äá»ng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($result && $result->num_rows > 0): ?>
                                            <?php while($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td>
                                                    <img src="../../assets/images/faces/face1.jpg" class="me-2" alt="image">
                                                    <?= htmlspecialchars($row['fullname']) ?>
                                                </td>
                                                
                                                <td style="max-width: 300px; white-space: normal;">
                                                    <p class="mb-0 text-muted">
                                                        <?= nl2br(htmlspecialchars($row['comment_text'])) ?>
                                                    </p>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($row['lesson_name']) ?>
                                                    <br>
                                                    <small class="text-muted">KhÃ³a: <?= htmlspecialchars($row['course_name']) ?></small>
                                                </td>

                                                <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                                                
                                                <td>
                                                    <?php if ($role != 'admins'): ?>
                                                    <a href="DeleteBinhLuan.php?id=<?= $row['id'] ?>" 
                                                       class="btn btn-inverse-danger btn-sm btn-icon"
                                                       onclick="return confirm('Xóa bÃ¬nh luáº­n nÃ y?')"
                                                       title="Xóa spam">
                                                        <i class="mdi mdi-delete"></i>
                                                    </a>
                                                    <?php else: ?>
                                                    <span class="text-muted small">Read-only</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr><td colspan="5" class="text-center p-4">ChÆ°a cÃ³ bÃ¬nh luáº­n nÃ o.</td></tr>
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
