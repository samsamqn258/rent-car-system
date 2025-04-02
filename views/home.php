<?php include 'views/shared/header.php'; ?>

<!-- Hero Banner -->

<div class="hero-banner position-relative">
  <div class="hero-overlay  ">
    <img src="<?php echo BASE_URL; ?>/public/images/pexels-photo-1392621.jpeg"
      style="height: 100%; width: 100%; object-fit: cover;" class="opacity-75" />
  </div>
  <div class="container">
    <div class="row" style="justify-content: center;">
      <div class="col-md-8">
        <div class="hero-content">
          <h1 class="hero-title">Thuê xe tự lái dễ dàng</h1>
          <p class="hero-subtitle">Khám phá hàng ngàn xe <strong style="color:#5fcf86">chất lượng cao</strong> từ các
            chủ xe đáng tin cậy</p>
        </div>
      </div>
    </div>
  </div>
  <!-- Search Form -->
  <div class="search-form-container position-absolute shadow start-50 end-50 translate-middle">
    <form action="<?php echo BASE_URL; ?>/cars/searchAddress" method="get" class="search-form">
      <div class="row g-3">
        <div class="col-md-4 border-end">
          <div class="form-floating">
            <input type="text" class="form-control border-0" id="address" name="address" placeholder="Nhập địa điểm">
            <label for="address">Lựa chọn địa điểm</label>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-floating">
            <input type="date" class="form-control border-" id="start_date" name="start_date"
              min="<?php echo date('Y-m-d'); ?>" value="<?php echo date('Y-m-d'); ?>">
            <label for="start_date">Ngày bắt đầu</label>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-floating">
            <input type="date" class="form-control border-" id="end_date" name="end_date"
              min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
              value="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
            <label for="end_date">Ngày kết thúc</label>
          </div>
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn h-100 text-white fw-bold" style="background-color: #5fcf86; width: 100%">
            Tìm Xe
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Promotions -->
<div class="container promotions">
  <div class="d-flex flex-column justify-content-center align-items-center mb-4">
    <h2 class="heading-title">Chương trình khuyến mãi</h2>
    <p class="heading-subtitle mt-2">Nhận nhiều hấp dẫn từ chúng tôi</p>
  </div>
  <!-- Carousel promotions -->
  <div style="margin-top:30px ;">
    <div id="promotionsCarousel" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <div class="row">
            <div class="col-md-4">
              <img src="<?php echo BASE_URL; ?>/public/images/khuyenmai.jpg" class="d-block w-100" alt="Promotion 1">
            </div>
            <div class="col-md-4">
              <img src="<?php echo BASE_URL; ?>/public/images/khuyenmai.jpg" class="d-block w-100" alt="Promotion 2">
            </div>
            <div class="col-md-4">
              <img src="<?php echo BASE_URL; ?>/public/images/khuyenmai.jpg" class="d-block w-100" alt="Promotion 3">
            </div>
          </div>
        </div>
        <div class="carousel-item">
          <div class="row">
            <div class="col-md-4">
              <img src="<?php echo BASE_URL; ?>/public/images/khuyenmai.jpg" class="d-block w-100" alt="Promotion 4">
            </div>
            <div class="col-md-4">
              <img src="<?php echo BASE_URL; ?>/public/images/khuyenmai.jpg" class="d-block w-100" alt="Promotion 5">
            </div>
            <div class="col-md-4">
              <img src="<?php echo BASE_URL; ?>/public/images/khuyenmai.jpg" class="d-block w-100" alt="Promotion 6">
            </div>
          </div>
        </div>
      </div>
      <!-- Controls -->
      <button class="carousel-control-prev" type="button" data-bs-target="#promotionsCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#promotionsCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
  </div>
</div>


<!-- Featured Cars -->
<div class="container" style="margin-top: 100px;">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="heading-title">Xe nổi bật</h2>
    <a href="<?php echo BASE_URL; ?>/cars/search" class="btn" style="border: 1px solid #5fcf86; color: #5fcf86;">Xem tất
      cả</a>
  </div>

  <div class="row">
    <?php foreach ($featured_cars as $car): ?>
      <a href="<?php echo BASE_URL; ?>/cars/details/<?php echo $car['id']; ?>" class="card-car col-md-3 mb-4">
        <div class="card h-100">
          <img src="<?php echo BASE_URL . '/' . $car['primary_image']; ?>" class="card-img-top"
            alt="<?php echo $car['brand'] . ' ' . $car['model']; ?>" style="height: 200px; object-fit: contain;">
          <!-- <div>
            <pre><?php print_r($car); ?></pre>
          </div> -->
          <div class="card-body">
            <h5 class="card-title"><?php echo $car['brand'] . ' ' . $car['model'] . ' (' . $car['year'] . ')'; ?></h5>


            <div class="car-specs">
              <span class="badge bg-secondary me-1">
                <i class="fas fa-car me-1"></i>
                <?php echo $car['car_type'] == 'electric' ? 'Xe điện' : ($car['car_type'] == 'gasoline' ? 'Xe xăng' : 'Xe dầu'); ?>
              </span>
              <span class="badge bg-secondary me-1">
                <i class="fas fa-user me-1"></i>
                <?php echo $car['seats']; ?> chỗ
              </span>
            </div>

            <div style="color: #767676; font-size: 0.875rem; margin-top: 10px;">
              <img src="https://cdn0.iconfinder.com/data/icons/4web-3/139/location-512.png"
                style="width:16px; height: 16px" />
              <?php echo $car['address']; ?>

            </div>
          </div>

          <div class=" card-footer">
            <div class="d-flex align-items-center justify-content-between">
              <div class="car-rating d-flex align-items-center">
                <i class="fas fa-star text-warning"></i>
                <p style=" margin-top: 1px; margin-left: 3px; margin-bottom: 0; color: #767676; font-size: 0.875rem">
                  <?php echo $car['review_count'] === 0 ? "5.0" : $car['review_count']; ?>
                </p>
              </div>

              <div class="car-price fw-bold " style="color: #5fcf86; font-size: 1rem">
                <?php echo number_format($car['price_per_day'], 0, ',', '.'); ?> VND/ngày
              </div>
            </div>
          </div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</div>

