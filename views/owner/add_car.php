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
          <a href="<?php echo BASE_URL; ?>/cars/add" class="list-group-item list-group-item-action active">
            <i class="fas fa-plus-circle me-2"></i> Đăng xe mới
          </a>
          <a href="<?php echo BASE_URL; ?>/owner/cars" class="list-group-item list-group-item-action">
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
        <div class="card-header bg-primary text-white">
          <h3 class="mb-0">Thêm xe mới</h3>
        </div>
        <div class="card-body">
          <form action="<?php echo BASE_URL; ?>/cars/add" method="post" enctype="multipart/form-data" id="addCarForm">
            <!-- Car Information -->
            <h5 class="mb-3">Thông tin cơ bản</h5>
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="brand" class="form-label">Hãng xe</label>
                <input type="text" class="form-control" id="brand" name="brand" required
                  value="<?php echo isset($_SESSION['form_data']['brand']) ? $_SESSION['form_data']['brand'] : ''; ?>">
              </div>
              <div class="col-md-6">
                <label for="model" class="form-label">Mẫu xe</label>
                <input type="text" class="form-control" id="model" name="model" required
                  value="<?php echo isset($_SESSION['form_data']['model']) ? $_SESSION['form_data']['model'] : ''; ?>">
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
                    $selected = (isset($_SESSION['form_data']['year']) && $_SESSION['form_data']['year'] == $y) ? 'selected' : '';
                    echo "<option value=\"$y\" $selected>$y</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="col-md-4">
                <label for="car_type" class="form-label">Loại xe</label>
                <select class="form-select" id="car_type" name="car_type" required>
                  <option value="">Chọn loại xe</option>
                  <option value="electric"
                    <?php echo (isset($_SESSION['form_data']['car_type']) && $_SESSION['form_data']['car_type'] == 'electric') ? 'selected' : ''; ?>>
                    Xe điện</option>
                  <option value="gasoline"
                    <?php echo (isset($_SESSION['form_data']['car_type']) && $_SESSION['form_data']['car_type'] == 'gasoline') ? 'selected' : ''; ?>>
                    Xe xăng</option>
                  <option value="diesel"
                    <?php echo (isset($_SESSION['form_data']['car_type']) && $_SESSION['form_data']['car_type'] == 'diesel') ? 'selected' : ''; ?>>
                    Xe dầu</option>
                </select>
              </div>
              <div class="col-md-4">
                <label for="seats" class="form-label">Số chỗ ngồi</label>
                <select class="form-select" id="seats" name="seats" required>
                  <option value="">Chọn số chỗ</option>
                  <?php
                  for ($s = 4; $s <= 16; $s++) {
                    if ($s % 2 == 0 || $s == 5 || $s == 7) {
                      $selected = (isset($_SESSION['form_data']['seats']) && $_SESSION['form_data']['seats'] == $s) ? 'selected' : '';
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
                  value="<?php echo isset($_SESSION['form_data']['price_per_day']) ? $_SESSION['form_data']['price_per_day'] : ''; ?>">
                <span class="input-group-text">VND/ngày</span>
              </div>
            </div>

            <div class="mb-3">
              <label for="description" class="form-label">Mô tả xe</label>
              <textarea class="form-control" id="description" name="description" rows="4"
                required><?php echo isset($_SESSION['form_data']['description']) ? $_SESSION['form_data']['description'] : ''; ?></textarea>
              <div class="form-text">Mô tả chi tiết về xe, tính năng, điều kiện thuê, v.v.</div>
            </div>

            <!-- Location -->
            <h5 class="mt-4 mb-3">Vị trí xe</h5>
            <div class="row">
              <div class="col-md-3">
                <label for="province" class="form-label">Tỉnh/Thành phố</label>
                <select class="form-select" id="province">
                  <option value="">Chọn Tỉnh/Thành phố</option>
                </select>
              </div>
              <div class="col-md-3">
                <label for="district" class="form-label">Quận/Huyện</label>
                <select class="form-select" id="district" disabled>
                  <option value="">Chọn Quận/Huyện</option>
                </select>
              </div>
              <div class="col-md-3">
                <label for="ward" class="form-label">Phường/Xã</label>
                <select class="form-select" id="ward" disabled>
                  <option value="">Chọn Phường/Xã</option>
                </select>
              </div>
              <div class="col-md-3">
                <label for="street" class="form-label">Tên đường</label>
                <select class="form-select" id="street" disabled>
                  <option value="">Chọn Tên Đường</option>
                </select>
              </div>
            </div>

            <!-- Ô ẩn để lưu địa chỉ -->
            <input type="hidden" id="address" name="address">

            <!-- Map for location selection -->
            <!-- Ẩn input để lưu tọa độ -->
            <input type="hidden" id="latitude" name="latitude">
            <input type="hidden" id="longitude" name="longitude">

            <!-- Images -->
            <h5 class="mt-4 mb-3">Hình ảnh xe</h5>
            <div class="mb-3">
              <label for="car_images" class="form-label">Tải lên hình ảnh xe (tối đa 5 hình)</label>
              <input type="file" class="form-control" id="car_images" name="car_images[]" accept="image/*" multiple
                required>
              <div class="form-text">
                Chọn 3-5 hình ảnh chất lượng cao của xe. Hình đầu tiên sẽ là hình chính.
                <br>Hỗ trợ định dạng: JPG, JPEG, PNG. Kích thước tối đa: 5MB mỗi ảnh.
              </div>
            </div>

            <div id="imagePreview" class="mb-3 row"></div>

            <div class="alert alert-info">
              <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i> Lưu ý quan trọng:</h6>
              <p class="mb-0">Xe của bạn sẽ được kiểm duyệt bởi quản trị viên trước khi hiển thị trên hệ thống. Thông
                thường quá trình này mất khoảng 24-48 giờ.</p>
            </div>

            <div class="d-grid gap-2 mt-4">
              <button type="submit" class="btn btn-primary btn-lg">Đăng xe</button>
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

          const primaryBadge = document.createElement('span');
          primaryBadge.className = i === 0 ? 'badge bg-primary mb-2' : 'badge bg-secondary mb-2';
          primaryBadge.textContent = i === 0 ? 'Hình ảnh chính' : 'Hình ảnh phụ';
          cardBody.appendChild(primaryBadge);

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

    // Form validation
    const form = document.getElementById('addCarForm');

    form.addEventListener('submit', function(e) {
      const imageInput = document.getElementById('car_images');

      if (imageInput.files.length === 0) {
        e.preventDefault();
        alert('Vui lòng tải lên ít nhất một hình ảnh của xe.');
      }
    });
  });
