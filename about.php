<?php
include 'config.php';
include 'header.php';
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

  .breadcrumbs-custom-container {
    display: flex;
    justify-content: center;
    position: relative;
    z-index: 1;
  }

  .breadcrumbs-custom-inner {
    text-align: center;
    color: #fff;
  }

  .breadcrumbs-custom-item h6 {
    margin-bottom: 15px;
    letter-spacing: 3px;
    font-size: 0.9rem;
    text-transform: uppercase;
    opacity: 0.9;
    font-weight: 500;
  }

  .breadcrumbs-custom-title {
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 0;
    text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    animation: fadeInDown 0.8s ease-out;
  }

  .breadcrumbs-custom-path {
    list-style: none;
    padding: 0;
    margin-top: 25px;
    display: flex;
    justify-content: center;
    gap: 15px;
    flex-wrap: wrap;
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

  /* About Section Styling */
  .box-1 {
    padding: 40px 0;
  }
  
  .box-1 h2 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 20px;
    line-height: 1.3;
  }
  
  .box-1 h5 {
    font-size: 1.3rem;
    color: #667eea;
    margin-bottom: 25px;
    font-weight: 600;
    line-height: 1.5;
  }
  
  .box-1 p {
    font-size: 1.1rem;
    color: #6c757d;
    line-height: 1.8;
    margin-bottom: 30px;
  }
  
  .thumbnail-media {
    position: relative;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    transition: transform 0.3s ease;
    min-height: 280px;
    max-height: 320px;
    background-size: cover;
    background-position: center;
  }
  
  .thumbnail-media:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 35px rgba(0,0,0,0.15);
  }
  
  .thumbnail-media-icon {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 50px;
    color: #fff;
    background: rgba(102, 126, 234, 0.9);
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    cursor: pointer;
    z-index: 2;
  }
  
  .thumbnail-media-icon:hover {
    background: rgba(102, 126, 234, 1);
    transform: translate(-50%, -50%) scale(1.08);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
  }

  /* Box Classic Styling */
  .box-classic {
    background: #fff;
    border-radius: 12px;
    padding: 25px 20px;
    text-align: center;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    box-shadow: 0 3px 15px rgba(0,0,0,0.06);
    border: 1px solid rgba(0,0,0,0.05);
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    overflow: hidden;
  }
  
  .box-classic::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    transform: scaleX(0);
    transition: transform 0.3s ease;
  }
  
  .box-classic:hover {
    transform: translateY(-6px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.12);
    border-color: rgba(102, 126, 234, 0.25);
  }
  
  .box-classic:hover::before {
    transform: scaleX(1);
  }
  
  .box-classic-icon {
    font-size: 2.5rem;
    color: #667eea;
    margin-bottom: 15px;
    transition: all 0.3s ease;
    display: block;
  }
  
  .box-classic:hover .box-classic-icon {
    transform: scale(1.08) rotate(3deg);
    color: #764ba2;
  }
  
  .box-classic-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 10px;
    transition: color 0.3s ease;
    line-height: 1.4;
  }
  
  .box-classic:hover .box-classic-title {
    color: #667eea;
  }
  
  .box-classic-inner p {
    color: #6c757d;
    line-height: 1.6;
    font-size: 0.85rem;
    margin: 0;
  }
  
  .box-classic-main {
    text-decoration: none;
    width: 100%;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
  }

  /* Timeline Styling */
  .timeline-classic-item {
    background: #fff;
    border-radius: 16px;
    padding: 35px 30px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    transition: all 0.4s ease;
    border: 1px solid rgba(0,0,0,0.05);
    height: 100%;
    position: relative;
  }
  
  .timeline-classic-item:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.12);
  }
  
  .timeline-classic-time {
    font-size: 1.5rem;
    font-weight: 700;
    color: #667eea;
    margin-bottom: 20px;
  }
  
  .timeline-classic-divider {
    width: 60px;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    margin: 20px 0;
    border-radius: 2px;
  }
  
  .timeline-classic-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 15px;
  }
  
  .timeline-classic-item p {
    color: #6c757d;
    line-height: 1.7;
  }

  /* Testimonials Styling */
  .quote-classic {
    background: #fff;
    border-radius: 16px;
    padding: 35px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    transition: all 0.4s ease;
    border: 1px solid rgba(0,0,0,0.05);
    height: 100%;
    position: relative;
    margin-bottom: 30px;
  }
  
  .quote-classic::before {
    content: '"';
    position: absolute;
    top: 20px;
    left: 25px;
    font-size: 5rem;
    color: rgba(102, 126, 234, 0.1);
    font-family: Georgia, serif;
    line-height: 1;
  }
  
  .quote-classic:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.12);
  }
  
  .quote-classic-avatar-outer {
    margin-bottom: 20px;
    display: flex;
    justify-content: center;
  }
  
  .quote-classic-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #667eea;
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
  }
  
  .quote-classic-cite {
    font-size: 1.2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 5px;
  }
  
  .quote-classic-position {
    color: #667eea;
    font-size: 0.9rem;
    margin-bottom: 15px;
    font-weight: 600;
  }
  
  .quote-classic-text {
    color: #6c757d;
    line-height: 1.8;
    font-style: italic;
    position: relative;
    z-index: 1;
  }
  
  .quote-classic-text q {
    quotes: none;
  }

  /* Section Headers */
  .section h2 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 20px;
    position: relative;
    display: inline-block;
  }
  
  .section h2::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 2px;
  }
  
  .section p {
    color: #6c757d;
    font-size: 1.1rem;
    margin-top: 25px;
  }

  /* Button Styling */
  .button-primary-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: #fff;
    padding: 16px 40px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
    box-shadow: 0 6px 25px rgba(102, 126, 234, 0.3);
    position: relative;
    overflow: hidden;
  }
  
  .button-primary-gradient::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
  }
  
  .button-primary-gradient:hover::before {
    width: 300px;
    height: 300px;
  }
  
  .button-primary-gradient:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 35px rgba(102, 126, 234, 0.4);
    color: #fff;
    text-decoration: none;
  }
  
  .button-primary-gradient span {
    position: relative;
    z-index: 1;
  }

  /* Animations */
  @keyframes fadeInDown {
    from {
      opacity: 0;
      transform: translateY(-30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  /* Grid System Support */
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
  
  @media (min-width: 576px) {
    .row.row-30 > .col-sm-6 {
      flex: 0 0 50%;
      max-width: 50%;
    }
  }
  
  @media (min-width: 768px) {
    .row.row-30 > .col-md-6 {
      flex: 0 0 50%;
      max-width: 50%;
    }
    
    .row.row-50 > .col-md-11 {
      flex: 0 0 91.666667%;
      max-width: 91.666667%;
    }
  }
  
  @media (min-width: 992px) {
    .row.row-30 > .col-lg-4 {
      flex: 0 0 33.333333%;
      max-width: 33.333333%;
    }
    
    .row.row-30 > .col-lg-3 {
      flex: 0 0 25%;
      max-width: 25%;
    }
    
    .row.row-50 > .col-lg-6 {
      flex: 0 0 50%;
      max-width: 50%;
    }
  }

  /* Responsive Design */
  @media (max-width: 768px) {
    .breadcrumbs-custom-title {
      font-size: 2.5rem;
    }
    
    .box-1 h2 {
      font-size: 2rem;
    }
    
    .box-1 h5 {
      font-size: 1.1rem;
    }
    
    .section h2 {
      font-size: 2rem;
    }
    
    .thumbnail-media {
      min-height: 220px;
      max-height: 250px;
    }
    
    .thumbnail-media-icon {
      width: 60px;
      height: 60px;
      font-size: 35px;
    }
    
    .box-classic {
      margin-bottom: 20px;
      padding: 20px 15px;
    }
    
    .box-classic-icon {
      font-size: 2rem;
      margin-bottom: 12px;
    }
    
    .box-classic-title {
      font-size: 1rem;
    }
    
    .box-classic-inner p {
      font-size: 0.8rem;
    }
    
    .timeline-classic-item {
      margin-bottom: 30px;
    }
    
    .quote-classic {
      margin-bottom: 30px;
    }
  }

</style>
<div class="breadcrumbs-custom">
  <div class="container breadcrumbs-custom-container">
    <div class="breadcrumbs-custom-inner">
      <div class="breadcrumbs-custom-item">
        <h6>Giới thiệu</h6>
        <h1 class="breadcrumbs-custom-title">Về Chúng Tôi</h1>
      </div>
      <ul class="breadcrumbs-custom-path">
        <li><a href="home.php">Trang chủ</a></li>
        <li class="active">Giới thiệu</li>
      </ul>
    </div>
  </div>
</div>

<section class="section section-lg bg-default">
  <div class="container">
    <div class="row row-50 justify-content-center justify-content-xl-between flex-lg-row-reverse align-items-center">
      <div class="col-md-11 col-lg-6 col-xl-5 wow fadeInLeft">
        <div class="box-1">
          <h2>Đôi nét về V-Learning</h2>
          <h5>Nơi khởi đầu cho hành trình chinh phục tri thức và tương lai của bạn.</h5>
          <p>Được thành lập với sứ mệnh mang lại nền giáo dục chất lượng cao, dễ tiếp cận cho mọi người. 
            Chúng tôi cung cấp các khóa học đa dạng từ Công nghệ thông tin, Kinh tế đến Kỹ năng mềm, giúp học viên trang bị hành trang vững chắc cho sự nghiệp.</p>
          <a class="button button-lg button-primary-gradient" href="courses_list.php">
            <span><i class="mdi mdi-book-open-variant" style="margin-right: 8px;"></i>Xem Khóa Học</span>
          </a>
        </div>
      </div>
      <div class="col-md-11 col-lg-6 wow fadeInRight">
        <div class="thumbnail-media" style="background-image: url('images/dog.jpg');">
            <a class="icon thumbnail-media-icon mdi mdi-play-circle-outline" href="#" onclick="return false;"></a>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section section-lg bg-gray-100 text-center">
  <div class="container">
    <h2 class="wow fadeIn">Chương trình Đào tạo</h2>
    <p class="wow fadeIn" data-wow-delay=".2s" style="max-width: 700px; margin: 20px auto 0;">Chúng tôi cung cấp các lộ trình học tập bài bản, từ cơ bản đến nâng cao, giúp bạn phát triển toàn diện.</p>
    <div class="row row-30 justify-content-center mt-4">
      
      <div class="col-sm-6 col-lg-4 col-xl-3 wow fadeInUp" data-wow-delay=".2s">
        <article class="box-classic">
          <a class="icon box-classic-icon linearicons-calculator2" href="#"></a>
          <a class="box-classic-main" href="#">
            <h4 class="box-classic-title">Kế toán & Tài chính</h4>
            <div class="box-classic-inner">
              <p>Làm chủ các con số và kỹ năng quản lý tài chính doanh nghiệp.</p>
            </div>
          </a>
        </article>
      </div>

      <div class="col-sm-6 col-lg-4 col-xl-3 wow fadeInUp" data-wow-delay=".3s">
        <article class="box-classic">
          <a class="icon box-classic-icon linearicons-briefcase" href="#"></a>
          <a class="box-classic-main" href="#">
            <h4 class="box-classic-title">Quản trị Kinh doanh</h4>
            <div class="box-classic-inner">
              <p>Học cách vận hành và xử lý các vấn đề trong môi trường doanh nghiệp.</p>
            </div>
          </a>
        </article>
      </div>

      <div class="col-sm-6 col-lg-4 col-xl-3 wow fadeInUp" data-wow-delay=".4s">
        <article class="box-classic">
          <a class="icon box-classic-icon linearicons-laptop-phone" href="#"></a>
          <a class="box-classic-main" href="#">
            <h4 class="box-classic-title">Công nghệ thông tin</h4>
            <div class="box-classic-inner">
              <p>Khám phá thế giới lập trình, phát triển phần mềm và công nghệ mới.</p>
            </div>
          </a>
        </article>
      </div>

      <div class="col-sm-6 col-lg-4 col-xl-3 wow fadeInUp" data-wow-delay=".5s">
        <article class="box-classic">
          <a class="icon box-classic-icon linearicons-bubble-user" href="#"></a>
          <a class="box-classic-main" href="#">
            <h4 class="box-classic-title">Đào tạo Online</h4>
            <div class="box-classic-inner">
              <p>Học mọi lúc mọi nơi với hệ thống bài giảng trực tuyến chất lượng cao.</p>
            </div>
          </a>
        </article>
      </div>

    </div>
  </div>
</section>

<section class="section section-lg text-center">
  <div class="container">
    <h2 class="wow fadeIn">Lịch sử phát triển</h2>
    <p class="wow fadeIn" data-wow-delay=".2s" style="max-width: 700px; margin: 25px auto 50px;">Hành trình phát triển của chúng tôi qua các giai đoạn quan trọng</p>
    <div class="row row-30 justify-content-center">
      
      <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay=".2s">
        <article class="timeline-classic-item">
          <p class="timeline-classic-time heading-4">2020-Nay</p>
          <div class="timeline-classic-divider"></div>
          <h4 class="timeline-classic-title">Chuyển đổi số toàn diện</h4>
          <p>Phát triển mạnh mẽ hệ thống E-Learning, tiếp cận hàng nghìn học viên trên toàn quốc.</p>
        </article>
      </div>

      <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay=".3s">
        <article class="timeline-classic-item">
          <p class="timeline-classic-time heading-4">2015-2019</p>
          <div class="timeline-classic-divider"></div>
          <h4 class="timeline-classic-title">Mở rộng quy mô</h4>
          <p>Hợp tác với các doanh nghiệp lớn để cung cấp nhân sự chất lượng cao sau đào tạo.</p>
        </article>
      </div>

      <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay=".4s">
        <article class="timeline-classic-item">
          <p class="timeline-classic-time heading-4">2010-2014</p>
          <div class="timeline-classic-divider"></div>
          <h4 class="timeline-classic-title">Thành lập</h4>
          <p>Bắt đầu với những lớp học offline nhỏ về tin học văn phòng và ngoại ngữ.</p>
        </article>
      </div>

    </div>
  </div>
</section>

<section class="section section-lg bg-default text-center pt-0">
  <div class="container">
    <h2 class="wow fadeIn">Học viên nói gì về chúng tôi?</h2>
    <p class="wow fadeIn" data-wow-delay=".2s" style="max-width: 700px; margin: 25px auto 50px;">Những chia sẻ chân thực từ học viên đã và đang học tập tại V-Learning</p>
    <div class="row row-30 justify-content-center">
      
      <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay=".2s">
        <blockquote class="quote-classic">
          <div class="quote-classic-avatar-outer">
            <img class="quote-classic-avatar" src="images/testimonials-1-68x68.jpg" alt="Nguyễn Văn A" width="68" height="68"/>
          </div>
          <div class="quote-classic-main">
            <p class="heading-5 quote-classic-cite">Nguyễn Văn A</p>
            <p class="quote-classic-position">Lập trình viên</p>
            <div class="quote-classic-text">
              <q>Khóa học rất thực tế, giảng viên nhiệt tình. Tôi đã tìm được việc làm ngay sau khi tốt nghiệp khóa PHP.</q>
            </div>
          </div>
        </blockquote>
      </div>

      <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay=".3s">
        <blockquote class="quote-classic">
          <div class="quote-classic-avatar-outer">
            <img class="quote-classic-avatar" src="images/testimonials-2-68x68.jpg" alt="Trần Thị B" width="68" height="68"/>
          </div>
          <div class="quote-classic-main">
            <p class="heading-5 quote-classic-cite">Trần Thị B</p>
            <p class="quote-classic-position">Sinh viên</p>
            <div class="quote-classic-text">
              <q>Môi trường học tập thân thiện, tài liệu phong phú. Hệ thống học online rất mượt mà.</q>
            </div>
          </div>
        </blockquote>
      </div>

      <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay=".4s">
        <blockquote class="quote-classic">
          <div class="quote-classic-avatar-outer">
            <img class="quote-classic-avatar" src="images/testimonials-3-68x68.jpg" alt="Lê Văn C" width="68" height="68" style="background: linear-gradient(135deg, #667eea, #764ba2);"/>
          </div>
          <div class="quote-classic-main">
            <p class="heading-5 quote-classic-cite">Lê Văn C</p>
            <p class="quote-classic-position">Designer</p>
            <div class="quote-classic-text">
              <q>Các khóa học kỹ năng mềm ở đây giúp tôi tự tin hơn rất nhiều trong công việc và cuộc sống.</q>
            </div>
          </div>
        </blockquote>
      </div>

    </div>
  </div>
</section>

<?php 
include 'footer.php'; 
?>