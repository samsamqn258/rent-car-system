<?php include 'views/shared/header.php'; ?>

<div class="container mt-4">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3">
      <div class="card mb-4">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0">Tài khoản của bạn</h5>
        </div>
        <div class="list-group list-group-flush">
          <a href="<?php echo BASE_URL; ?>/user/profile" class="list-group-item list-group-item-action">
            <i class="fas fa-user me-2"></i> Thông tin cá nhân
          </a>
          <a href="<?php echo BASE_URL; ?>/user/bookings" class="list-group-item list-group-item-action">
            <i class="fas fa-history me-2"></i> Lịch sử thuê xe
          </a>
          <a href="<?php echo BASE_URL; ?>/user/change_password" class="list-group-item list-group-item-action active">
            <i class="fas fa-key me-2"></i> Đổi mật khẩu
          </a>
          <?php if ($_SESSION['user_role'] == 'owner'): ?>
          <div class="list-group-item list-group-item-secondary fw-bold">Quản lý chủ xe</div>
          <a href="<?php echo BASE_URL; ?>/owner/cars" class="list-group-item list-group-item-action">
            <i class="fas fa-car me-2"></i> Quản lý xe
          </a>
          <a href="<?php echo BASE_URL; ?>/owner/bookings" class="list-group-item list-group-item-action">
            <i class="fas fa-calendar-alt me-2"></i> Quản lý đơn thuê
          </a>
          <a href="<?php echo BASE_URL; ?>/owner/revenue?period=week" class="list-group-item list-group-item-action">
            <i class="fas fa-chart-line me-2"></i> Doanh thu
          </a>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="col-md-9">
      <div class="card">
        <div class="card-header bg-primary text-white">
          <h3 class="mb-0">Đổi mật khẩu</h3>
        </div>
        <div class="card-body">
          <form action="<?php echo BASE_URL; ?>/user/change_password" method="post" id="changePasswordForm">
            <div class="mb-3">
              <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
              <div class="input-group">
                <input type="password" class="form-control" id="current_password" name="current_password" required>
                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="current_password">
                  <i class="fas fa-eye"></i>
                </button>
              </div>
            </div>

            <div class="mb-3">
              <label for="new_password" class="form-label">Mật khẩu mới</label>
              <div class="input-group">
                <input type="password" class="form-control" id="new_password" name="new_password" required
                  minlength="6">
                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="new_password">
                  <i class="fas fa-eye"></i>
                </button>
              </div>
              <div class="form-text">Mật khẩu phải có ít nhất 6 ký tự.</div>
            </div>

            <div class="mb-3">
              <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới</label>
              <div class="input-group">
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required
                  minlength="6">
                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="confirm_password">
                  <i class="fas fa-eye"></i>
                </button>
              </div>
            </div>

            <div class="password-strength mb-3" id="passwordStrength">
              <div class="progress">
                <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0"
                  aria-valuemax="100"></div>
              </div>
              <div class="text-muted mt-1 small">Độ mạnh mật khẩu: <span id="strengthText">Chưa nhập</span></div>
            </div>

            <div class="d-grid gap-2 mt-4">
              <button type="submit" class="btn btn-primary">Đổi mật khẩu</button>
            </div>
          </form>
        </div>
      </div>

      <div class="card mt-4">
        <div class="card-header bg-info text-white">
          <h5 class="mb-0">Mẹo bảo mật tài khoản</h5>
        </div>
        <div class="card-body">
          <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex align-items-center">
              <i class="fas fa-check-circle text-success me-3"></i>
              <div>
                <strong>Sử dụng mật khẩu mạnh</strong>
                <p class="mb-0">Kết hợp chữ hoa, chữ thường, số và ký tự đặc biệt.</p>
              </div>
            </li>
            <li class="list-group-item d-flex align-items-center">
              <i class="fas fa-check-circle text-success me-3"></i>
              <div>
                <strong>Thay đổi mật khẩu định kỳ</strong>
                <p class="mb-0">Nên thay đổi mật khẩu ít nhất 3 tháng một lần.</p>
              </div>
            </li>
            <li class="list-group-item d-flex align-items-center">
              <i class="fas fa-check-circle text-success me-3"></i>
              <div>
                <strong>Không sử dụng cùng mật khẩu</strong>
                <p class="mb-0">Tránh sử dụng cùng mật khẩu với các tài khoản khác.</p>
              </div>
            </li>
            <li class="list-group-item d-flex align-items-center">
              <i class="fas fa-check-circle text-success me-3"></i>
              <div>
                <strong>Đăng xuất khi sử dụng thiết bị công cộng</strong>
                <p class="mb-0">Luôn đăng xuất khi sử dụng máy tính công cộng.</p>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Toggle password visibility
  const toggleButtons = document.querySelectorAll('.toggle-password');

  toggleButtons.forEach(button => {
    button.addEventListener('click', function() {
      const targetId = this.getAttribute('data-target');
      const input = document.getElementById(targetId);
      const icon = this.querySelector('i');

      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    });
  });

  // Password strength meter
  const newPassword = document.getElementById('new_password');
  const confirmPassword = document.getElementById('confirm_password');
  const progressBar = document.querySelector('#passwordStrength .progress-bar');
  const strengthText = document.getElementById('strengthText');

  newPassword.addEventListener('input', function() {
    const value = this.value;
    let strength = 0;

    // Length check
    if (value.length >= 6) {
      strength += 20;
    }

    // Contains lowercase letters
    if (/[a-z]/.test(value)) {
      strength += 20;
    }

    // Contains uppercase letters
    if (/[A-Z]/.test(value)) {
      strength += 20;
    }

    // Contains numbers
    if (/[0-9]/.test(value)) {
      strength += 20;
    }

    // Contains special characters
    if (/[^a-zA-Z0-9]/.test(value)) {
      strength += 20;
    }

    // Update progress bar
    progressBar.style.width = strength + '%';
    progressBar.setAttribute('aria-valuenow', strength);

    // Set color based on strength
    if (strength < 40) {
      progressBar.className = 'progress-bar bg-danger';
      strengthText.textContent = 'Yếu';
    } else if (strength < 80) {
      progressBar.className = 'progress-bar bg-warning';
      strengthText.textContent = 'Trung bình';
    } else {
      progressBar.className = 'progress-bar bg-success';
      strengthText.textContent = 'Mạnh';
    }
  });

  // Password confirmation validation
  const form = document.getElementById('changePasswordForm');

  form.addEventListener('submit', function(e) {
    if (newPassword.value !== confirmPassword.value) {
      e.preventDefault();
      alert('Mật khẩu xác nhận không khớp với mật khẩu mới!');
    }
  });
});
</script>

<?php include 'views/shared/footer.php'; ?>