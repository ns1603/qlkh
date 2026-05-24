<?php
session_start();
include 'config.php';

$alert_type = "";
$alert_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $first_name = trim($_POST['first-name'] ?? '');
    $last_name  = trim($_POST['last-name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $message    = trim($_POST['message'] ?? '');

    // Validate dữ liệu
    if (
        empty($first_name) ||
        empty($last_name) ||
        empty($email) ||
        empty($message)
    ) {

        $alert_type = "error";
        $alert_message = "Vui lòng nhập đầy đủ thông tin!";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $alert_type = "error";
        $alert_message = "Email không hợp lệ!";

    } else {

        $stmt = $conn->prepare("
            INSERT INTO contacts 
            (first_name, last_name, email, phone, message)
            VALUES (?, ?, ?, ?, ?)
        ");

        if ($stmt) {

            $stmt->bind_param(
                "sssss",
                $first_name,
                $last_name,
                $email,
                $phone,
                $message
            );

            if ($stmt->execute()) {

                $alert_type = "success";
                $alert_message = "Gửi liên hệ thành công!";

            } else {

                $alert_type = "error";
                $alert_message = "Có lỗi xảy ra khi gửi liên hệ!";
            }

            $stmt->close();

        } else {

            $alert_type = "error";
            $alert_message = "Không thể kết nối hệ thống!";
        }
    }
}
?>

<?php include 'header.php'; ?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (!empty($alert_message)) : ?>
<script>
document.addEventListener("DOMContentLoaded", function () {

    Swal.fire({
        icon: '<?php echo $alert_type; ?>',
        title: '<?php echo ($alert_type == "success") ? "Thành công!" : "Thông báo"; ?>',
        text: '<?php echo $alert_message; ?>',
        confirmButtonColor: '#667eea',
        confirmButtonText: 'OK'
    });

});
</script>
<?php endif; ?>

<style>

body{
    background: #f5f7fb;
}

/* =========================
   BREADCRUMB
========================= */

.breadcrumbs-custom{
    background: linear-gradient(135deg,#667eea,#764ba2);
    padding: 90px 0;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.breadcrumbs-custom::before{
    content:'';
    position:absolute;
    inset:0;
    background: rgba(255,255,255,0.05);
}

.breadcrumbs-custom-inner{
    position: relative;
    z-index: 2;
    color:#fff;
}

.breadcrumbs-custom h6{
    letter-spacing: 3px;
    text-transform: uppercase;
    margin-bottom: 15px;
    opacity: .9;
}

.breadcrumbs-custom-title{
    font-size: 55px;
    font-weight: 700;
}

.breadcrumbs-custom-path{
    list-style: none;
    padding:0;
    margin-top:20px;
    display:flex;
    justify-content:center;
    gap:15px;
    flex-wrap: wrap;
}

.breadcrumbs-custom-path li{
    color:#fff;
}

.breadcrumbs-custom-path li a{
    color:#ddd;
    text-decoration:none;
    transition:.3s;
}

.breadcrumbs-custom-path li a:hover{
    color:#fff;
}

/* =========================
   SECTION
========================= */

.section-lg{
    padding:80px 0;
}

.contact-wrapper{
    background:#fff;
    border-radius:20px;
    padding:40px;
    box-shadow:0 10px 40px rgba(0,0,0,0.08);
}

.section-title{
    font-size:40px;
    font-weight:700;
    color:#2c3e50;
    margin-bottom:40px;
    position: relative;
}

.section-title::after{
    content:'';
    width:80px;
    height:4px;
    background:linear-gradient(135deg,#667eea,#764ba2);
    position:absolute;
    left:0;
    bottom:-10px;
    border-radius: 10px;
}

/* =========================
   CONTACT CARD
========================= */

.contact-info-card{
    background:#fff;
    border-radius:18px;
    padding:30px;
    margin-bottom:25px;
    box-shadow:0 5px 25px rgba(0,0,0,0.06);
    transition:.4s;
    height:100%;
}

.contact-info-card:hover{
    transform:translateY(-8px);
    box-shadow:0 15px 35px rgba(102,126,234,0.15);
}

.contact-info-card h4{
    font-size:22px;
    margin-bottom:20px;
    color:#2c3e50;
    font-weight:700;
}

.contact-info-card i{
    color:#667eea;
    margin-right:8px;
}

.contact-info-card p,
.contact-info-card li{
    color:#6c757d;
    line-height:1.8;
}

.contact-info-card ul{
    list-style:none;
    padding:0;
    margin:0;
}

.contact-info-card a{
    text-decoration:none;
    color:#667eea;
    transition:.3s;
}

.contact-info-card a:hover{
    color:#764ba2;
}

/* =========================
   FORM
========================= */

.form-wrap{
    position:relative;
    margin-bottom:25px;
}

.form-input{
    width:100%;
    padding:16px 20px;
    border:2px solid #e5e7eb;
    border-radius:14px;
    background:#fff;
    font-size:16px;
    transition:.3s;
}

.form-input:focus{
    outline:none;
    border-color:#667eea;
    box-shadow:0 0 0 4px rgba(102,126,234,0.1);
}

.form-label{
    position:absolute;
    top:16px;
    left:20px;
    color:#777;
    pointer-events:none;
    transition:.3s;
    background:#fff;
    padding:0 6px;
}

.form-input:focus + .form-label,
.form-input:not(:placeholder-shown) + .form-label{
    top:-10px;
    left:15px;
    font-size:13px;
    color:#667eea;
}

textarea.form-input{
    min-height:160px;
    resize:vertical;
}

/* =========================
   BUTTON
========================= */

.button-primary{
    background:linear-gradient(135deg,#667eea,#764ba2);
    color:#fff;
    border:none;
    padding:16px 35px;
    border-radius:50px;
    font-size:17px;
    font-weight:600;
    cursor:pointer;
    transition:.3s;
    box-shadow:0 8px 25px rgba(102,126,234,0.3);
}

.button-primary:hover{
    transform:translateY(-3px);
    box-shadow:0 12px 35px rgba(102,126,234,0.4);
}

/* =========================
   MAP
========================= */

.map-section iframe{
    width:100%;
    border:0;
    display:block;
    filter:grayscale(20%);
    transition:.3s;
}

.map-section iframe:hover{
    filter:grayscale(0%);
}

/* =========================
   RESPONSIVE
========================= */

@media(max-width:768px){

    .breadcrumbs-custom-title{
        font-size:40px;
    }

    .section-title{
        font-size:30px;
    }

    .contact-wrapper{
        padding:25px;
    }
}

</style>

<!-- Breadcrumb -->
<div class="breadcrumbs-custom">
    <div class="container">
        <div class="breadcrumbs-custom-inner">

            <h6>Kết nối với chúng tôi</h6>

            <h1 class="breadcrumbs-custom-title">
                Liên Hệ
            </h1>

            <ul class="breadcrumbs-custom-path">
                <li>
                    <a href="home.php">Trang chủ</a>
                </li>

                <li>Liên hệ</li>
            </ul>

        </div>
    </div>
</div>

<!-- Contact -->
<section class="section-lg">
    <div class="container">

        <div class="row">

            <!-- LEFT -->
            <div class="col-lg-4 mb-4">

                <div class="contact-info-card">
                    <h4>
                        <i class="mdi mdi-map-marker"></i>
                        Địa chỉ
                    </h4>

                    <p>
                        Số 123, ABC <br>
                        TP Vinh, Nghệ An
                    </p>
                </div>

            
            

            </div>

            <!-- RIGHT -->
            <div class="col-lg-8">

                <div class="contact-wrapper">

                    <h2 class="section-title">
                        Gửi tin nhắn cho chúng tôi
                    </h2>

                    <form method="POST">

                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-wrap">

                                    <input
                                        type="text"
                                        name="first-name"
                                        class="form-input"
                                        placeholder=" "
                                        required
                                    >

                                    <label class="form-label">
                                        Họ
                                    </label>

                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-wrap">

                                    <input
                                        type="text"
                                        name="last-name"
                                        class="form-input"
                                        placeholder=" "
                                        required
                                    >

                                    <label class="form-label">
                                        Tên
                                    </label>

                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-wrap">

                                    <input
                                        type="email"
                                        name="email"
                                        class="form-input"
                                        placeholder=" "
                                        required
                                    >

                                    <label class="form-label">
                                        Email
                                    </label>

                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-wrap">

                                    <input
                                        type="text"
                                        name="phone"
                                        class="form-input"
                                        placeholder=" "
                                    >

                                    <label class="form-label">
                                        Số điện thoại
                                    </label>

                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-wrap">

                                    <textarea
                                        name="message"
                                        class="form-input"
                                        placeholder=" "
                                        required
                                    ></textarea>

                                    <label class="form-label">
                                        Nội dung tin nhắn
                                    </label>

                                </div>
                            </div>

                            <div class="col-12">

                                <button
                                    type="submit"
                                    class="button-primary"
                                >
                                    <i class="mdi mdi-send"></i>
                                    Gửi Tin Nhắn
                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>
</section>

<!-- MAP -->
<section class="map-section">

    <iframe
        src="https://www.google.com/maps?q=18.658757,105.69539&hl=vi&z=14&output=embed"
        height="450"
        allowfullscreen=""
        loading="lazy">
    </iframe>

</section>

<?php include 'footer.php'; ?>