</script>
<!-- Chọn địa chỉ theo option -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const provinceSelect = document.getElementById('province');
    const districtSelect = document.getElementById('district');
    const wardSelect = document.getElementById('ward');
    const streetSelect = document.getElementById('street');
    const addressInput = document.getElementById('address');

    // API danh sách đường (Thay thế bằng API thực tế nếu có)
    const streetsByDistrict = {
      "760": ["Nguyễn Huệ", "Lê Lợi", "Đồng Khởi"], // Quận 1
      "765": ["Điện Biên Phủ", "Phan Đăng Lưu", "Xô Viết Nghệ Tĩnh"], // Quận Bình Thạnh
    };

    // Fetch danh sách tỉnh/thành phố
    fetch('https://provinces.open-api.vn/api/?depth=1')
      .then(response => response.json())
      .then(data => {
        data.forEach(province => {
          let option = document.createElement('option');
          option.value = province.code;
          option.textContent = province.name;
          provinceSelect.appendChild(option);
        });
      });

    // Khi chọn tỉnh/thành phố
    provinceSelect.addEventListener('change', function() {
      let provinceCode = this.value;
      resetSelect([districtSelect, wardSelect, streetSelect]);
      updateAddress();

      if (!provinceCode) return;

      fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`)
        .then(response => response.json())
        .then(data => {
          data.districts.forEach(district => {
            let option = document.createElement('option');
            option.value = district.code;
            option.textContent = district.name;
            districtSelect.appendChild(option);
          });
          districtSelect.disabled = false;
        });
    });

    // Khi chọn quận/huyện
    districtSelect.addEventListener('change', function() {
      let districtCode = this.value;
      resetSelect([wardSelect, streetSelect]);
      updateAddress();

      if (!districtCode) return;

      fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`)
        .then(response => response.json())
        .then(data => {
          data.wards.forEach(ward => {
            let option = document.createElement('option');
            option.value = ward.name;
            option.textContent = ward.name;
            wardSelect.appendChild(option);
          });
          wardSelect.disabled = false;
        });

      // Load danh sách đường (Nếu có API thực tế, thay thế đoạn này)
      if (streetsByDistrict[districtCode]) {
        streetsByDistrict[districtCode].forEach(street => {
          let option = document.createElement('option');
          option.value = street;
          option.textContent = street;
          streetSelect.appendChild(option);
        });
        streetSelect.disabled = false;
      }
    });

    // Khi chọn phường/xã hoặc tên đường
    wardSelect.addEventListener('change', updateAddress);
    streetSelect.addEventListener('change', updateAddress);

    // Hàm cập nhật địa chỉ và gọi API để lấy tọa độ
    function updateAddress() {
      let province = provinceSelect.options[provinceSelect.selectedIndex]?.text || '';
      let district = districtSelect.options[districtSelect.selectedIndex]?.text || '';
      let ward = wardSelect.value || '';
      let street = streetSelect.value || '';

      let address = [street, ward, district, province].filter(Boolean).join(', ');
      addressInput.value = address;

      // Cập nhật tọa độ nếu có
      if (address) {
        updateLocation(address);
      }
    }

    // Hàm gọi API để lấy tọa độ
    function updateLocation(address) {
      var apiUrl = 'https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(address);

      fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
          if (data.length > 0) {
            document.getElementById('latitude').value = data[0].lat;
            document.getElementById('longitude').value = data[0].lon;
          } else {
            alert('Không tìm thấy vị trí. Vui lòng nhập lại địa chỉ.');
          }
        })
        .catch(error => console.error('Lỗi:', error));
    }

    function resetSelect(selectElements) {
      selectElements.forEach(select => {
        select.innerHTML = '<option value="">Chọn</option>';
        select.disabled = true;
      });
    }


  });
</script>


<?php
// Clear form data from session
if (isset($_SESSION['form_data'])) {
  unset($_SESSION['form_data']);
}
?>

<?php include 'views/shared/footer.php'; ?>