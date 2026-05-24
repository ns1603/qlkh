<?php 
include 'config.php';
include 'header.php'; 

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_id = isset($_GET['category']) ? intval($_GET['category']) : 0;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;
$limit = 9; 
$offset = ($page - 1) * $limit;

$where_clauses = ["c.status = 'published'"];

if (!empty($search)) {
    $where_clauses[] = "c.title LIKE '%" . $conn->real_escape_string($search) . "%'";
}
if ($category_id > 0) {
    $where_clauses[] = "c.category_id = $category_id";
}

$where_sql = "WHERE " . implode(' AND ', $where_clauses);

$sql_count = "SELECT COUNT(*) as total FROM courses c $where_sql";
$result_count = $conn->query($sql_count);
$total_courses = $result_count->fetch_assoc()['total'];
$total_pages = ceil($total_courses / $limit);

$sql = "SELECT c.*, u.fullname AS teacher_name, cat.name AS category_name 
        FROM courses c 
        LEFT JOIN users u ON c.teacher_id = u.id 
        LEFT JOIN categories cat ON c.category_id = cat.id 
        $where_sql 
        ORDER BY c.created_at DESC 
        LIMIT $offset, $limit";

$result = $conn->query($sql);
$categories = $conn->query("SELECT * FROM categories");
?>

