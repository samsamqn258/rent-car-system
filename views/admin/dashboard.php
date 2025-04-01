<?php include 'views/shared/header.php'; ?>

<div class="container-fluid mt-4">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
      <div class="position-sticky pt-3">
        <ul class="nav flex-column">
          <li class="nav-item">
            <a class="nav-link active" href="<?php echo BASE_URL; ?>/admin/dashboard">
              <i class="fas fa-tachometer-alt me-2"></i> Bảng điều khiển
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/users">
              <i class="fas fa-users me-2"></i> Quản lý người dùng
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/cars">
              <i class="fas fa-car me-2"></i> Quản lý xe
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/promotions">
              <i class="fas fa-tags me-2"></i> Quản lý khuyến mãi
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/statistics?period=week">
              <i class="fas fa-chart-bar me-2"></i> Thống kê doanh thu
            </a>
          </li>
        </ul>
      </div>
    </div>

    <!-- Main content -->
    <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
      <div
        class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Bảng điều khiển</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">
              <i class="fas fa-download me-1"></i> Xuất báo cáo
            </button>
          </div>
          <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="periodDropdown"
              data-bs-toggle="dropdown" aria-expanded="false">
              <i class="far fa-calendar-alt me-1"></i> Khoảng thời gian
            </button>
            <ul class="dropdown-menu" aria-labelledby="periodDropdown">
              <li><a class="dropdown-item" href="#">Hôm nay</a></li>
              <li><a class="dropdown-item" href="#">Tuần này</a></li>
              <li><a class="dropdown-item" href="#">Tháng này</a></li>
              <li><a class="dropdown-item" href="#">Năm nay</a></li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Quick Stats -->
      <div class="row mb-4">
        <div class="col-md-6 col-lg-3 mb-3">
          <div class="card bg-primary text-white h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="card-title">Tổng doanh thu</h6>
                  <h2 class="mb-0"><?php echo number_format($total_revenue, 0, ',', '.'); ?> VND</h2>
                </div>
                <i class="fas fa-money-bill-wave fa-2x"></i>
              </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
              <a href="<?php echo BASE_URL; ?>/admin/statistics?period=week" class="text-white">Xem chi tiết</a>
              <i class="fas fa-arrow-right text-white"></i>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
          <div class="card bg-info text-white h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="card-title">Tổng số xe</h6>
                  <h2 class="mb-0"><?php echo $total_cars; ?></h2>
                </div>
                <i class="fas fa-car fa-2x"></i>
              </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
              <a href="<?php echo BASE_URL; ?>/admin/cars" class="text-white">Xem chi tiết</a>
              <i class="fas fa-arrow-right text-white"></i>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
          <div class="card bg-success text-white h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="card-title">Tổng số người dùng</h6>
                  <h2 class="mb-0"><?php echo $total_users; ?></h2>
                </div>
                <i class="fas fa-users fa-2x"></i>
              </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
              <a href="<?php echo BASE_URL; ?>/admin/users" class="text-white">Xem chi tiết</a>
              <i class="fas fa-arrow-right text-white"></i>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
          <div class="card bg-warning text-dark h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="card-title">Đơn thuê xe</h6>
                  <h2 class="mb-0"><?php echo $total_bookings; ?></h2>
                </div>
                <i class="fas fa-file-invoice fa-2x"></i>
              </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
              <a href="#" class="text-dark">Xem chi tiết</a>
              <i class="fas fa-arrow-right text-dark"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Revenue Chart -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h5 class="card-title">Doanh thu theo tháng</h5>
            </div>
            <div class="card-body">
              <canvas id="revenueChart" height="250"></canvas>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <!-- Cars Awaiting Approval -->
        <div class="col-md-6 mb-4">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">Xe chờ duyệt</h5>
              <span class="badge bg-warning"><?php echo count($pending_cars); ?></span>
            </div>
            <div class="card-body p-0">
              <?php if (empty($pending_cars)): ?>
              <div class="p-3 text-center text-muted">
                Không có xe nào đang chờ duyệt.
              </div>
              <?php else: ?>
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Xe</th>
                      <th>Chủ xe</th>
                      <th>Ngày đăng</th>
                      <th>Hành động</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($pending_cars as $car): ?>
                    <tr>
                      <td><?php echo $car['id']; ?></td>
                      <td><?php echo $car['brand'] . ' ' . $car['model']; ?></td>
                      <td><?php echo $car['owner_name']; ?></td>
                      <td><?php echo date('d/m/Y', strtotime($car['created_at'])); ?></td>
                      <td>
                        <a href="<?php echo BASE_URL; ?>/cars/details/<?php echo $car['id']; ?>"
                          class="btn btn-sm btn-info" target="_blank">
                          <i class="fas fa-eye"></i>
                        </a>
                        <a href="<?php echo BASE_URL; ?>/cars/approve/<?php echo $car['id']; ?>"
                          class="btn btn-sm btn-success">
                          <i class="fas fa-check"></i>
                        </a>
                        <a href="<?php echo BASE_URL; ?>/cars/reject/<?php echo $car['id']; ?>"
                          class="btn btn-sm btn-danger">
                          <i class="fas fa-times"></i>
                        </a>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
              <?php endif; ?>
            </div>
            <div class="card-footer text-center">
              <a href="<?php echo BASE_URL; ?>/admin/cars" class="text-decoration-none">Xem tất cả</a>
            </div>
          </div>
        </div>

        <!-- Recent Users -->
        <div class="col-md-6 mb-4">
          <div class="card">
            <div class="card-header">
              <h5 class="card-title mb-0">Người dùng mới đăng ký</h5>
            </div>
            <div class="card-body p-0">
              <?php if (empty($recent_users)): ?>
              <div class="p-3 text-center text-muted">
                Không có người dùng mới đăng ký gần đây.
              </div>
              <?php else: ?>
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Tên</th>
                      <th>Email</th>
                      <th>Vai trò</th>
                      <th>Ngày đăng ký</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($recent_users as $user): ?>
                    <tr>
                      <td><?php echo $user['id']; ?></td>
                      <td><?php echo $user['fullname']; ?></td>
                      <td><?php echo $user['email']; ?></td>
                      <td>
                        <?php if ($user['role'] == 'admin'): ?>
                        <span class="badge bg-danger">Admin</span>
                        <?php elseif ($user['role'] == 'owner'): ?>
                        <span class="badge bg-primary">Chủ xe</span>
                        <?php else: ?>
                        <span class="badge bg-secondary">Người dùng</span>
                        <?php endif; ?>
                      </td>
                      <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
              <?php endif; ?>
            </div>
            <div class="card-footer text-center">
              <a href="<?php echo BASE_URL; ?>/admin/users" class="text-decoration-none">Xem tất cả</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Revenue Chart
  var ctx = document.getElementById('revenueChart').getContext('2d');
  var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
      datasets: [{
        label: 'Doanh thu (VND)',
        data: <?php echo json_encode(array_values($monthly_revenue)); ?>,
        backgroundColor: 'rgba(54, 162, 235, 0.5)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return value.toLocaleString('vi-VN') + ' VND';
            }
          }
        }
      },
      plugins: {
        tooltip: {
          callbacks: {
            label: function(context) {
              return context.dataset.label + ': ' + context.raw.toLocaleString('vi-VN') + ' VND';
            }
          }
        }
      }
    }
  });
});
</script>

<?php include 'views/shared/footer.php'; ?>