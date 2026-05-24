<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>V-Learning</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            box-sizing: border-box;
        }

        /* ── NAVBAR ── */
        .vl-header {
            font-family: 'Be Vietnam Pro', sans-serif;
        }

        .vl-nav-top {
            background: #0f1f3d;
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
        }

        .vl-nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 64px;
            padding: 0 1.5rem;
        }

        /* Logo */
        .vl-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .vl-logo-icon {
            width: 36px;
            height: 36px;
            background: #2563eb;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            color: #fff;
            letter-spacing: .5px;
        }

        .vl-logo-text {
            font-size: 18px;
            font-weight: 700;
            color: #fff;
            letter-spacing: .02em;
        }

        .vl-logo-text span {
            color: #60a5fa;
        }

        /* Links */
        .vl-nav-links {
            display: flex;
            align-items: center;
            gap: 2px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .vl-nav-links li {
            position: relative;
        }

        .vl-nav-link {
            display: flex;
            align-items: center;
            gap: 5px;
            color: rgba(255, 255, 255, 0.72);
            text-decoration: none;
            font-size: 14px;
            padding: 7px 12px;
            border-radius: 7px;
            transition: all .15s;
            white-space: nowrap;
        }

        .vl-nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
        }

        .vl-nav-link.active {
            color: #fff;
        }

        .vl-divider {
            width: 1px;
            height: 20px;
            background: rgba(255, 255, 255, 0.15);
            margin: 0 6px;
            flex-shrink: 0;
        }

        /* Badge */
        .vl-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ef4444;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            min-width: 16px;
            height: 16px;
            border-radius: 8px;
            padding: 0 4px;
            margin-top: -3px;
            margin-left: 1px;
            vertical-align: top;
            line-height: 1;
        }

        /* User dropdown */
        .vl-user {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #fff;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 8px;
            transition: background .15s;
            position: relative;
        }

        .vl-user:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .vl-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            color: #fff;
            flex-shrink: 0;
        }

        .vl-user-name {
            font-size: 13px;
            font-weight: 500;
        }

        .vl-chevron {
            font-size: 11px;
            opacity: .6;
        }

        .vl-dropdown {
            display: none;
            position: absolute;
            top: calc(0px);
            right: 0;
            background: #fff;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            min-width: 190px;
            padding: 6px;
            z-index: 9999;
        }

        .vl-user:hover .vl-dropdown {
            display: block;
        }

        .vl-dd-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 8px 10px;
            border-radius: 6px;
            font-size: 13px;
            color: #374151;
            text-decoration: none;
            transition: background .1s;
        }

        .vl-dd-item:hover {
            background: #f3f4f6;
            color: #374151;
        }

        .vl-dd-item i {
            font-size: 15px;
            color: #6b7280;
        }

        .vl-dd-item.danger {
            color: #ef4444;
        }

        .vl-dd-item.danger i {
            color: #ef4444;
        }

        .vl-dd-sep {
            height: 1px;
            background: #e5e7eb;
            margin: 4px 0;
        }

        /* Auth buttons */
        .vl-btn-login {
            display: inline-flex;
            align-items: center;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: rgba(255, 255, 255, 0.85) !important;
            font-size: 13px;
            font-weight: 500;
            padding: 7px 16px;
            border-radius: 7px;
            text-decoration: none;
            transition: all .15s;
        }

        .vl-btn-login:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.5);
            color: #fff !important;
        }

        .vl-btn-register {
            display: inline-flex;
            align-items: center;
            background: #2563eb;
            color: #fff !important;
            font-size: 13px;
            font-weight: 600;
            padding: 7px 18px;
            border-radius: 7px;
            text-decoration: none;
            transition: background .15s;
        }

        .vl-btn-register:hover {
            background: #1d4ed8;
            color: #fff !important;
        }

        /* Sub-nav (logged in only) */
        .vl-subnav {
            background: #162447;
        }

        .vl-subnav-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
        }

        .vl-sub-link {
            display: flex;
            align-items: center;
            gap: 6px;
            color: rgba(255, 255, 255, 0.6);
            font-size: 13px;
            padding: 9px 14px;
            text-decoration: none;
            border-bottom: 2px solid transparent;
            transition: all .15s;
            white-space: nowrap;
        }

        .vl-sub-link:hover,
        .vl-sub-link.active {
            color: #60a5fa;
            border-bottom-color: #2563eb;
        }

        .vl-sub-link i {
            font-size: 15px;
        }

        /* Mobile toggle */
        .vl-mobile-toggle {
            display: none;
            background: none;
            border: none;
            color: #fff;
            font-size: 24px;
            cursor: pointer;
            padding: 4px;
        }

        @media (max-width: 768px) {
            .vl-mobile-toggle {
                display: block;
            }

            .vl-nav-links {
                display: none;
            }

            .vl-nav-links.open {
                display: flex;
                flex-direction: column;
                position: absolute;
                top: 64px;
                left: 0;
                right: 0;
                background: #0f1f3d;
                padding: 12px 1rem;
                z-index: 999;
                gap: 2px;
            }

            .vl-nav-links.open .vl-divider {
                display: none;
            }

            .vl-nav-links.open li {
                width: 100%;
            }

            .vl-nav-link {
                padding: 10px 12px;
            }

            .vl-dropdown {
                right: auto;
                left: 0;
            }

            .vl-subnav-inner {
                overflow-x: auto;
            }
        }
    </style>