<style>
    /* Breadcrumbs Styling */
    .breadcrumbs-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 100px 0;
        width: 100%;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    
    .breadcrumbs-custom::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        opacity: 0.3;
    }

    .breadcrumbs-custom-title {
        font-size: 3rem;
        font-weight: 700;
        color: #fff;
        margin-bottom: 15px;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        position: relative;
        z-index: 1;
    }

    .breadcrumbs-custom-path {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        justify-content: center;
        gap: 15px;
        flex-wrap: wrap;
        position: relative;
        z-index: 1;
    }

    .breadcrumbs-custom-path li {
        color: #fff;
        font-size: 0.95rem;
    }

    .breadcrumbs-custom-path li a {
        color: rgba(255,255,255,0.8);
        text-decoration: none;
        transition: all 0.3s ease;
        padding: 5px 10px;
        border-radius: 4px;
    }
    
    .breadcrumbs-custom-path li a:hover {
        color: #fff;
        background: rgba(255,255,255,0.2);
    }

    .breadcrumbs-custom-path li.active {
        color: #fff;
        font-weight: 600;
    }
    
    .breadcrumbs-custom-path li:not(:last-child)::after {
        content: '/';
        margin-left: 15px;
        color: rgba(255,255,255,0.5);
    }

    /* Course Card Styling */
    .course-card {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        height: 100%;
        display: flex;
        flex-direction: column;
        border: 1px solid rgba(0,0,0,0.05);
        position: relative;
        margin-bottom: 30px;
    }
    
    .course-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        opacity: 0;
        transition: opacity 0.4s ease;
        z-index: 1;
        pointer-events: none;
    }

    .course-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 20px 40px rgba(102, 126, 234, 0.15);
        border-color: rgba(102, 126, 234, 0.2);
    }
    
    .course-card:hover::before {
        opacity: 1;
    }

    .course-img-wrapper {
        position: relative;
        padding-top: 56.25%;
        overflow: hidden;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .course-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover; 
        transition: transform 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .course-card:hover .course-img {
        transform: scale(1.15);
    }
    
    .course-img-wrapper::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.1) 100%);
        opacity: 0;
        transition: opacity 0.4s ease;
    }
    
    .course-card:hover .course-img-wrapper::after {
        opacity: 1;
    }

    .course-category-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: rgba(102, 126, 234, 0.95);
        backdrop-filter: blur(10px);
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.8rem;
        color: #fff;
        font-weight: 600;
        z-index: 2;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        transition: all 0.3s ease;
    }
    
    .course-card:hover .course-category-badge {
        transform: scale(1.05);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
    }

    .course-price {
        position: absolute;
        top: 15px;
        right: 15px;
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
        color: #fff;
        padding: 8px 16px;
        border-radius: 25px;
        font-weight: 700;
        font-size: 0.9rem;
        box-shadow: 0 4px 15px rgba(238, 90, 111, 0.4);
        z-index: 2;
        transition: all 0.3s ease;
    }
    
    .course-card:hover .course-price {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(238, 90, 111, 0.5);
    }
    
    .course-price.free {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        box-shadow: 0 4px 15px rgba(17, 153, 142, 0.4);
    }
    
    .course-card:hover .course-price.free {
        box-shadow: 0 6px 20px rgba(17, 153, 142, 0.5);
    }

    .course-body {
        padding: 24px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        position: relative;
        z-index: 2;
    }

    .course-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 12px;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 54px;
    }
    
    .course-title a { 
        color: #2c3e50; 
        transition: color 0.3s ease;
        text-decoration: none;
    }
    
    .course-title a:hover { 
        color: #667eea;
        text-decoration: none;
    }
    
    .course-body small {
        font-size: 0.95rem;
        color: #6c757d;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .course-body small i {
        color: #667eea;
        font-size: 1.1rem;
    }

    .course-body .btn {
        margin-top: auto;
        margin-top: 18px;
        border-radius: 8px !important;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: #fff;
        padding: 10px 20px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }
    
    .course-body .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        color: #fff;
    }
    
    .course-body .btn-outline-primary {
        background: transparent;
        border: 2px solid #667eea;
        color: #667eea;
    }
    
    .course-body .btn-outline-primary:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: transparent;
        color: #fff;
    }

    /* Sidebar Styling */
    .sidebar-widget {
        background: #ffffff;
        padding: 25px;
        border-radius: 16px;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        margin-bottom: 25px;
        transition: all 0.3s ease;
    }
    
    .sidebar-widget:hover {
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
    }

    .sidebar-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 20px;
        color: #2c3e50;
        border-bottom: 3px solid #667eea;
        padding-bottom: 12px;
        position: relative;
    }
    
    .sidebar-title::after {
        content: '';
        position: absolute;
        bottom: -3px;
        left: 0;
        width: 50px;
        height: 3px;
        background: linear-gradient(90deg, #667eea, #764ba2);
    }

    .input-group {
        margin-bottom: 0;
    }
    
    .input-group .form-control {
        border-radius: 8px 0 0 8px;
        border: 2px solid #e5e7eb;
        padding: 12px 15px;
        transition: all 0.3s ease;
    }
    
    .input-group .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .input-group .btn {
        border-radius: 0 8px 8px 0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 12px 20px;
        transition: all 0.3s ease;
    }

    .input-group .btn:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        transform: scale(1.05);
    }

    .category-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .category-list li {
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
        transition: all 0.3s ease;
    }
    
    .category-list li:last-child {
        border-bottom: none;
    }
    
    .category-list li:hover {
        padding-left: 10px;
    }

    .category-list li a {
        color: #6c757d;
        font-size: 0.95rem;
        text-decoration: none;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .category-list li a::before {
        content: '▸';
        color: #667eea;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .category-list li a:hover,
    .category-list li a.active {
        color: #667eea;
        font-weight: 600;
        padding-left: 0;
    }
    
    .category-list li a:hover::before,
    .category-list li a.active::before {
        opacity: 1;
    }

    /* Pagination Styling */
    .pagination {
        margin-top: 40px;
    }

    .pagination .page-link {
        border-radius: 8px !important;
        margin: 0 5px;
        border: 2px solid #e5e7eb;
        color: #6c757d;
        padding: 10px 18px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .pagination .page-link:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
        color: #fff;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }
    
    .pagination .page-item.disabled .page-link {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .pagination .page-item.disabled .page-link:hover {
        transform: none;
        background: #e5e7eb;
        border-color: #e5e7eb;
        color: #6c757d;
    }

    /* Alert Styling */
    .alert-warning {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border: none;
        border-radius: 16px;
        padding: 40px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }
    
    .alert-warning h4 {
        color: #856404;
        font-weight: 700;
        margin-bottom: 15px;
    }
    
    .alert-warning p {
        color: #856404;
        margin-bottom: 20px;
    }
    
    .alert-warning .btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: #fff;
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .alert-warning .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }

    /* Grid System */
    .row.row-30 {
        display: flex;
        flex-wrap: wrap;
        margin-right: -15px;
        margin-left: -15px;
    }
    
    .row.row-30 > [class*="col-"] {
        padding-right: 15px;
        padding-left: 15px;
        position: relative;
        width: 100%;
    }
    
    .row.row-50 {
    display: flex;
        flex-wrap: wrap;
        margin-right: -15px;
        margin-left: -15px;
    }
    
    .row.row-50 > [class*="col-"] {
        padding-right: 15px;
        padding-left: 15px;
    position: relative;
        width: 100%;
    }
    
    @media (min-width: 768px) {
        .row.row-30 > .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }
        
        .row.row-50 > .col-lg-3 {
            flex: 0 0 25%;
            max-width: 25%;
        }
        
        .row.row-50 > .col-lg-9 {
            flex: 0 0 75%;
            max-width: 75%;
        }
    }
    
    @media (min-width: 992px) {
        .row.row-30 > .col-lg-4 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .breadcrumbs-custom-title {
            font-size: 2rem;
        }
        
        .sidebar-widget {
            margin-bottom: 20px;
        }
        
        .course-card {
            margin-bottom: 25px;
        }
    }
</style>

<section class="breadcrumbs-custom">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Tất cả khóa học</h2>
        <ul class="breadcrumbs-custom-path">
            <li><a href="home.php">Trang chủ</a></li>
            <li class="active">Khóa học</li>
        </ul>
    </div>
</section>

<section class="section section-lg bg-default">
    <div class="container">
        <div class="row row-50">
            <div class="col-lg-3">
                <div class="sidebar-widget">
                    <h5 class="sidebar-title">Tìm kiếm</h5>
                    <form action="courses_list.php" method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Nhập tên khóa học..." value="<?= htmlspecialchars($search) ?>">
                            <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                        </div>
                    </form>
                </div>

                <div class="sidebar-widget">
                    <h5 class="sidebar-title">Danh mục</h5>
                    <ul class="category-list pl-0">
                        <li>
                            <a href="courses_list.php" class="<?= ($category_id == 0) ? 'active' : '' ?>">
                                Tất cả khóa học
                            </a>
                        </li>
                        <?php while($cat = $categories->fetch_assoc()): ?>
                            <li>
                                <a href="courses_list.php?category=<?= $cat['id'] ?>" class="<?= ($category_id == $cat['id']) ? 'active' : '' ?>">
                                    <?= htmlspecialchars($cat['name']) ?>
                                </a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="row row-30">
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): 
                            $id = $row['id'];
                            $title = htmlspecialchars($row['title']);
                            $teacher = htmlspecialchars($row['teacher_name'] ?? 'Admin');
                            $cat_name = htmlspecialchars($row['category_name'] ?? 'General');
                            $price_tag = ($row['price'] == 0) ? "Miễn phí" : number_format($row['price'], 0, ',', '.') . 'đ';
                            
                            $img_url = !empty($row['thumbnail']) ? $row['thumbnail'] : 'assets/images/default.jpg';
                        ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="course-card">
                                    <div class="course-img-wrapper">
                                        <a href="course_details.php?id=<?= $id ?>">
                                            <img src="<?= $img_url ?>" class="course-img" alt="<?= $title ?>">
                                        </a>
                                        <span class="course-category-badge"><?= $cat_name ?></span>
                                        <span class="course-price <?= ($row['price'] == 0) ? 'free' : '' ?>">
                                            <?= ($row['price'] == 0) ? '<i class="mdi mdi-gift" style="margin-right: 5px;"></i>' : '' ?>
                                            <?= $price_tag ?>
                                        </span>
                                    </div>
                                    <div class="course-body">
                                        <h5 class="course-title">
                                            <a href="course_details.php?id=<?= $id ?>"><?= $title ?></a>
                                        </h5>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <small><i class="mdi mdi-account-circle"></i> <?= $teacher ?></small>
                                        </div>
                                        <div class="mt-auto pt-3 border-top mt-3">
                                            <a href="course_details.php?id=<?= $id ?>" class="btn btn-sm w-100">
                                                <i class="mdi mdi-eye" style="margin-right: 5px;"></i>Xem chi tiết
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12 text-center">
                            <div class="alert alert-warning">
                                <h4>Không tìm thấy khóa học nào!</h4>
                                <p>Vui lòng thử tìm từ khóa khác hoặc chọn danh mục khác.</p>
                                <a href="courses_list.php" class="btn btn-sm btn-primary mt-2">Xem tất cả</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($total_pages > 1): ?>
                <div class="mt-5 d-flex justify-content-center">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-lg">
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page-1 ?>&search=<?= $search ?>&category=<?= $category_id ?>">Trước</a>
                            </li>

                            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&search=<?= $search ?>&category=<?= $category_id ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page+1 ?>&search=<?= $search ?>&category=<?= $category_id ?>">Sau</a>
                            </li>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>