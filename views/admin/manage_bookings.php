<?php include 'views/shared/header.php'; ?>

<div class="container-fluid mt-4">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
      <div class="position-sticky pt-3">
        <ul class="nav flex-column">
          <li class="nav-item">
            <a class="nav-link " href="<?php echo BASE_URL; ?>/admin/dashboard">
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
            <a class="nav-link " href="<?php echo BASE_URL; ?>/admin/promotions">
              <i class="fas fa-tags me-2"></i> Quản lý khuyến mãi
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/statistics?period=week">
              <i class="fas fa-chart-bar me-2"></i> Thống kê doanh thu
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/contracts">
              <i class="fas fa-file-contract me-2"></i> Quản lý hợp đồng
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="<?php echo BASE_URL; ?>/admin/bookings">
              <i class="fas fa-users me-2"></i> Quản lý đơn thuê
            </a>
          </li>
        </ul>
      </div>
    </div>

    <!-- Main content -->
    <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
      <div
        class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Quản lý đơn thuê</h1>
      </div>

      <!-- Bookings Table -->
      <div class="card">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped table-hover" id="bookingsTable">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Khách hàng</th>
                  <th>Xe</th>
                  <th>Ngày thuê</th>
                  <th>Ngày trả</th>
                  <th>Trạng thái</th>

                </tr>
              </thead>
              <tbody>
                <?php foreach ($bookings as $booking): ?>
                <tr>
                  <td><?php echo $booking['id']; ?></td>
                  <td><?php echo $booking['owner_name']; ?></td>
                  <td><?php echo $booking['car_brand'] . ' ' . $booking['car_model']; ?></td>
                  <!-- Corrected the car details -->
                  <td><?php echo date('d/m/Y', strtotime($booking['start_date'])); ?></td>
                  <td><?php echo date('d/m/Y', strtotime($booking['end_date'])); ?></td>
                  <td>
                    <!-- Conditional display based on the status -->
                    <?php if ($booking['payment_status'] == 'paid'): ?>
                    <span class="badge bg-success">Đã thanh toán</span>
                    <?php elseif ($booking['payment_status'] == 'pending'): ?>
                    <span class="badge bg-warning">Chờ xử lý</span>
                    <?php else: ?>
                    <span class="badge bg-danger">Chưa thanh toán</span>
                    <?php endif; ?>
                  </td>

                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'views/shared/footer.php'; ?>