<!-- How It Works -->
<div class="bg-light py-5 mt-5">
  <div class="container">
    <h2 class="text-center mb-5 heading-title">Cách thức hoạt động</h2>

    <div class="row">
      <div class="col-md-3 text-center mb-4 mb-md-0">
        <div class="how-it-works-item d-flex align-items-center flex-column">
          <div class="icon-container mb-3 d-flex align-items-center justify-content-center">
            <i class="fas fa-search fa-3x " style="color: #5fcf86;"></i>
          </div>
          <h4>Tìm xe</h4>
          <p>Tìm kiếm xe dựa theo địa điểm, thời gian và nhu cầu của bạn.</p>
        </div>
      </div>

      <div class="col-md-3 text-center mb-4 mb-md-0">
        <div class="how-it-works-item d-flex align-items-center flex-column">
          <div class="icon-container mb-3 d-flex align-items-center justify-content-center">
            <i class="fas fa-calendar-check fa-3x " style="color: #5fcf86;"></i>
          </div>
          <h4>Đặt xe</h4>
          <p>Chọn xe phù hợp với bạn và gửi yêu cầu đặt xe đến chủ xe.</p>
        </div>
      </div>

      <div class="col-md-3 text-center mb-4 mb-md-0">
        <div class="how-it-works-item d-flex align-items-center flex-column">
          <div class="icon-container mb-3 d-flex align-items-center justify-content-center">
            <i class="fas fa-credit-card fa-3x " style="color: #5fcf86;"></i>
          </div>
          <h4>Thanh toán</h4>
          <p>Thanh toán an toàn qua MoMo. Không có phí ẩn hay phụ phí.</p>
        </div>
      </div>

      <div class="col-md-3 text-center">
        <div class="how-it-works-item d-flex align-items-center flex-column">
          <div class="icon-container mb-3 d-flex align-items-center justify-content-center">
            <i class="fas fa-car fa-3x " style="color: #5fcf86;"></i>
          </div>
          <h4>Nhận xe</h4>
          <p>Nhận xe từ chủ xe, kiểm tra và bắt đầu hành trình của bạn.</p>
        </div>
      </div>
    </div>

    <div class="text-center mt-4">
      <a href="<?php echo BASE_URL; ?>/cars/search" class="btn h-100 text-white fw-bold"
        style="background-color: #5fcf86; width: 150px">Tìm xe ngay</a>
    </div>
  </div>
</div>

<!-- Car Categories -->
<div class="container mt-5">
  <h2 class="text-center mb-4 heading-title">Loại xe phổ biến</h2>

  <div class="row">
    <div class="col-md-4 mb-4">
      <div class="card category-card">
        <img src="<?php echo BASE_URL; ?>/public/images/pngimg.com - mitsubishi_PNG20.png" class="card-img" alt="Sedan">
        <div class="card-img-overlay d-flex align-items-end">
          <div class="category-content">
            <h3 class="card-title">Sedan</h3>
            <p class="card-text mb-2">Xe 4 chỗ tiện nghi, phù hợp cho gia đình nhỏ</p>
            <a href="<?php echo BASE_URL; ?>/cars/search?car_type=gasoline&seats=4" class="btn btn-light">Xem ngay</a>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4 mb-4">
      <div class="card category-card">
        <img src="<?php echo BASE_URL; ?>/public/images/pngtree-suv-car-png-image_15682146.png" class="card-img"
          alt="SUV">
        <div class="card-img-overlay d-flex align-items-end">
          <div class="category-content">
            <h3 class="card-title">SUV</h3>
            <p class="card-text mb-2">Xe 7 chỗ rộng rãi, phù hợp cho gia đình lớn</p>
            <a href="<?php echo BASE_URL; ?>/cars/search?car_type=gasoline&seats=7" class="btn btn-light">Xem ngay</a>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4 mb-4">
      <div class="card category-card">
        <img
          src="<?php echo BASE_URL; ?>/public/images/pngtree-tesla-model-3-red-transparent-background-png-image_10337019.png"
          class="card-img" alt="Electric">
        <div class="card-img-overlay d-flex align-items-end">
          <div class="category-content">
            <h3 class="card-title">Xe điện</h3>
            <p class="card-text mb-2">Xe thân thiện với môi trường, tiết kiệm chi phí</p>
            <a href="<?php echo BASE_URL; ?>/cars/search?car_type=electric" class="btn btn-light">Xem ngay</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Testimonials -->
