<?php include 'views/shared/header.php'; ?>

<div class="container mt-4">
  <div class="row">
    <!-- Search Filters -->
    <div class="col-md-3">
      <div class="card mb-4">
        <div class="card-header bg-primary text-white">
          <h4 class="mb-0">Bộ lọc tìm kiếm</h4>
        </div>
        <div class="card-body">
          <form action="<?php echo BASE_URL; ?>/cars/search" method="get" id="search-form">
            <!-- Price Range -->
            <div class="mb-3">
              <label class="form-label fw-bold">Khoảng giá (VND/ngày)</label>
              <div class="row g-2">
                <div class="col-6">
                  <input type="number" class="form-control" name="min_price" placeholder="Tối thiểu"
                    value="<?php echo isset($_GET['min_price']) ? $_GET['min_price'] : ''; ?>">
                </div>
                <div class="col-6">
                  <input type="number" class="form-control" name="max_price" placeholder="Tối đa"
                    value="<?php echo isset($_GET['max_price']) ? $_GET['max_price'] : ''; ?>">
                </div>
              </div>
            </div>

            <!-- Car Brand -->
            <div class="mb-3">
              <label for="brand" class="form-label fw-bold">Hãng xe</label>
              <select class="form-select" id="brand" name="brand">
                <option value="">Tất cả hãng xe</option>
                <?php foreach ($brands as $brand): ?>
                <option value="<?php echo $brand; ?>"
                  <?php echo (isset($_GET['brand']) && $_GET['brand'] == $brand) ? 'selected' : ''; ?>>
                  <?php echo $brand; ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- Car Type -->
            <div class="mb-3">
              <label for="car_type" class="form-label fw-bold">Loại xe</label>
              <select class="form-select" id="car_type" name="car_type">
                <option value="">Tất cả loại xe</option>
                <option value="electric"
                  <?php echo (isset($_GET['car_type']) && $_GET['car_type'] == 'electric') ? 'selected' : ''; ?>>Xe điện
                </option>
                <option value="gasoline"
                  <?php echo (isset($_GET['car_type']) && $_GET['car_type'] == 'gasoline') ? 'selected' : ''; ?>>Xe xăng
                </option>
                <option value="diesel"
                  <?php echo (isset($_GET['car_type']) && $_GET['car_type'] == 'diesel') ? 'selected' : ''; ?>>Xe dầu
                </option>
              </select>
            </div>

            <!-- Number of Seats -->
            <div class="mb-3">
              <label for="seats" class="form-label fw-bold">Số chỗ ngồi</label>
              <select class="form-select" id="seats" name="seats">
                <option value="">Tất cả số chỗ</option>
                <option value="4" <?php echo (isset($_GET['seats']) && $_GET['seats'] == '4') ? 'selected' : ''; ?>>4
                  chỗ</option>
                <option value="5" <?php echo (isset($_GET['seats']) && $_GET['seats'] == '5') ? 'selected' : ''; ?>>5
                  chỗ</option>
                <option value="7" <?php echo (isset($_GET['seats']) && $_GET['seats'] == '7') ? 'selected' : ''; ?>>7
                  chỗ</option>
                <option value="8" <?php echo (isset($_GET['seats']) && $_GET['seats'] == '8') ? 'selected' : ''; ?>>8+
                  chỗ</option>
              </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">
              <i class="fas fa-search me-2"></i> Tìm kiếm
            </button>
          </form>
        </div>
      </div>

      <!-- Quick Tips -->
      <div class="card">
        <div class="card-header bg-info text-white">
          <h5 class="mb-0">Mẹo tìm xe</h5>
        </div>
        <div class="card-body">
          <ul class="list-unstyled">
            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Đặt xe sớm để có nhiều lựa chọn</li>
            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Tìm theo khu vực gần bạn</li>
            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Kiểm tra đánh giá và nhận xét</li>
            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> So sánh giá từ nhiều chủ xe</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Car List -->
    <div class="col-md-9">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Danh sách xe</h2>
        <div class="d-flex align-items-center">
          <span class="me-2">Sắp xếp theo:</span>
          <select class="form-select form-select-sm" id="sort-cars">
            <option value="price_asc">Giá tăng dần</option>
            <option value="price_desc">Giá giảm dần</option>
            <option value="rating_desc">Đánh giá cao nhất</option>
            <option value="newest">Mới nhất</option>
          </select>
        </div>
      </div>

      <?php if (empty($cars)): ?>
      <div class="alert alert-info">
        <h4 class="alert-heading">Không tìm thấy xe!</h4>
        <p>Không có xe nào phù hợp với tiêu chí tìm kiếm của bạn. Vui lòng thử lại với các tiêu chí khác.</p>
      </div>
      <?php else: ?>
      <div class="row" id="car-list">
        <?php foreach ($cars as $car): ?>
        <div class="col-md-6 col-lg-4 mb-4 car-item" data-price="<?php echo $car['price_per_day']; ?>"
          data-rating="<?php echo $car['avg_rating'] ? $car['avg_rating'] : 0; ?>"
          data-date="<?php echo strtotime($car['created_at']); ?>">
          <div class="card h-100">
            <img src="<?php echo BASE_URL . '/' . $car['primary_image']; ?>" class="card-img-top"
              alt="<?php echo $car['brand'] . ' ' . $car['model']; ?>" style="height: 200px; object-fit: cover;">

            <div class="card-body">
              <h5 class="card-title"><?php echo $car['brand'] . ' ' . $car['model'] . ' (' . $car['year'] . ')'; ?></h5>

              <div class="car-rating mb-2">
                <?php
                                        $rating = $car['avg_rating'] ? round($car['avg_rating']) : 0;
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $rating) {
                                                echo '<i class="fas fa-star text-warning"></i>';
                                            } else {
                                                echo '<i class="far fa-star text-warning"></i>';
                                            }
                                        }
                                        echo ' <span class="text-muted">(' . $car['review_count'] . ')</span>';
                                        ?>
              </div>

              <div class="car-price text-primary fw-bold mb-2">
                <?php echo number_format($car['price_per_day'], 0, ',', '.'); ?> VND/ngày
              </div>

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
            </div>

            <div class="card-footer">
              <div class="d-grid">
                <a href="<?php echo BASE_URL; ?>/cars/details/<?php echo $car['id']; ?>" class="btn btn-primary">
                  Xem chi tiết
                </a>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Sort cars
  const sortSelect = document.getElementById('sort-cars');
  const carList = document.getElementById('car-list');

  sortSelect.addEventListener('change', function() {
    const cars = Array.from(document.querySelectorAll('.car-item'));
    const sortValue = this.value;

    cars.sort(function(a, b) {
      if (sortValue === 'price_asc') {
        return a.dataset.price - b.dataset.price;
      } else if (sortValue === 'price_desc') {
        return b.dataset.price - a.dataset.price;
      } else if (sortValue === 'rating_desc') {
        return b.dataset.rating - a.dataset.rating;
      } else if (sortValue === 'newest') {
        return b.dataset.date - a.dataset.date;
      }
    });

    cars.forEach(car => carList.appendChild(car));
  });

  // Location search (simplified version - in a real app, use a geocoding service like OpenStreetMap's Nominatim)
  const locationInput = document.getElementById('location-search');

  locationInput.addEventListener('blur', function() {
    // Simulate geocoding - in a real app, call a geocoding API
    if (this.value) {
      // Example coordinates for Ho Chi Minh City
      document.getElementById('latitude').value = '10.762622';
      document.getElementById('longitude').value = '106.660172';
    } else {
      document.getElementById('latitude').value = '';
      document.getElementById('longitude').value = '';
    }
  });
});
</script>

<?php include 'views/shared/footer.php'; ?>