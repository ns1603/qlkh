<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (
    !isset($_SESSION['user_role']) || 
    !in_array($_SESSION['user_role'], ['admins', 'admin', 'teacher'])
) {
    header("Location: ../../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];

$sql = "SELECT c.*, u.fullname AS teacher_name 
        FROM courses c 
        LEFT JOIN users u ON c.teacher_id = u.id";

if ($role === 'teacher') {
    $sql .= " WHERE c.teacher_id = $user_id";
}

$sql .= " ORDER BY c.created_at DESC";
$result = $conn->query($sql);
if (!$result) die("Lá»i SQL: " . $conn->error);

$message = $_SESSION['status_message'] ?? '';
unset($_SESSION['status_message']);
?>

<?php
include ROOT_PATH . "/admin/header.php";
include ROOT_PATH . "/admin/navbar.php";
?>

<style>
.table td, .table th {
    vertical-align: middle;
}

.course-thumb {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,.15);
}

.action-btn {
    width: 32px;
    height: 32px;
    padding: 0;
}
</style>

<div class="main-panel">
    <div class="content-wrapper">

        <div class="page-header">
            <h3 class="page-title">ð Quáº£n lÃ½ KhÃ³a há»c</h3>
        </div>
        

        <?php if ($message): ?>
            <div class="alert alert-success"><?= $message ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card shadow-sm">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h4 class="card-title mb-1">Danh sÃ¡ch khÃ³a há»c</h4>
                                <p class="text-muted mb-0">Tá»ng cá»ng <?= $result->num_rows ?> khÃ³a há»c</p>
                            </div>
                            <?php if ($role != 'admins'): ?>
                            <a href="AddKhoaHoc.php" class="btn btn-gradient-primary btn-sm">
                                <i class="mdi mdi-plus-circle-outline"></i> Thêm khÃ³a há»c
                            </a>
                            <?php endif; ?>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>áº¢nh</th>
                                        <th>TÃªn khÃ³a há»c</th>
                                        <th>Giáº£ng viÃªn</th>
                                        <th>GiÃ¡</th>
                                        <th>Tráº¡ng thÃ¡i</th>
                                        <th class="text-center">HÃ nh Äá»ng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while($row = $result->fetch_assoc()): ?>
                                        <?php
                                            $stt = $row['status'];
                                            $statusText = [
                                                'published' => 'Äang má»',
                                                'draft' => 'Báº£n nhÃ¡p',
                                                'archived' => 'ÄÃ£ áº©n'
                                            ];
                                            $badge = [
                                                'published' => 'success',
                                                'draft' => 'secondary',
                                                'archived' => 'warning'
                                            ];
                                        ?>
                                        <tr>
                                            <td><?= $row['id'] ?></td>

                                            <td>
                                                <?php if (!empty($row['thumbnail'])): ?>
                                                    <img src="<?= BASE_PATH ?>/<?= $row['thumbnail'] ?>" class="course-thumb">
                                                <?php else: ?>
                                                    <i class="mdi mdi-image-off-outline mdi-36px text-muted"></i>
                                                <?php endif; ?>
                                            </td>

                                            <td style="max-width: 220px;">
                                                <strong><?= htmlspecialchars($row['title']) ?></strong>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($row['teacher_name'] ?? 'ChÆ°a cáº­p nháº­t') ?>
                                            </td>

                                            <td>
                                                <?php if ($row['price'] == 0): ?>
                                                    <span class="badge badge-success">Miá»n phÃ­</span>
                                                <?php else: ?>
                                                    <span class="font-weight-bold text-danger">
                                                        <?= number_format($row['price'], 0, ',', '.') ?> Ä
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <span class="badge badge-<?= $badge[$stt] ?>">
                                                    <?= $statusText[$stt] ?>
                                                </span>
                                            </td>

                                            <td class="text-center">
                                                <?php if ($role != 'admins'): ?>
                                                <a href="EditKhoaHoc.php?id=<?= $row['id'] ?>"
                                                   class="btn btn-outline-info btn-sm action-btn"
                                                   title="Sá»­a">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>

                                                <a href="DeleteKhoaHoc.php?id=<?= $row['id'] ?>"
                                                   class="btn btn-outline-danger btn-sm action-btn"
                                                   onclick="return confirm('Xóa khÃ³a há»c nÃ y?')"
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
                                        <td colspan="7" class="text-center">
                                            <div class="py-5">
                                                <i class="mdi mdi-book-open-page-variant mdi-48px text-muted"></i>
                                                <p class="mt-2 text-muted">
                                                    ChÆ°a cÃ³ khÃ³a há»c nÃ o. Thêm ngay cho vui ð
                                                </p>
                                            </div>
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
