<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_id'])) {
    die("Truy cáº­p bá» tá»« chá»i!");
}

$user_id = (int)$_SESSION['user_id'];

/*
 Láº¥y thÃ´ng bÃ¡o:
 - Ghim lÃªn Äáº§u
 - Æ¯u tiÃªn trÆ°á»c
 - Má»i nháº¥t trÆ°á»c
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

<h3 class="mb-4">ð ThÃ´ng bÃ¡o cá»§a báº¡n</h3>

<?php if ($result->num_rows === 0): ?>
    <div class="alert alert-info">Báº¡n chÆ°a cÃ³ thÃ´ng bÃ¡o nÃ o.</div>
<?php endif; ?>

<?php while ($row = $result->fetch_assoc()): ?>

<?php
    /* ===== XÃC Äá»NH NGÆ¯á»I Gá»¬I ===== */
    if ($row['sender_type'] === 'admin') {
        $senderName = 'Há» thá»ng';
        $roleBadge  = '<span class="badge badge-danger">ADMIN</span>';
    } else {
        $senderName = htmlspecialchars($row['user_name'] ?? 'KhÃ´ng xÃ¡c Äá»nh');

        if ($row['user_role'] === 'teacher') {
            $roleBadge = '<span class="badge badge-primary">GIÃO VIÃN</span>';
        } else {
            $roleBadge = '<span class="badge badge-secondary">Há» THá»NG</span>';
        }
    }

    /* ===== Æ¯U TIÃN ===== */
    $priorityBadge = '';
    if ($row['priority'] === 'important') {
        $priorityBadge = '<span class="badge badge-warning ms-2">Æ¯U TIÃN</span>';
    }

    /* ===== GHIM ===== */
    $pinIcon = $row['is_pinned'] ? 'ð ' : '';
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
            Gá»­i bá»i <strong><?= $senderName ?></strong>
            â¢ <?= date("d/m/Y H:i", strtotime($row['created_at'])) ?>
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
