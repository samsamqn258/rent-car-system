<?php include 'views/shared/header.php';
require_once __DIR__ . '/../../utils/OpenStreetMap.php';
?>

<div class="container mt-4">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3">
      <div class="card mb-4">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0">Quản lý chủ xe</h5>
        </div>
        <div class="list-group list-group-flush">
          <a href="<?php echo BASE_URL; ?>/cars/add" class="list-group-item list-group-item-action">
            <i class="fas fa-plus-circle me-2"></i> Đăng xe mới
          </a>
          <a href="<?php echo BASE_URL; ?>/owner/cars" class="list-group-item list-group-item-action active">
            <i class="fas fa-car me-2"></i> Quản lý xe
          </a>
          <a href="<?php echo BASE_URL; ?>/owner/bookings" class="list-group-item list-group-item-action">
            <i class="fas fa-calendar-alt me-2"></i> Quản lý đơn thuê
          </a>
          <a href="<?php echo BASE_URL; ?>/owner/revenue?period=week" class="list-group-item list-group-item-action">
            <i class="fas fa-chart-line me-2"></i> Doanh thu
          </a>
          <a href="<?php echo BASE_URL; ?>/owner/contracts" class="list-group-item list-group-item-action">
            <i class="fas fa-chart-line me-2"></i> Hợp đồng của tôi
          </a>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="col-md-9">
      <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
          <h3 class="mb-0">Chỉnh sửa thông tin xe</h3>
          <a href="<?php echo BASE_URL; ?>/owner/cars" class="btn btn-light btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Quay lại
          </a>
        </div>
        <div class="card-body">
          <form action="<?php echo BASE_URL; ?>/cars/edit/<?php echo $car_details['id']; ?>" method="post"
            enctype="multipart/form-data" id="editCarForm">
            <!-- Car Basic Information -->
            <h5 class="mb-3">Thông tin cơ bản</h5>
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="brand" class="form-label">Hãng xe</label>
                <input type="text" class="form-control" id="brand" name="brand" required
                  value="<?php echo isset($_SESSION['form_data']['brand']) ? $_SESSION['form_data']['brand'] : $car_details['brand']; ?>">
              </div>
              <div class="col-md-6">
                <label for="model" class="form-label">Mẫu xe</label>
                <input type="text" class="form-control" id="model" name="model" required
                  value="<?php echo isset($_SESSION['form_data']['model']) ? $_SESSION['form_data']['model'] : $car_details['model']; ?>">
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-4">
                <label for="year" class="form-label">Năm sản xuất</label>
                <select class="form-select" id="year" name="year" required>
                  <option value="">Chọn năm</option>
                  <?php
                  $current_year = date('Y');
                  for ($y = $current_year; $y >= $current_year - 20; $y--) {
                    $selected = '';
                    if (isset($_SESSION['form_data']['year'])) {
                      $selected = ($_SESSION['form_data']['year'] == $y) ? 'selected' : '';
                    } else {
                      $selected = ($car_details['year'] == $y) ? 'selected' : '';
                    }
                    echo "<option value=\"$y\" $selected>$y</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="col-md-4">
                <label for="car_type" class="form-label">Loại xe</label>
                <select class="form-select" id="car_type" name="car_type" required>
                  <option value="">Chọn loại xe</option>
                  <?php
                  $car_types = [
                    'electric' => 'Xe điện',
                    'gasoline' => 'Xe xăng',
                    'diesel' => 'Xe dầu'
                  ];

                  foreach ($car_types as $value => $label) {
                    $selected = '';
                    if (isset($_SESSION['form_data']['car_type'])) {
                      $selected = ($_SESSION['form_data']['car_type'] == $value) ? 'selected' : '';
                    } else {
                      $selected = ($car_details['car_type'] == $value) ? 'selected' : '';
                    }
                    echo "<option value=\"$value\" $selected>$label</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="col-md-4">
                <label for="seats" class="form-label">Số chỗ ngồi</label>
                <select class="form-select" id="seats" name="seats" required>
                  <option value="">Chọn số chỗ</option>
                  <?php
                  for ($s = 4; $s <= 16; $s++) {
                    if ($s % 2 == 0 || $s == 5 || $s == 7) {
                      $selected = '';
                      if (isset($_SESSION['form_data']['seats'])) {
                        $selected = ($_SESSION['form_data']['seats'] == $s) ? 'selected' : '';
                      } else {
                        $selected = ($car_details['seats'] == $s) ? 'selected' : '';
                      }
                      echo "<option value=\"$s\" $selected>$s chỗ</option>";
                    }
                  }
                  ?>
                </select>
              </div>
            </div>

            <div class="mb-3">
              <label for="price_per_day" class="form-label">Giá thuê mỗi ngày (VND)</label>
              <div class="input-group">
                <input type="number" class="form-control" id="price_per_day" name="price_per_day" min="100000"
                  step="10000" required
                  value="<?php echo isset($_SESSION['form_data']['price_per_day']) ? $_SESSION['form_data']['price_per_day'] : $car_details['price_per_day']; ?>">
                <span class="input-group-text">VND/ngày</span>
              </div>
            </div>

            <div class="mb-3">
              <label for="description" class="form-label">Mô tả xe</label>
              <textarea class="form-control" id="description" name="description" rows="4"
                required><?php echo isset($_SESSION['form_data']['description']) ? $_SESSION['form_data']['description'] : $car_details['description']; ?></textarea>
              <div class="form-text">Mô tả chi tiết về xe, tính năng, điều kiện thuê, v.v.</div>
            </div>

            <!-- Location -->
            <h5 class="mt-4 mb-3">Vị trí xe</h5>
            <div class="mb-3">
              <label for="address" class="form-label">Địa chỉ</label>
              <input type="text" class="form-control" id="address" name="address" required
                value="<?php echo isset($_SESSION['form_data']['address']) ? $_SESSION['form_data']['address'] : $car_details['address']; ?>">
            </div>

            <!-- Map for location selection -->
            <div class="mb-3">
              <label class="form-label">Chọn vị trí trên bản đồ</label>
              <?php
              // Use existing coordinates
              $lat = isset($_SESSION['form_data']['latitude']) ? $_SESSION['form_data']['latitude'] : $car_details['latitude'];
              $lng = isset($_SESSION['form_data']['longitude']) ? $_SESSION['form_data']['longitude'] : $car_details['longitude'];

              // Generate map with location picker
              echo OpenStreetMap::generateMapWithSearch($lat, $lng, 15);
              ?>
            </div>

            <!-- Current Images -->
            <h5 class="mt-4 mb-3">Hình ảnh hiện tại</h5>
            <div class="row mb-3">
              <?php if (!empty($car_details['images'])): ?>
                <?php foreach ($car_details['images'] as $index => $image): ?>
                  <div class="col-md-4 mb-3">
                    <div class="card">
                      <img src="<?php echo BASE_URL . '/' . $image['image_path']; ?>" class="card-img-top" alt="Car Image"
                        style="height: 180px; object-fit: cover;">
                      <div class="card-body">
                        <?php if ($image['is_primary']): ?>
                          <span class="badge bg-primary mb-2">Hình ảnh chính</span>
                        <?php else: ?>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="primary_image"
                              id="primary_<?php echo $image['id']; ?>" value="<?php echo $image['id']; ?>">
                            <label class="form-check-label" for="primary_<?php echo $image['id']; ?>">
                              Đặt làm hình ảnh chính
                            </label>
                          </div>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="col-12">
                  <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i> Không có hình ảnh nào cho xe này.
                  </div>
                </div>
              <?php endif; ?>
            </div>

            <!-- Upload New Images -->
            <h5 class="mt-4 mb-3">Tải lên hình ảnh mới (tùy chọn)</h5>
            <div class="mb-3">
              <label for="car_images" class="form-label">Chọn hình ảnh</label>
              <input type="file" class="form-control" id="car_images" name="car_images[]" accept="image/*" multiple>
              <div class="form-text">
                Bạn có thể tải lên thêm hình ảnh mới hoặc giữ nguyên hình ảnh hiện tại.
                <br>Hỗ trợ định dạng: JPG, JPEG, PNG. Kích thước tối đa: 5MB mỗi ảnh.
              </div>
            </div>

            <div id="imagePreview" class="mb-3 row"></div>

            <!-- Car Status Information -->
            <div class="alert alert-info mt-3">
              <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i> Trạng thái xe:</h6>
              <p class="mb-0">
                <?php
                switch ($car_details['status']) {
                  case 'approved':
                    echo '<span class="badge bg-success">Đã duyệt</span> Xe của bạn đang hiển thị trên hệ thống và có thể được thuê.';
                    break;
                  case 'unapproved':
                    echo '<span class="badge bg-warning text-dark">Chờ duyệt</span> Xe của bạn đang chờ quản trị viên xét duyệt.';
                    break;
                  case 'rejected':
                    echo '<span class="badge bg-danger">Bị từ chối</span> Xe của bạn đã bị từ chối. Vui lòng chỉnh sửa thông tin và gửi lại.';
                    break;
                  case 'rented':
                    echo '<span class="badge bg-primary">Đang cho thuê</span> Xe của bạn hiện đang được thuê.';
                    break;
                  default:
                    echo '<span class="badge bg-secondary">Không xác định</span>';
                }
                ?>
              </p>
            </div>

            <!-- Submit Button -->
            <div class="d-grid gap-2 mt-4">
              <button type="submit" class="btn btn-primary btn-lg">Cập nhật thông tin xe</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Image preview functionality
    const imageInput = document.getElementById('car_images');
    const imagePreview = document.getElementById('imagePreview');

    imageInput.addEventListener('change', function() {
      imagePreview.innerHTML = '';

      if (this.files.length > 5) {
        alert('Bạn chỉ có thể tải lên tối đa 5 hình ảnh.');
        this.value = '';
        return;
      }

      for (let i = 0; i < this.files.length; i++) {
        const file = this.files[i];

        // Check file size
        if (file.size > 5 * 1024 * 1024) {
          alert('Hình ảnh ' + file.name + ' vượt quá kích thước tối đa (5MB).');
          this.value = '';
          imagePreview.innerHTML = '';
          return;
        }

        // Check file type
        if (!file.type.match('image/(jpeg|jpg|png)')) {
          alert('Hình ảnh ' + file.name + ' không đúng định dạng. Chỉ hỗ trợ JPG, JPEG và PNG.');
          this.value = '';
          imagePreview.innerHTML = '';
          return;
        }

        // Create preview
        const col = document.createElement('div');
        col.className = 'col-md-4 mb-3';

        const card = document.createElement('div');
        card.className = 'card h-100';

        const reader = new FileReader();
        reader.onload = function(e) {
          const img = document.createElement('img');
          img.src = e.target.result;
          img.className = 'card-img-top';
          img.style.height = '180px';
          img.style.objectFit = 'cover';
          card.appendChild(img);

          const cardBody = document.createElement('div');
          cardBody.className = 'card-body';

          const fileName = document.createElement('p');
          fileName.className = 'card-text small text-muted mb-0';
          fileName.textContent = file.name;
          cardBody.appendChild(fileName);

          card.appendChild(cardBody);
        };

        reader.readAsDataURL(file);
        col.appendChild(card);
        imagePreview.appendChild(col);
      }
    });
  });
</script>

<?php
// Clear form data from session
if (isset($_SESSION['form_data'])) {
  unset($_SESSION['form_data']);
}
?>

<?php include 'views/shared/footer.php'; ?>