<div class="bg-light py-5 mt-5">
  <div class="container">
    <h2 class="text-center mb-5 heading-title">Khách hàng nói gì về chúng tôi</h2>

    <div class="row">
      <?php foreach ($testimonials as $testimonial): ?>
        <div class="col-md-4 mb-4">
          <div class="card h-100">
            <div class="card-body">
              <div class="d-flex align-items-center mb-3">
                <img src="<?php echo BASE_URL; ?>/public/images/avatar/<?php echo $testimonial['avatar']; ?>"
                  alt="<?php echo $testimonial['name']; ?>" class="rounded-circle me-3" width="60">
                <div>
                  <h5 class="mb-0"><?php echo $testimonial['name']; ?></h5>
                  <div class="text-warning">
                    <?php
                    for ($i = 1; $i <= 5; $i++) {
                      if ($i <= $testimonial['rating']) {
                        echo '<i class="fas fa-star"></i>';
                      } else {
                        echo '<i class="far fa-star"></i>';
                      }
                    }
                    ?>
                  </div>
                </div>
              </div>
              <p class="card-text">"<?php echo $testimonial['comment']; ?>"</p>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- Become a Car Owner -->
<div class="container mt-5 mb-5">
  <div class="row align-items-center">
    <div class="col-md-6">

      <h2 class="heading-title">Bạn có xe và muốn cho thuê?</h2>
      <p class="lead">Hãy trở thành chủ xe và kiếm thêm thu nhập từ chiếc xe của bạn!</p>
      <ul class="list-unstyled">
        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Kiếm thêm thu nhập</li>
        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Linh hoạt về thời gian</li>
        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Đăng ký dễ dàng</li>
        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Được bảo vệ bởi các chính sách đảm bảo</li>
      </ul>
      <a href="<?php echo BASE_URL; ?>/auth/register_owner" class="btn h-100 text-white fw-bold"
        style="background-color: #5fcf86; width: 100%">Đăng ký làm chủ xe</a>
    </div>
    <div class="col-md-6">
      <img src="<?php echo BASE_URL; ?>/public/images/pexels-photo-1287351.jpeg" class="img-fluid" alt="Become an owner">
    </div>
  </div>
</div>

<style>
  .heading-title {
    font-size: 2rem;
    font-weight: 600;
  }

  .heading-subtitle {
    font-size: 1.2rem
  }

  .hero-banner {
    background-image: url('<?php echo BASE_URL; ?>/public/images/hero-banner.jpg');
    background-size: cover;
    background-position: center;
    height: 600px;
    position: relative;
    color: white;
    display: flex;
    align-items: center;
  }

  .hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
  }

  .hero-content {
    position: relative;
    z-index: 1;
    text-align: center;
  }

  .hero-title {
    font-size: 4rem;
    font-weight: bold;
    margin-bottom: 1rem;
  }

  .hero-subtitle {
    font-size: 2rem;
    margin-bottom: 2rem;
  }

  .search-form-container {
    background-color: #fff;
    padding: 30px;
    border-radius: 10px;
    width: 850px;
    bottom: -110px;
    z-index: 10;
  }

  .how-it-works-item {
    padding: 20px;
  }

  .icon-container {
    display: inline-block;
    width: 80px;
    height: 80px;
    line-height: 80px;
    border-radius: 50%;
    background-color: #f8f9fa;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
  }

  /* Promotions */
  .promotions {
    margin-top: 100px;
    padding-top: 80px;
  }

  .card-car {
    color: #000;
    text-decoration: none;
  }

  .category-card {
    overflow: hidden;
    border: none;
    border-radius: 10px;
  }

  .category-card img {
    height: 250px;
    object-fit: cover;
    transition: transform 0.3s ease;
  }

  .category-card:hover img {
    transform: scale(1.05);
  }

  .category-content {
    background-color: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 15px;
    border-radius: 0 0 10px 10px;
    width: 100%;
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Set minimum end date based on start date
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    startDateInput.addEventListener('change', function() {
      const startDate = new Date(this.value);
      const nextDay = new Date(startDate);
      nextDay.setDate(nextDay.getDate() + 1);

      const formattedDate = nextDay.toISOString().split('T')[0];
      endDateInput.min = formattedDate;

      if (new Date(endDateInput.value) < nextDay) {
        endDateInput.value = formattedDate;
      }
    });
  });
</script>

<?php include 'views/shared/footer.php'; ?>