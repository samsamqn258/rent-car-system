<?php include 'views/shared/header.php'; ?>

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
          <a href="<?php echo BASE_URL; ?>/owner/cars" class="list-group-item list-group-item-action">
            <i class="fas fa-car me-2"></i> Quản lý xe
          </a>
          <a href="<?php echo BASE_URL; ?>/owner/bookings" class="list-group-item list-group-item-action">
            <i class="fas fa-calendar-alt me-2"></i> Quản lý đơn thuê
          </a>
          <a href="<?php echo BASE_URL; ?>/owner/revenue?period=week"
            class="list-group-item list-group-item-action active">
            <i class="fas fa-chart-line me-2"></i> Doanh thu
          </a>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="col-md-9">
      <h2 class="mb-4">Thống kê doanh thu</h2>

      <!-- Period Filter -->
      <div class="card mb-4">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h5 class="card-title mb-0">Thời gian</h5>
            </div>
            <div class="d-flex">
              <select class="form-select me-2" id="periodFilter">
                <option value="day" selected <?php echo $period == 'day' ? 'selected' : ''; ?>>Hôm nay</option>
                <option value="week" <?php echo $period == 'week' ? 'selected' : ''; ?>>Tuần này</option>
                <option value="year" <?php echo $period == 'year' ? 'selected' : ''; ?>>Năm nay</option>
              </select>
              <select class="form-select" id="yearFilter">
                <?php
                $currentYear = date('Y');
                for ($y = $currentYear; $y >= $currentYear - 2; $y--) {
                  $selected = $year == $y ? 'selected' : '';
                  echo "<option value=\"$y\" $selected>$y</option>";
                }
                ?>
              </select>
            </div>
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

                  <h2 class="mb-0"><?php echo number_format($statistics['total_revenue'] ?? 0, 0, ',', '.'); ?> VND</h2>
                </div>
                <i class="fas fa-money-bill-wave fa-2x"></i>
              </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
              <span>Đã trừ phí dịch vụ</span>
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
              <span>Đã hoàn thành</span>
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
              <span>Tất cả đơn thuê</span>
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
        <div class="card-header">
          <h5 class="card-title">Doanh thu theo tháng</h5>
        </div>
        <div class="card-body">
          <canvas id="revenueChart" height="300"></canvas>
        </div>
      </div>

      <!-- Per Car Revenue -->
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Doanh thu theo xe</h5>
          <button class="btn btn-sm btn-outline-primary" id="exportCarRevenue">
            <i class="fas fa-download me-1"></i> Xuất báo cáo
          </button>
        </div>
        <div class="card-body">
          <?php if (empty($car_revenue)): ?>
            <div class="alert alert-info mb-0">
              <i class="fas fa-info-circle me-2"></i> Chưa có dữ liệu doanh thu cho các xe trong thời gian này.
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover" id="carRevenueTable">
                <thead class="table-light">
                  <tr>
                    <th>Xe</th>
                    <th>Số lượt thuê</th>
                    <th>Tổng ngày thuê</th>
                    <th>Doanh thu</th>
                    <th>Tỷ lệ</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $total_revenue = array_sum(array_column($car_revenue, 'revenue'));
                  foreach ($car_revenue as $revenue):
                  ?>
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          <img src="<?php echo BASE_URL . '/' . ($revenue['car_image'] ?? 'default_image.jpg'); ?>"
                            alt="<?php echo ($revenue['car_brand'] ?? 'Unknown') . ' ' . ($revenue['car_model'] ?? 'Unknown'); ?>"
                            class="me-2 rounded" style="width: 40px; height: 30px; object-fit: cover;">
                          <div>
                            <div>
                              <?php
                              echo ($revenue['brand'] ?? 'Unknown') . ' ' . ($revenue['model'] ?? 'Unknown');
                              ?>
                            </div>
                            <small
                              class="text-muted"><?php echo $revenue['car_type'] == 'electric' ? 'Xe điện' : ($revenue['car_type'] == 'gasoline' ? 'Xe xăng' : 'Xe dầu'); ?>,
                              <?php echo $revenue['seats']; ?> chỗ</small>
                          </div>
                        </div>
                      </td>
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
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>


    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Period filter change
    const periodFilter = document.getElementById('periodFilter');
    const yearFilter = document.getElementById('yearFilter');

    function updateFilters() {
      const period = periodFilter.value;
      const year = yearFilter.value;
      window.location.href = `<?php echo BASE_URL; ?>/owner/revenue?period=${period}&year=${year}`;
    }

    periodFilter.addEventListener('change', updateFilters);
    yearFilter.addEventListener('change', updateFilters);

    // Revenue Chart
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?php echo json_encode($monthly_stats['months']); ?>,
        datasets: [{
          label: 'Doanh thu (VND)',
          data: <?php echo json_encode($monthly_stats['revenue']); ?>,
          backgroundColor: 'rgba(13, 110, 253, 0.5)',
          borderColor: 'rgba(13, 110, 253, 1)',
          borderWidth: 1
        }, {
          label: 'Số đơn thuê',
          data: <?php echo json_encode($monthly_stats['bookings']); ?>,
          backgroundColor: 'rgba(25, 135, 84, 0.5)',
          borderColor: 'rgba(25, 135, 84, 1)',
          borderWidth: 1,
          type: 'line',
          yAxisID: 'y1'
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            title: {
              display: true,
              text: 'Doanh thu (VND)'
            },
            ticks: {
              callback: function(value) {
                return value.toLocaleString('vi-VN') + ' VND';
              }
            }
          },
          y1: {
            position: 'right',
            beginAtZero: true,
            title: {
              display: true,
              text: 'Số đơn thuê'
            },
            grid: {
              drawOnChartArea: false
            }
          }
        },
        plugins: {
          tooltip: {
            callbacks: {
              label: function(context) {
                let label = context.dataset.label || '';
                if (label) {
                  label += ': ';
                }
                if (context.datasetIndex === 0) {
                  label += context.raw.toLocaleString('vi-VN') + ' VND';
                } else {
                  label += context.raw;
                }
                return label;
              }
            }
          }
        }
      }
    });

    // Export car revenue data
    document.getElementById('exportCarRevenue').addEventListener('click', function() {
      const table = document.getElementById('carRevenueTable');
      if (!table) return;

      let csv = [];
      const rows = table.querySelectorAll('tr');

      for (let i = 0; i < rows.length; i++) {
        const row = [],
          cols = rows[i].querySelectorAll('td, th');

        for (let j = 0; j < cols.length; j++) {
          // Clean the text content to remove extra spaces and line breaks
          let data = cols[j].textContent.replace(/(\r\n|\n|\r)/gm, '').trim();

          // Escape double quotes
          data = data.replace(/"/g, '""');

          // Add the data to the row array, enclosed in double quotes
          row.push('"' + data + '"');
        }

        csv.push(row.join(','));
      }

      // Create CSV file
      const csvContent = 'data:text/csv;charset=utf-8,' + csv.join('\n');
      const encodedUri = encodeURI(csvContent);

      // Create download link and click it
      const link = document.createElement('a');
      link.setAttribute('href', encodedUri);
      link.setAttribute('download', 'doanh_thu_theo_xe.csv');
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    });
  });
</script>

<?php include 'views/shared/footer.php'; ?>