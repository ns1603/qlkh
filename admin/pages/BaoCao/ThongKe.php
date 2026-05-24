<?php
session_start();
include(__DIR__ . '/../../../config.php');

if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 'admins' && $_SESSION['user_role'] != 'admin')) {
    die("Bạn không có quyền xem báo cáo!");
}

$start_date = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-01');
$end_date   = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');
$cost_percent = isset($_GET['cost_percent']) ? intval($_GET['cost_percent']) : 0; // % Chi phí ước tính (Mặc định 0%)


$sql_filter = "SELECT SUM(total_amount) as total_revenue 
               FROM orders 
               WHERE status = 'completed' 
               AND created_at BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'";

$result_filter = $conn->query($sql_filter)->fetch_assoc();
$revenue_filtered = $result_filter['total_revenue'] ?? 0;

$total_cost = $revenue_filtered * ($cost_percent / 100);
$net_profit = $revenue_filtered - $total_cost;
$profit_margin = ($revenue_filtered > 0) ? round(($net_profit / $revenue_filtered) * 100, 1) : 0;

$current_year = date('Y');
$monthly_revenue = array_fill(1, 12, 0);
$sql_month = "SELECT MONTH(created_at) as month, SUM(total_amount) as total 
              FROM orders 
              WHERE status = 'completed' AND YEAR(created_at) = $current_year 
              GROUP BY MONTH(created_at)";
$res_month = $conn->query($sql_month);
while($row = $res_month->fetch_assoc()) {
    $monthly_revenue[$row['month']] = $row['total'];
}
$js_months = json_encode(array_values($monthly_revenue));
?>

<?php include ROOT_PATH . "/admin/header.php"; ?>
<?php include ROOT_PATH . "/admin/navbar.php"; ?>

<div class="container-fluid page-body-wrapper">
    <?php include ROOT_PATH . "/admin/sidebar.php"; ?>
    <div class="main-panel">
        <div class="content-wrapper">
            
            <div class="page-header">
                <h3 class="page-title"> Báo cáo Doanh thu & Lợi nhuận </h3>
            </div>

            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body py-3">
                            <form method="GET" class="row align-items-end">
                                <div class="col-md-3">
                                    <label class="font-weight-bold">Từ ngày:</label>
                                    <input type="date" name="from_date" class="form-control" value="<?= $start_date ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="font-weight-bold">Đến ngày:</label>
                                    <input type="date" name="to_date" class="form-control" value="<?= $end_date ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="font-weight-bold">Chi phí ước tính (%):</label>
                                    <input type="number" name="cost_percent" class="form-control" value="<?= $cost_percent ?>" min="0" max="100" placeholder="VD: 30% trả GV">
                                    <small class="text-muted">Chi phí Maketting, Trả GV...</small>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-gradient-primary btn-block w-100">
                                        <i class="mdi mdi-filter"></i> Xem kết quả
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 stretch-card grid-margin">
                    <div class="card bg-gradient-info card-img-holder text-white">
                        <div class="card-body">
                            <h4 class="font-weight-normal mb-3">Tổng Doanh Thu <i class="mdi mdi-chart-line mdi-24px float-right"></i></h4>
                            <h2 class="mb-5"><?= number_format($revenue_filtered) ?> đ</h2>
                            <h6 class="card-text">Giai đoạn: <?= date('d/m', strtotime($start_date)) ?> - <?= date('d/m', strtotime($end_date)) ?></h6>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 stretch-card grid-margin">
                    <div class="card bg-gradient-danger card-img-holder text-white">
                        <div class="card-body">
                            <h4 class="font-weight-normal mb-3">Chi Phí (<?= $cost_percent ?>%) <i class="mdi mdi-calculator mdi-24px float-right"></i></h4>
                            <h2 class="mb-5">- <?= number_format($total_cost) ?> đ</h2>
                            <h6 class="card-text">Chi phí vận hành ước tính</h6>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 stretch-card grid-margin">
                    <div class="card bg-gradient-success card-img-holder text-white">
                        <div class="card-body">
                            <h4 class="font-weight-normal mb-3">Lãi Ròng (Lợi nhuận) <i class="mdi mdi-cash-multiple mdi-24px float-right"></i></h4>
                            <h2 class="mb-5"><?= number_format($net_profit) ?> đ</h2>
                            <h6 class="card-text">Tỷ suất lãi: <strong><?= $profit_margin ?>%</strong></h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Xu hướng doanh thu năm <?= $current_year ?></h4>
                            <canvas id="revenueChart" style="height:250px"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <?php include ROOT_PATH . "/admin/footer.php"; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: <?= $js_months ?>,
                borderColor: '#b66dff',
                backgroundColor: 'rgba(182, 109, 255, 0.2)',
                borderWidth: 3,
                fill: true,
                tension: 0.3
            }]
        },
        options: { responsive: true }
    });
</script>
