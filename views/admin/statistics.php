<?php include 'views/shared/header.php'; ?>

<div class="container-fluid mt-4">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
      <div class="position-sticky pt-3">
        <ul class="nav flex-column">
          <li class="nav-item">
            <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/dashboard">
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
            <a class="nav-link active" href="<?php echo BASE_URL; ?>/admin/statistics?period=week">
              <i class="fas fa-chart-bar me-2"></i> Thống kê doanh thu
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/contracts">
              <i class="fas fa-file-contract me-2"></i> Quản lý hợp đồng
            </a>
          </li>
        </ul>
      </div>
    </div>

    <!-- Main content -->
    <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
      <div
        class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Thống kê doanh thu</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="exportToCSV">
              <i class="fas fa-download me-1"></i> Xuất báo cáo
            </button>
          </div>
          <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="periodDropdown"
              data-bs-toggle="dropdown" aria-expanded="false">
              <i class="far fa-calendar-alt me-1"></i> Khoảng thời gian
            </button>
            <ul class="dropdown-menu" aria-labelledby="periodDropdown">
              <li><a class="dropdown-item <?php echo $period == 'day' ? 'active' : ''; ?>"
                  href="<?php echo BASE_URL; ?>/admin/statistics?period=day&year=<?php echo $year; ?>">Hôm nay</a></li>
              <li><a class="dropdown-item <?php echo $period == 'week' ? 'active' : ''; ?>"
                  href="<?php echo BASE_URL; ?>/admin/statistics?period=week&year=<?php echo $year; ?>">Tuần này</a>
              </li>
              <li><a class="dropdown-item <?php echo $period == 'year' ? 'active' : ''; ?>"
                  href="<?php echo BASE_URL; ?>/admin/statistics?period=year&year=<?php echo $year; ?>">Năm nay</a></li>
            </ul>
          </div>
          <div class="dropdown ms-2">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="yearDropdown"
              data-bs-toggle="dropdown" aria-expanded="false">
              <i class="far fa-calendar me-1"></i> <?php echo $year; ?>
            </button>
            <ul class="dropdown-menu" aria-labelledby="yearDropdown">
              <?php
              $current_year = date('Y');
              for ($y = $current_year; $y >= $current_year - 2; $y--) {
                echo '<li><a class="dropdown-item ' . ($year == $y ? 'active' : '') . '" href="' . BASE_URL . '/admin/statistics?period=' . $period . '&year=' . $y . '">' . $y . '</a></li>';
              }
              ?>
            </ul>
          </div>
        </div>
      </div>

      <!-- Revenue Summary Cards -->
      <div class="row mb-4">
        <div class="col-md-6 col-lg-3 mb-3">
          <div class="card bg-primary text-white h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="card-title">Tổng doanh thu</h6>
                  <h2 class="mb-0"><?php echo number_format($statistics['total_revenue'], 0, ',', '.'); ?> VND</h2>
                </div>
                <i class="fas fa-money-bill-wave fa-2x"></i>
              </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
              <span>Tổng hợp từ tất cả đơn thuê</span>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
          <div class="card bg-success text-white h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="card-title">Đơn thuê thành công</h6>
                  <h2 class="mb-0"><?php echo $statistics['completed_bookings']; ?></h2>
                </div>
                <i class="fas fa-check-circle fa-2x"></i>
              </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
              <span>Số đơn hoàn thành</span>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
          <div class="card bg-info text-white h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="card-title">Tổng đơn thuê</h6>
                  <h2 class="mb-0"><?php echo $statistics['total_bookings']; ?></h2>
                </div>
                <i class="fas fa-file-invoice fa-2x"></i>
              </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
              <span>Bao gồm cả đơn hủy</span>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
          <div class="card bg-warning text-dark h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="card-title">Tỷ lệ thành công</h6>
                  <h2 class="mb-0">
                    <?php echo $statistics['total_bookings'] > 0 ? round(($statistics['completed_bookings'] / $statistics['total_bookings']) * 100) : 0; ?>%
                  </h2>
                </div>
                <i class="fas fa-percentage fa-2x"></i>
              </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
              <span>Đơn hoàn thành / Tổng đơn</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Monthly Revenue Chart -->
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
          <h5 class="card-title">Doanh thu theo tháng - <?php echo $year; ?></h5>
          <button class="btn btn-sm btn-outline-secondary" id="downloadChart">
            <i class="fas fa-download me-1"></i> Tải biểu đồ
          </button>
        </div>
        <div class="card-body">
          <canvas id="revenueChart" height="300"></canvas>
        </div>
      </div>

      <!-- Car Revenue Table -->
      <div class="card">
        <div class="card-header d-flex justify-content-between">
          <h5 class="card-title">Doanh thu theo xe</h5>
          <div class="input-group" style="width: 300px;">
            <input type="text" class="form-control form-control-sm" id="searchCarTable" placeholder="Tìm kiếm xe...">
            <button class="btn btn-outline-secondary btn-sm" type="button">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover" id="carRevenueTable">
              <thead>
                <tr>
                  <th>Xe</th>
                  <th>Chủ xe</th>
                  <th>Số lượt thuê</th>
                  <th>Tổng ngày thuê</th>
                  <th>Doanh thu</th>
                  <th>Tỷ lệ</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($car_revenue)): ?>
                  <tr>
                    <td colspan="6" class="text-center">Không có dữ liệu doanh thu cho giai đoạn này.</td>
                  </tr>
                <?php else: ?>
                  <?php
                  $total_revenue = array_sum(array_column($car_revenue, 'revenue'));
                  foreach ($car_revenue as $revenue):
                  ?>
                    <tr class="search-row"
                      data-search="<?php echo strtolower($revenue['brand'] . ' ' . $revenue['model']); ?>">
                      <td>
                        <div class="d-flex align-items-center">
                          <img src="<?php echo BASE_URL . '/' . $revenue['car_image']; ?>"
                            alt="<?php echo $revenue['brand'] . ' ' . $revenue['model']; ?>" class="me-2 rounded"
                            style="width: 40px; height: 30px; object-fit: cover;">
                          <div>
                            <div><?php echo $revenue['brand'] . ' ' . $revenue['model']; ?></div>
                            <small
                              class="text-muted"><?php echo $revenue['car_type'] == 'electric' ? 'Xe điện' : ($revenue['car_type'] == 'gasoline' ? 'Xe xăng' : 'Xe dầu'); ?>,
                              <?php echo $revenue['seats']; ?> chỗ</small>
                          </div>
                        </div>
                      </td>
                      <td>Chủ xe #<?php echo $revenue['id']; ?></td>
                      <td><?php echo $revenue['bookings']; ?></td>
                      <td><?php echo $revenue['total_days']; ?></td>
                      <td><?php echo number_format($revenue['revenue'], 0, ',', '.'); ?> VND</td>
                      <td>
                        <?php
                        $percentage = $total_revenue > 0 ? ($revenue['revenue'] / $total_revenue) * 100 : 0;
                        ?>
                        <div class="d-flex align-items-center">
                          <div class="progress flex-grow-1 me-2" style="height: 8px;">
                            <div class="progress-bar bg-primary" role="progressbar"
                              style="width: <?php echo $percentage; ?>%" aria-valuenow="<?php echo $percentage; ?>"
                              aria-valuemin="0" aria-valuemax="100"></div>
                          </div>
                          <span><?php echo round($percentage, 1); ?>%</span>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Monthly Revenue Chart
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const months = ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9',
      'Tháng 10', 'Tháng 11', 'Tháng 12'
    ];
    const revenueData = <?php echo json_encode(array_values($monthly_revenue)); ?>;

    const chart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: months,
        datasets: [{
          label: 'Doanh thu (VND)',
          data: revenueData,
          backgroundColor: 'rgba(54, 162, 235, 0.5)',
          borderColor: 'rgba(54, 162, 235, 1)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
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

    // Download chart as image
    document.getElementById('downloadChart').addEventListener('click', function() {
      const canvas = document.getElementById('revenueChart');
      const image = canvas.toDataURL('image/png');
      const link = document.createElement('a');
      link.download = 'doanh-thu-thang-' + <?php echo $year; ?> + '.png';
      link.href = image;
      link.click();
    });

    // Search in car revenue table
    document.getElementById('searchCarTable').addEventListener('keyup', function() {
      const searchValue = this.value.toLowerCase();
      const rows = document.querySelectorAll('.search-row');

      rows.forEach(row => {
        const searchText = row.dataset.search;

        if (searchText.includes(searchValue)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });

    // Export to CSV
    document.getElementById('exportToCSV').addEventListener('click', function() {
      const table = document.getElementById('carRevenueTable');
      const rows = table.querySelectorAll('tr');

      let csv = [];
      for (let i = 0; i < rows.length; i++) {
        const row = [],
          cols = rows[i].querySelectorAll('td, th');

        for (let j = 0; j < cols.length; j++) {
          // Get text content, clean it up
          let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '').trim();

          // Escape double quotes and wrap with quotes
          data = data.replace(/"/g, '""');
          row.push('"' + data + '"');
        }

        csv.push(row.join(','));
      }

      const csvString = csv.join('\n');
      const a = document.createElement('a');
      a.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csvString);
      a.target = '_blank';
      a.download = 'doanh-thu-theo-xe-' + <?php echo $year; ?> + '.csv';
      a.click();
    });
  });
</script>

<?php include 'views/shared/footer.php'; ?>