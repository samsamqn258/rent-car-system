<?php include 'views/shared/header.php';
require_once __DIR__ . '/../../utils/OpenStreetMap.php';
?>

<div class="container mt-4">
  <div class="row">
    <!-- Car Images Slider -->
    <div class="col-md-7">
      <div id="carImagesCarousel" class="carousel slide" data-bs-ride="carousel">
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
      </div>
    </div>

    <!-- Car Details -->
    <div class="col-md-5">
      <h2><?php echo $car_details['brand'] . ' ' . $car_details['model'] . ' (' . $car_details['year'] . ')'; ?></h2>

      <div class="car-rating mb-3">
        <?php
                $rating = $car_details['avg_rating'] ? round($car_details['avg_rating']) : 0;
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= $rating) {
                        echo '<i class="fas fa-star text-warning"></i>';
                    } else {
                        echo '<i class="far fa-star text-warning"></i>';
                    }
                }
                echo ' <span>(' . $car_details['review_count'] . ' reviews)</span>';
                ?>
      </div>

      <div class="car-price mb-3">
        <h3 class="text-primary"><?php echo number_format($car_details['price_per_day'], 0, ',', '.'); ?> VND / ngày
        </h3>
      </div>

      <div class="car-specs mb-3">
        <p><strong>Loại xe:</strong>
          <?php echo $car_details['car_type'] == 'electric' ? 'Xe điện' : ($car_details['car_type'] == 'gasoline' ? 'Xe xăng' : 'Xe dầu'); ?>
        </p>
        <p><strong>Số chỗ ngồi:</strong> <?php echo $car_details['seats']; ?></p>
        <p><strong>Chủ xe:</strong> <?php echo $car_details['owner_name']; ?></p>
      </div>

      <div class="car-address mb-3">
        <p><strong>Địa chỉ:</strong> <?php echo $car_details['address']; ?></p>
      </div>

      <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $car_details['owner_id']): ?>
      <a href="<?php echo BASE_URL . '/booking/create/' . $car_details['id']; ?>"
        class="btn btn-primary btn-lg w-100 mb-3">Đặt xe ngay</a>
      <?php elseif (!isset($_SESSION['user_id'])): ?>
      <a href="<?php echo BASE_URL . '/auth/login'; ?>" class="btn btn-primary btn-lg w-100 mb-3">Đăng nhập để đặt
        xe</a>
      <?php endif; ?>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-md-7">
      <!-- Car Description -->
      <div class="card mb-4">
        <div class="card-header">
          <h3>Mô tả xe</h3>
        </div>
        <div class="card-body">
          <p><?php echo nl2br($car_details['description']); ?></p>
        </div>
      </div>

      <!-- Car Reviews -->
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h3>Đánh giá từ khách hàng</h3>
          <span class="badge bg-primary"><?php echo count($car_details['reviews']); ?> đánh giá</span>
        </div>
        <div class="card-body">
          <?php if (empty($car_details['reviews'])): ?>
          <p class="text-muted">Chưa có đánh giá nào cho xe này.</p>
          <?php else: ?>
          <?php foreach ($car_details['reviews'] as $review): ?>
          <div class="review-item mb-3 pb-3 border-bottom">
            <div class="d-flex justify-content-between">
              <div>
                <h5><?php echo $review['user_name']; ?></h5>
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
              <small class="text-muted"><?php echo date('d/m/Y', strtotime($review['created_at'])); ?></small>
            </div>
            <p class="mt-2"><?php echo nl2br($review['comment']); ?></p>
          </div>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-md-5">
      <!-- Car Location Map -->
      <div class="card">
        <div class="card-header">
          <h3>Vị trí xe</h3>
        </div>
        <div class="card-body p-0">
          <?php echo OpenStreetMap::generateMap($car_details['latitude'], $car_details['longitude'], 15, '100%', '400px'); ?>
        </div>
        <div class="card-footer">
          <p class="mb-0"><strong>Địa chỉ:</strong> <?php echo $car_details['address']; ?></p>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'views/shared/footer.php'; ?>