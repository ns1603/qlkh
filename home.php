<?php include 'header.php'; ?>
<style>
    .course-card {
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: 0.3s;
        height: 100%;
        display: flex;
        flex-direction: column;
        border: 1px solid #eee;
    }
    
    .course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        border-color: #b2ebf2;
    }

    .course-img-wrapper {
        position: relative;
        padding-top: 56.25%;
        overflow: hidden;
    }
    
    .course-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover; 
        transition: 0.5s;
    }
    
    .course-card:hover .course-img {
        transform: scale(1.1);
    }

    .course-price {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #fff;
        padding: 4px 10px;
        border-radius: 4px;
        font-weight: 700;
        color: #d32f2f;
        font-size: 0.85rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .course-body {
        padding: 20px;
        text-align: left;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .course-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 10px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 44px;
    }
    
    .course-title a { color: #333; transition: 0.2s; }
    .course-title a:hover { color: #007bff; text-decoration: none; }

    .course-teacher {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 15px;
    }
    
    .course-footer {
        margin-top: auto; 
        border-top: 1px solid #f0f0f0;
        padding-top: 15px;
    }

    .course-card {
    max-width: 350px;
    margin: 0 auto;   
}
</style>

<section class="section section-lg bg-default text-center">
  <div class="container">
    
    <h2 class="wow fadeIn mb-5">Khóa học nổi bật</h2>
    <div class="row row-30 justify-content-center">
      <?php
      include 'config.php';
      $sql = "SELECT courses.*, users.fullname AS teacher_name 
              FROM courses 
              LEFT JOIN users ON courses.teacher_id = users.id 
              WHERE courses.status = 'published' 
              ORDER BY courses.created_at DESC 
              LIMIT 8"; 
      
      $result = $conn->query($sql);

      if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              $course_id   = $row['id'];
              $title       = $row['title'];
              $teacher     = $row['teacher_name'] ?? 'Admin';
              $price       = $row['price'];
              $img_url = !empty($row['thumbnail']) ? $row['thumbnail'] : 'assets/images/course-default.jpg';
              $price_tag = ($price == 0) ? "Miễn phí" : number_format($price, 0, ',', '.') . 'đ';
      ?>
              
              <div class="col-md-6 col-lg-3 mb-4 wow fadeInUp">
                  <div class="course-card">
                      <div class="course-img-wrapper">
                          <a href="course_details.php?id=<?= $course_id ?>">
                              <img src="<?= $img_url ?>" class="course-img" alt="<?= htmlspecialchars($title) ?>">
                          </a>
                          <div class="course-price"><?= $price_tag ?></div>
                      </div>

                      <div class="course-body">
                          <h5 class="course-title">
                              <a href="course_details.php?id=<?= $course_id ?>">
                                  <?= htmlspecialchars($title) ?>
                              </a>
                          </h5>
                          
                          <div class="course-teacher">
                              <i class="mdi mdi-account-circle text-primary"></i> 
                              <span>GV: <?= htmlspecialchars($teacher) ?></span>
                          </div>

                          <div class="course-footer">
                          <a href="course_details.php?id=<?= $course_id ?>" 
                            style="display: inline-block; 
                                    padding: 8px 16px; 
                                    border: 1px solid #007bff;
                                    border-radius: 4px;
                                    color: #007bff;
                                    text-decoration: none;
                                    transition: all 0.3s ease;">
                                Xem chi tiết
                            </a>
                          </div>
                      </div>
                  </div>
              </div>

      <?php 
          }
      } else {
          echo '<div class="col-12"><p class="text-muted">Chưa có khóa học nào được xuất bản.</p></div>';
      }
      $conn->close();
      ?>
    </div>

    <div class="mt-5">
        <a class="button button-lg button-primary-gradient" href="courses_list.php">
            <span>Xem tất cả khóa học</span>
        </a>
    </div>
  </div>
</section>
<section class="section section-lg bg-light text-center">
  <div class="container">

    <h2 class="wow fadeIn mb-5 text-success">
        🎓 Khóa học miễn phí
    </h2>

    <div class="row row-30 justify-content-center">
      <?php
      include 'config.php';

      $sql_free = "SELECT courses.*, users.fullname AS teacher_name 
                   FROM courses 
                   LEFT JOIN users ON courses.teacher_id = users.id 
                   WHERE courses.status = 'published' 
                     AND courses.price = 0
                   ORDER BY courses.created_at DESC 
                   LIMIT 8";

      $result_free = $conn->query($sql_free);

      if ($result_free && $result_free->num_rows > 0) {
          while ($row = $result_free->fetch_assoc()) {
              $course_id = $row['id'];
              $title     = $row['title'];
              $teacher   = $row['teacher_name'] ?? 'Admin';
              $img_url   = !empty($row['thumbnail']) 
                            ? $row['thumbnail'] 
                            : 'assets/images/course-default.jpg';
      ?>
        <div class="col-md-6 col-lg-3 mb-4 wow fadeInUp">
            <div class="course-card">
                <div class="course-img-wrapper">
                    <a href="course_details.php?id=<?= $course_id ?>">
                        <img src="<?= $img_url ?>" class="course-img" alt="<?= htmlspecialchars($title) ?>">
                    </a>
                    <div class="course-price text-success">Miễn phí</div>
                </div>

                <div class="course-body">
                    <h5 class="course-title">
                        <a href="course_details.php?id=<?= $course_id ?>">
                            <?= htmlspecialchars($title) ?>
                        </a>
                    </h5>

                    <div class="course-teacher">
                        <i class="mdi mdi-account-circle text-primary"></i>
                        <span>GV: <?= htmlspecialchars($teacher) ?></span>
                    </div>

                    <div class="course-footer">
                        <a href="course_details.php?id=<?= $course_id ?>"
                           class="button button-sm button-success w-100"
                           style="display: inline-block; 
                                    padding: 8px 16px; 
                                    border: 1px solid #007bff;
                                    border-radius: 4px;
                                    color: #007bff;
                                    text-decoration: none;
                                    transition: all 0.3s ease;">
                            Học miễn phí
                        </a>
                    </div>
                </div>
            </div>
        </div>
      <?php
          }
      } else {
          echo '<div class="col-12">
                  <p class="text-muted">Hiện chưa có khóa học miễn phí.</p>
                </div>';
      }
      ?>
    </div>

    <div class="mt-5">
        <a class="button button-lg button-primary-gradient" href="courses_list.php">
            <span>Xem tất cả khóa học</span>
        </a>
    </div>

  </div>
</section>

<?php include 'footer.php'; ?>