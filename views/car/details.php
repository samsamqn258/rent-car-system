<?php include 'views/shared/header.php';
require_once __DIR__ . '/../../utils/OpenStreetMap.php';
?>

<div class="container mt-4">
  <div class="row">
    <!-- Car Images Slider -->
    <div class="col-md-7">
      <div id="carImagesCarousel" class="carousel slide rounded overflow-hidden shadow-sm" data-bs-ride="carousel">
        <div class="carousel-inner">
          <?php
          $first = true;
          foreach ($car_details['images'] as $image): ?>
            <div class="carousel-item <?php echo $first ? 'active' : ''; ?>">
              <img src="<?php echo BASE_URL . '/' . $image['image_path']; ?>" class="d-block w-100"
                alt="<?php echo $car_details['brand'] . ' ' . $car_details['model']; ?>"
                style="height: 400px; object-fit: cover;">
            </div>
          <?php
            $first = false;
          endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carImagesCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carImagesCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
        <!-- Optional: Add indicators -->
        <div class="carousel-indicators">
          <?php
          $index = 0;
          foreach ($car_details['images'] as $image): ?>
            <button type="button" data-bs-target="#carImagesCarousel" data-bs-slide-to="<?php echo $index; ?>"
              <?php echo $index === 0 ? 'class="active"' : ''; ?>></button>
          <?php
            $index++;
          endforeach; ?>
        </div>
      </div>

      <!-- Car Location Map - MOVED HERE FROM BELOW -->
      <div class="card border-0 shadow-sm my-4">
        <div class="card-header bg-white border-bottom">
          <h3 class="fs-5 fw-semibold mb-0">Vị trí xe</h3>
        </div>
        <div class="card-body p-0">
          <?php echo OpenStreetMap::generateMap($car_details['latitude'], $car_details['longitude'], 15, '100%', '400px'); ?>
        </div>
        <div class="card-footer bg-white">
          <div class="d-flex align-items-center">
            <i class="fas fa-map-marker-alt text-danger me-2"></i>
            <p class="mb-0 small"><strong>Địa chỉ:</strong> <?php echo $car_details['address']; ?></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Car Details -->
    <div class="col-md-5">
      <div class="top-24 card border-0 shadow-sm" style="max-height: calc(100vh - 120px); overflow-y: auto;">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
              <h2 class="fw-bold"><?php echo $car_details['brand'] . ' ' . $car_details['model'] . ' (' . $car_details['year'] . ')'; ?></h2>
              <div class="car-rating">
                <?php
                $rating = $car_details['avg_rating'] ? round($car_details['avg_rating']) : 0;
                for ($i = 1; $i <= 5; $i++) {
                  if ($i <= $rating) {
                    echo '<i class="fas fa-star text-warning"></i>';
                  } else {
                    echo '<i class="far fa-star text-warning"></i>';
                  }
                }
                echo ' <span class="text-muted">(' . $car_details['review_count'] . ' đánh giá)</span>';
                ?>
              </div>
            </div>
            <div class="d-flex gap-2">
              <button class="btn btn-sm btn-outline-secondary rounded-circle">
                <i class="far fa-heart"></i>
              </button>
              <button class="btn btn-sm btn-outline-secondary rounded-circle">
                <i class="fas fa-share-alt"></i>
              </button>
            </div>
          </div>
          <!-- Location Info -->
          <div class="car-address mb-4">
            <div class="d-flex align-items-center">
              <i class="fas fa-map-marker-alt text-danger me-2"></i>
              <span class="fw-semibold"><?php echo $car_details['address']; ?></span>
            </div>
          </div>
          <!-- Promotion Banner -->
          <div class="bg-light p-3 rounded mt-3">
            <div class="d-flex align-items-center text-success mb-1">
              <i class="fas fa-shield-alt me-2"></i>
              <span class="small fw-semibold">Miễn cọc tiền mặt</span>
            </div>
            <p class="small text-muted mb-0">
              Chuyến đi của bạn đã được đảm bảo. Bạn chỉ cần mang GPLX & CCCD/Passport khi nhận xe.
            </p>
          </div>
          <div class="mb-3 mt-2">
            <h4 class="text-danger fw-semibold mb-1 small">Ưu đãi: Giảm 10% cho chuyến đi</h4>
            <p class="text-muted mb-0 small">Nhập mã "MIOTO10" để được giảm 10% tối đa 300k cho chuyến đi</p>
          </div>
          <div class="car-price mb-4">
            <h3 class="text-success fw-bold"><?php echo number_format($car_details['price_per_day'], 0, ',', '.'); ?> VND <span class="text-muted fs-6 fw-normal">/ ngày</span></h3>
          </div>

          <!-- Car Attributes -->
          <div class="car-attributes row mb-4 text-center g-0">
            <div class="col-3">
              <div class="p-2">
                <i class="fas fa-car-side text-muted mb-2"></i>
                <div class="text-muted small">Truyền động</div>
                <div class="small fw-semibold">Tự động</div>
              </div>
            </div>
            <div class="col-3">
              <div class="p-2">
                <i class="fas fa-gas-pump text-muted mb-2"></i>
                <div class="text-muted small">Nhiên liệu</div>
                <div class="small fw-semibold">
                  <?php echo $car_details['car_type'] == 'electric' ? 'Điện' : ($car_details['car_type'] == 'gasoline' ? 'Xăng' : 'Dầu'); ?>
                </div>
              </div>
            </div>
            <div class="col-3">
              <div class="p-2">
                <i class="fas fa-users text-muted mb-2"></i>
                <div class="text-muted small">Số chỗ</div>
                <div class="small fw-semibold"><?php echo $car_details['seats']; ?> chỗ</div>
              </div>
            </div>
            <div class="col-3">
              <div class="p-2">
                <i class="fas fa-user text-muted mb-2"></i>
                <div class="text-muted small">Chủ xe</div>
                <div class="small fw-semibold text-truncate" title="<?php echo $car_details['owner_name']; ?>">
                  <?php echo $car_details['owner_name']; ?>
                </div>
              </div>
            </div>
          </div>

          <!-- Booking CTA -->
          <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $car_details['owner_id']): ?>
            <a href="<?php echo BASE_URL . '/booking/create/' . $car_details['id']; ?>"
              class="btn btn-success btn-lg w-100 mb-3 fw-semibold">ĐẶT XE NGAY</a>
          <?php elseif (!isset($_SESSION['user_id'])): ?>
            <a href="<?php echo BASE_URL . '/auth/login'; ?>"
              class="btn btn-success btn-lg w-100 mb-3 fw-semibold">ĐĂNG NHẬP ĐỂ ĐẶT XE</a>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="row mt-4">
      <div class="col-md-7">
        <!-- Car Description -->
        <div class="card mb-4 border-0 shadow-sm">
          <div class="card-header bg-white border-bottom">
            <h3 class="fs-5 fw-semibold mb-0">Mô tả xe</h3>
          </div>
          <div class="card-body">
            <p><?php echo nl2br($car_details['description']); ?></p>
            <!-- <button class="btn btn-link text-success p-0">Xem thêm</button> -->
          </div>
        </div>

        <!-- Car Features -->
        <div class="card mb-4 border-0 shadow-sm">
          <div class="card-header bg-white border-bottom">
            <h3 class="fs-5 fw-semibold mb-0">Các tiện nghi khác</h3>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-6 col-md-3 mb-3">
                <div class="d-flex align-items-center">
                  <i class="fas fa-snowflake text-muted me-2"></i>
                  <span class="small">Điều hòa</span>
                </div>
              </div>
              <div class="col-6 col-md-3 mb-3">
                <div class="d-flex align-items-center">
                  <i class="fas fa-bluetooth-b text-muted me-2"></i>
                  <span class="small">Bluetooth</span>
                </div>
              </div>
              <div class="col-6 col-md-3 mb-3">
                <div class="d-flex align-items-center">
                  <i class="fas fa-camera text-muted me-2"></i>
                  <span class="small">Camera 360</span>
                </div>
              </div>
              <div class="col-6 col-md-3 mb-3">
                <div class="d-flex align-items-center">
                  <i class="fas fa-map-marker-alt text-muted me-2"></i>
                  <span class="small">Camera lùi</span>
                </div>
              </div>
              <div class="col-6 col-md-3 mb-3">
                <div class="d-flex align-items-center">
                  <i class="fas fa-compass text-muted me-2"></i>
                  <span class="small">Định vị GPS</span>
                </div>
              </div>
              <div class="col-6 col-md-3 mb-3">
                <div class="d-flex align-items-center">
                  <i class="fas fa-usb text-muted me-2"></i>
                  <span class="small">Khe cắm USB</span>
                </div>
              </div>
              <div class="col-6 col-md-3 mb-3">
                <div class="d-flex align-items-center">
                  <i class="fas fa-wifi text-muted me-2"></i>
                  <span class="small">Wifi</span>
                </div>
              </div>
              <div class="col-6 col-md-3 mb-3">
                <div class="d-flex align-items-center">
                  <i class="fas fa-charging-station text-muted me-2"></i>
                  <span class="small">ETC</span>
                </div>
              </div>
            </div>
            <!-- <button class="btn btn-link text-success p-0">Xem thêm</button> -->
          </div>
        </div>

        <!-- Document Requirements -->
        <div class="card mb-4 border-0 shadow-sm">
          <div class="card-header bg-white border-bottom">
            <h3 class="fs-5 fw-semibold mb-0">Giấy tờ thuê xe</h3>
          </div>
          <div class="card-body">
            <div class="d-flex mb-2">
              <div class="bg-warning" style="width:3px; margin-right:15px;"></div>
              <div class="small">CMND/CCCD gắn chip & GPLX (đặt cọc 5 triệu)</div>
            </div>
            <div class="d-flex">
              <div class="bg-warning" style="width:3px; margin-right:15px;"></div>
              <div class="small">CMND/CCCD gắn chip & Hộ khẩu/KT3/Passport (đặt cọc 15 triệu)</div>
            </div>
          </div>
        </div>

        <!-- Car Reviews -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
            <h3 class="fs-5 fw-semibold mb-0">Đánh giá từ khách hàng</h3>
            <span class="badge bg-success rounded-pill"><?php echo count($car_details['reviews']); ?> đánh giá</span>
          </div>
          <div class="card-body">
            <?php if (empty($car_details['reviews'])): ?>
              <p class="text-muted">Chưa có đánh giá nào cho xe này.</p>
            <?php else: ?>
              <?php foreach ($car_details['reviews'] as $review): ?>
                <div class="review-item mb-3 pb-3 border-bottom">
                  <div class="d-flex justify-content-between">
                    <div class="d-flex">
                      <div class="me-3">
                        <!-- Add user avatar if available -->
                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width:40px; height:40px;">
                          <i class="fas fa-user"></i>
                        </div>
                      </div>
                      <div>
                        <h5 class="fs-6 fw-semibold mb-1"><?php echo $review['user_name']; ?></h5>
                        <div class="review-rating">
                          <?php
                          for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $review['rating']) {
                              echo '<i class="fas fa-star text-warning"></i>';
                            } else {
                              echo '<i class="far fa-star text-warning"></i>';
                            }
                          }
                          ?>
                        </div>
                      </div>
                    </div>
                    <small class="text-muted"><?php echo date('d/m/Y', strtotime($review['created_at'])); ?></small>
                  </div>
                  <p class="mt-2 small"><?php echo nl2br($review['comment']); ?></p>
                </div>
              <?php endforeach; ?>
              <a href="#" class="btn btn-link text-success p-0">Xem thêm đánh giá</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <?php include 'views/shared/footer.php'; ?>