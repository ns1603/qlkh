<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    
    <li class="nav-item nav-profile">
      <a href="#" class="nav-link">
        <div class="nav-profile-image">
          <img src="/Learning/admin/assets/images/faces/face1.jpg" alt="profile" />
          <span class="login-status online"></span>
        </div>
        <div class="nav-profile-text d-flex flex-column">
          <span class="font-weight-bold mb-2">
            <?php echo isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Admin'; ?>
          </span>
          <span class="text-secondary text-small">
            <?php echo isset($_SESSION['user_role']) ? ucfirst($_SESSION['user_role']) : 'Member'; ?>
          </span>
        </div>
        <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="/Learning/admin/index.php">
        <span class="menu-title">Dashboard</span>
        <i class="mdi mdi-home menu-icon"></i>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#ui-courses" aria-expanded="false" aria-controls="ui-courses">
        <span class="menu-title">Quản lý Khóa Học</span>
        <i class="menu-arrow"></i>
        <i class="mdi mdi-book-open-page-variant menu-icon"></i>
      </a>
      <div class="collapse" id="ui-courses">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> 
            <a class="nav-link" href="/Learning/admin/pages/KhoaHoc/ListKhoaHoc.php">Danh sách khóa học</a>
          </li>
          <li class="nav-item"> 
            <a class="nav-link" href="/Learning/admin/pages/KhoaHoc/AddKhoaHoc.php">Thêm khóa học mới</a>
          </li>
        </ul>
      </div>
    </li>
    <li class="nav-item">
            <a class="nav-link" href="/Learning/admin/pages/DanhMuc/ListDanhMuc.php">
              <span class="menu-title">Quản lý Danh mục</span>
              <i class="mdi mdi-format-list-bulleted menu-icon"></i> </a>
          </li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#ui-lessons" aria-expanded="false" aria-controls="ui-lessons">
        <span class="menu-title">Quản lý Bài Giảng</span>
        <i class="menu-arrow"></i>
        <i class="mdi mdi-video menu-icon"></i>
      </a>
      <div class="collapse" id="ui-lessons">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> 
            <a class="nav-link" href="/Learning/admin/pages/BaiGiang/ListBaiGiang.php">Danh sách Video</a>
          </li>
          <li class="nav-item"> 
            <a class="nav-link" href="/Learning/admin/pages/TaiLieu/ListTaiLieu.php">Tài liệu đính kèm</a>
          </li>
        </ul>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#ui-quizzes" aria-expanded="false" aria-controls="ui-quizzes">
        <span class="menu-title">Ngân hàng Đề thi</span>
        <i class="menu-arrow"></i>
        <i class="mdi mdi-file-document-box menu-icon"></i>
      </a>
      <div class="collapse" id="ui-quizzes">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> 
            <a class="nav-link" href="/Learning/admin/pages/DeThi/ListDeThi.php">Danh sách Đề thi</a>
          </li>
          <li class="nav-item"> 
            <a class="nav-link" href="/Learning/admin/pages/CauHoi/ListCauHoi.php">Kho Câu hỏi (Questions)</a>
          </li>
        </ul>
      </div>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/Learning/admin/pages/DeThi/ListDiemThi.php">
          <span class="menu-title">Kết quả thi & Báo cáo</span>
          <i class="mdi mdi-poll menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#ui-users" aria-expanded="false" aria-controls="ui-users">
        <span class="menu-title">Quản lý Người dùng</span>
        <i class="menu-arrow"></i>
        <i class="mdi mdi-account-multiple menu-icon"></i>
      </a>
      <div class="collapse" id="ui-users">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> 
            <a class="nav-link" href="/Learning/admin/pages/NguoiDung/ListHocVien.php">Danh sách Học viên</a>
          </li>
          <?php if(isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 'admins' || $_SESSION['user_role'] == 'admin')): ?>
          <li class="nav-item"> 
            <a class="nav-link" href="/Learning/admin/pages/NguoiDung/ListGiaoVien.php">Danh sách Giáo viên</a>
          </li>
          <?php endif; ?>
        </ul>
      </div>
    </li>

    <?php if(isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 'admins' || $_SESSION['user_role'] == 'admin')): ?>
    <li class="nav-item">
      <a class="nav-link" href="/Learning/admin/pages/DonHang/ListDonHang.php">
        <span class="menu-title">Quản lý Doanh thu</span>
        <i class="mdi mdi-cash-usd menu-icon"></i>
      </a>
    </li>
    <?php endif; ?>

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#ui-interactive" aria-expanded="false" aria-controls="ui-interactive">
        <span class="menu-title">Phản hồi & Đánh giá</span>
        <i class="menu-arrow"></i>
        <i class="mdi mdi-comment-processing menu-icon"></i>
      </a>
      <div class="collapse" id="ui-interactive">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> 
            <a class="nav-link" href="/Learning/admin/pages/BinhLuan/ListBinhLuan.php">Bình luận (Comments)</a>
          </li>
          <li class="nav-item"> 
            <a class="nav-link" href="/Learning/admin/pages/DanhGia/ListDanhGia.php">Đánh giá (Ratings)</a>
          </li>
        </ul>
      </div>
    </li>
    <?php if(isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 'admins' || $_SESSION['user_role'] == 'admin')): ?>
    <li class="nav-item">
      <a class="nav-link" href="/Learning/admin/pages/HopDong/ListHopDongGV.php">
        <span class="menu-title">Quản lý Hợp đồng</span>
        <i class="mdi mdi-file-document-box menu-icon"></i>
      </a>
    </li>
    <?php endif; ?>

    <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'teacher'): ?>
    <li class="nav-item">
      <a class="nav-link" href="/Learning/admin/pages/HopDong/HopDongGV.php">
        <span class="menu-title">Hợp đồng của tôi</span>
        <i class="mdi mdi-file-eye menu-icon"></i>
      </a>
    </li>
    <?php endif; ?>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#ui-thongbao" aria-expanded="false" aria-controls="ui-thongbao">
        <span class="menu-title">Thông báo</span>
        <i class="menu-arrow"></i>
        <i class="mdi mdi-bell-ring menu-icon"></i>
      </a>
      <div class="collapse" id="ui-thongbao">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="/Learning/admin/pages/ThongBao/AddThongBao.php">Gửi thông báo</a></li>
          <li class="nav-item"> <a class="nav-link" href="/Learning/admin/pages/ThongBao/ListThongBao.php">Lịch sử gửi</a></li>
        </ul>
      </div>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/Learning/admin/pages/BaoCao/ThongKe.php">
        <span class="menu-title">Báo cáo & Thống kê</span>
        <i class="mdi mdi-chart-bar menu-icon"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="https://github.com/taiqhau293" target="_blank">
        <span class="menu-title">Github của Admin</span>
        <i class="mdi mdi-file-document-box menu-icon"></i>
      </a>
    </li>
  </ul>
</nav>