</head>

<body>

    <header class="vl-header" style="position: sticky; top: 0; z-index: 1000;">

        <div class="vl-nav-top">
            <div class="vl-nav-inner">

                <!-- Logo -->
                <a class="vl-logo" href="home.php">
                    <div class="vl-logo-icon">VL</div>
                    <span class="vl-logo-text">V-<span>Learning</span></span>
                </a>

                <!-- Mobile toggle -->
                <button class="vl-mobile-toggle" onclick="document.getElementById('vl-nav').classList.toggle('open')" aria-label="Mở menu">
                    ☰
                </button>

                <!-- Desktop nav -->
                <ul class="vl-nav-links" id="vl-nav">
                    <li><a class="vl-nav-link" href="home.php">Trang chủ</a></li>
                    <li><a class="vl-nav-link" href="about.php">Giới thiệu</a></li>
                    <li><a class="vl-nav-link" href="courses_list.php">Khóa học</a></li>
                    <li><a class="vl-nav-link" href="contacts.php">Liên hệ</a></li>

                    <?php if (isset($_SESSION['user_id'])): ?>

                        <li>
                            <div class="vl-divider"></div>
                        </li>

                        <li>
                            <a class="vl-nav-link" href="my_notifications.php">
                                <i class="mdi mdi-bell" aria-hidden="true"></i>
                                Thông báo
                                <?php
                                include_once 'config.php';
                                $uid = $_SESSION['user_id'];
                                $count = $conn->query("SELECT id FROM notifications WHERE receiver_id=$uid AND is_read=0")->num_rows;
                                if ($count > 0) echo "<span class='vl-badge'>$count</span>";
                                ?>
                            </a>
                        </li>

                        <li>
                            <a class="vl-nav-link" href="quiz_list.php">
                                <i class="mdi mdi-pencil-box-outline" aria-hidden="true"></i>
                                Kiểm tra
                            </a>
                        </li>

                        <li>
                            <div class="vl-divider"></div>
                        </li>

                        <li>
                            <?php
                            $fullname = htmlspecialchars($_SESSION['fullname'] ?? 'User');
                            $initials = '';
                            $parts = explode(' ', $fullname);
                            if (count($parts) >= 2) {
                                $initials = mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr(end($parts), 0, 1));
                            } else {
                                $initials = mb_strtoupper(mb_substr($fullname, 0, 2));
                            }
                            ?>
                            <div class="vl-user">
                                <div class="vl-avatar"><?= $initials ?></div>
                                <span class="vl-user-name"><?= $fullname ?></span>
                                <span class="vl-chevron">▾</span>

                                <div class="vl-dropdown">
                                    <a class="vl-dd-item" href="profile.php">
                                        <i class="mdi mdi-account-circle"></i> Hồ sơ cá nhân
                                    </a>
                                    <a class="vl-dd-item" href="my_courses.php">
                                        <i class="mdi mdi-book-open-variant"></i> Khóa học của tôi
                                    </a>
                                    <?php if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'teacher'])): ?>
                                        <a class="vl-dd-item" href="admin/index.php">
                                            <i class="mdi mdi-shield-account"></i> Trang quản trị
                                        </a>
                                    <?php endif; ?>
                                    <div class="vl-dd-sep"></div>
                                    <a class="vl-dd-item danger" href="logout.php">
                                        <i class="mdi mdi-logout"></i> Đăng xuất
                                    </a>
                                </div>
                            </div>
                        </li>

                    <?php else: ?>

                        <li>
                            <div class="vl-divider"></div>
                        </li>
                        <li><a class="vl-btn-login" href="Login.php">Đăng nhập</a></li>
                        <li style="margin-left: 6px;"><a class="vl-btn-register" href="Register.php">Đăng ký</a></li>

                    <?php endif; ?>
                </ul>

            </div>
        </div>

        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="vl-subnav">
                <div class="vl-subnav-inner">
                    <a class="vl-sub-link" href="home.php">
                        <i class="mdi mdi-home-outline"></i> Trang chủ
                    </a>
                    <a class="vl-sub-link" href="my_courses.php">
                        <i class="mdi mdi-book-open-variant"></i> Khóa học của tôi
                    </a>
                    <a class="vl-sub-link" href="my_certificates.php">
                        <i class="mdi mdi-certificate-outline"></i> Chứng chỉ
                    </a>
                    <a class="vl-sub-link" href="wishlist.php">
                        <i class="mdi mdi-heart-outline"></i> Yêu thích
                    </a>
                    <a class="vl-sub-link" href="my_progress.php">
                        <i class="mdi mdi-chart-line"></i> Tiến độ học
                    </a>
                </div>
            </div>
        <?php endif; ?>

    </header>