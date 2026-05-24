<?php
session_start();
include(__DIR__ . '/../../../config.php');
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'teacher') {
    die("Truy cáº­p bá» tá»« chá»i!");
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM teacher_contracts WHERE teacher_id = $user_id ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<?php include ROOT_PATH . "/admin/header.php"; ?>
<?php include ROOT_PATH . "/admin/navbar.php"; ?>

<div class="container-fluid page-body-wrapper">
    <?php include ROOT_PATH . "/admin/sidebar.php"; ?>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title"> Há»£p Äá»ng lao Äá»ng </h3>
            </div>
            
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Danh sÃ¡ch Há»£p Äá»ng cá»§a tÃ´i</h4>
                            <p class="card-description">ThÃ´ng tin chi tiáº¿t vá» thá»a thuáº­n há»£p tÃ¡c.</p>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>MÃ£ Há»£p Äá»ng</th>
                                            <th>Tá»· lá» chia sáº»</th>
                                            <th>Thá»i háº¡n</th>
                                            <th>Tráº¡ng thÃ¡i</th>
                                            <th>TÃ i liá»u</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($result && $result->num_rows > 0): ?>
                                            <?php while($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($row['contract_code']) ?></strong></td>
                                                
                                                <td class="text-success fw-bold">
                                                    <?= $row['revenue_share'] ?>% (GiÃ¡o viÃªn nháº­n)
                                                </td>
                                                
                                                <td>
                                                    Tá»«: <?= date('d/m/Y', strtotime($row['start_date'])) ?> <br>
                                                    Äáº¿n: <?= date('d/m/Y', strtotime($row['end_date'])) ?>
                                                </td>
                                                
                                                <td>
                                                    <?php 
                                                        $today = date('Y-m-d');
                                                        if ($row['end_date'] < $today) {
                                                            echo '<label class="badge badge-danger">ÄÃ£ háº¿t háº¡n</label>';
                                                        } elseif ($row['status'] == 'active') {
                                                            echo '<label class="badge badge-success">Äang hiá»u lá»±c</label>';
                                                        } else {
                                                            echo '<label class="badge badge-warning">ÄÃ£ cháº¥m dá»©t</label>';
                                                        }
                                                    ?>
                                                </td>
                                                
                                                <td>
                                                    <?php if(!empty($row['file_path'])): ?>
                                                        <a href="<?= BASE_PATH ?>/<?= $row['file_path'] ?>" target="_blank" class="btn btn-gradient-info btn-sm">
                                                            <i class="mdi mdi-download"></i> Táº£i vá» / Xem
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted">KhÃ´ng cÃ³ file</span>
                                                    <?php endif; ?>
                                                </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center">Báº¡n chÆ°a cÃ³ há»£p Äá»ng nÃ o. Vui lÃ²ng liÃªn há» Admin.</td>
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
