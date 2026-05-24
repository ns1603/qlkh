<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_id'])) {
    die("Truy cập bị từ chối!");
}

$user_id = (int)$_SESSION['user_id'];

/*
 Lấy thông báo:
 - Ghim lên đầu
 - Ưu tiên trước
 - Mới nhất trước
*/
$sql = "
    SELECT 
        n.id,
        n.title,
        n.message,
        n.priority,
        n.is_pinned,
        n.created_at,
        n.sender_type,

        a.username  AS admin_name,
        u.fullname  AS user_name,
        u.role      AS user_role

    FROM notifications n

    LEFT JOIN admins a
        ON n.sender_type = 'admin'
       AND n.sender_id   = a.id

    LEFT JOIN users u
        ON n.sender_type = 'user'
       AND n.sender_id   = u.id

    WHERE n.receiver_id = ?

    ORDER BY 
        n.is_pinned DESC,
        n.priority DESC,
        n.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php include ROOT_PATH . "/admin/header.php"; ?>
<?php include ROOT_PATH . "/admin/navbar.php"; ?>

<div class="container-fluid page-body-wrapper">
<?php include ROOT_PATH . "/admin/sidebar.php"; ?>

<div class="main-panel">
<div class="content-wrapper">

<h3 class="mb-4">🔔 Thông báo của bạn</h3>

<?php if ($result->num_rows === 0): ?>
    <div class="alert alert-info">Bạn chưa có thông báo nào.</div>
<?php endif; ?>

<?php while ($row = $result->fetch_assoc()): ?>

<?php
    /* ===== XÁC ĐỊNH NGƯỜI GỬI ===== */
    if ($row['sender_type'] === 'admin') {
        $senderName = 'Hệ thống';
        $roleBadge  = '<span class="badge badge-danger">ADMIN</span>';
    } else {
        $senderName = htmlspecialchars($row['user_name'] ?? 'Không xác định');

        if ($row['user_role'] === 'teacher') {
            $roleBadge = '<span class="badge badge-primary">GIÁO VIÊN</span>';
        } else {
            $roleBadge = '<span class="badge badge-secondary">HỆ THỐNG</span>';
        }
    }

    /* ===== ƯU TIÊN ===== */
    $priorityBadge = '';
    if ($row['priority'] === 'important') {
        $priorityBadge = '<span class="badge badge-warning ms-2">ƯU TIÊN</span>';
    }

    /* ===== GHIM ===== */
    $pinIcon = $row['is_pinned'] ? '📌 ' : '';
?>

<div class="card mb-3 <?= $row['priority'] === 'important' ? 'border-warning' : '' ?>">
    <div class="card-body">

        <h5 class="card-title">
            <?= $pinIcon ?>
            <?= htmlspecialchars($row['title']) ?>
            <?= $priorityBadge ?>
        </h5>

        <p class="text-muted mb-1">
            <?= $roleBadge ?>
            Gửi bởi <strong><?= $senderName ?></strong>
            • <?= date("d/m/Y H:i", strtotime($row['created_at'])) ?>
        </p>

        <p class="card-text">
            <?= nl2br(htmlspecialchars($row['message'])) ?>
        </p>

    </div>
</div>

<?php endwhile; ?>

</div>
<?php include ROOT_PATH . "/admin/footer.php"; ?>
</div>
</div>
