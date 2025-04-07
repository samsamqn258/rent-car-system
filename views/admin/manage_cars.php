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
            <a class="nav-link active" href="<?php echo BASE_URL; ?>/admin/cars">
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
        <h1 class="h2">Quản lý xe</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="exportToCSV">
              <i class="fas fa-download me-1"></i> Xuất CSV
            </button>
          </div>
        </div>
      </div>

      <!-- Car Status Filter -->
      <div class="card mb-4">
        <div class="card-body">
          <div class="row">
            <div class="col-md-3 mb-2 mb-md-0">
              <select class="form-select" id="statusFilter">
                <option value="all">Tất cả trạng thái</option>
                <option value="approved">Đã duyệt</option>
                <option value="unapproved">Chờ duyệt</option>
                <option value="rejected">Đã từ chối</option>
                <option value="rented">Đang cho thuê</option>
                <option value="hidden">Ẩn</option>
              </select>
            </div>
            <div class="col-md-3 mb-2 mb-md-0">
              <select class="form-select" id="typeFilter">
                <option value="all">Tất cả loại xe</option>
                <option value="electric">Xe điện</option>
                <option value="gasoline">Xe xăng</option>
                <option value="diesel">Xe dầu</option>
              </select>
            </div>
            <div class="col-md-4 mb-2 mb-md-0">
              <input type="text" class="form-control" id="searchInput" placeholder="Tìm kiếm theo tên xe, chủ xe...">
            </div>
            <div class="col-md-2">
              <button class="btn btn-primary w-100" id="resetFilters">
                <i class="fas fa-sync-alt me-1"></i> Đặt lại
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Cars Table -->
      <div class="card">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped table-hover" id="carsTable">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Hình ảnh</th>
                  <th>Xe</th>
                  <th>Chủ xe</th>
                  <th>Giá thuê/ngày</th>
                  <th>Trạng thái</th>
                  <th>Ngày đăng</th>
                  <th>Hành động</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($cars as $car): ?>
                  <tr data-status="<?php echo $car['status']; ?>" data-type="<?php echo $car['car_type']; ?>"
                    data-search="<?php echo strtolower($car['brand'] . ' ' . $car['model'] . ' ' . $car['owner_name']); ?>">
                    <td><?php echo $car['id']; ?></td>
                    <td>
                      <?php if (!empty($car['primary_image'])): ?>
                        <img src="<?php echo BASE_URL . '/' . $car['primary_image']; ?>"
                          alt="<?php echo $car['brand'] . ' ' . $car['model']; ?>" class="img-thumbnail"
                          style="width: 60px; height: 40px; object-fit: cover;">
                      <?php else: ?>
                        <img src="<?php echo BASE_URL; ?>/public/images/no-image.jpg" alt="No Image" class="img-thumbnail"
                          style="width: 60px; height: 40px; object-fit: cover;">
                      <?php endif; ?>
                    </td>
                    <td>
                      <div><?php echo $car['brand'] . ' ' . $car['model'] . ' (' . $car['year'] . ')'; ?></div>
                      <small class="text-muted">
                        <?php echo $car['car_type'] == 'electric' ? 'Xe điện' : ($car['car_type'] == 'gasoline' ? 'Xe xăng' : 'Xe dầu'); ?>,
                        <?php echo $car['seats']; ?> chỗ
                      </small>
                    </td>
                    <td><?php echo $car['owner_name']; ?></td>
                    <td><?php echo number_format($car['price_per_day'], 0, ',', '.'); ?> VND</td>
                    <td>
                      <?php
                      switch ($car['status']) {
                        case 'approved':
                          echo '<span class="badge bg-success">Đã duyệt</span>';
                          break;
                        case 'unapproved':
                          echo '<span class="badge bg-warning text-dark">Chờ duyệt</span>';
                          break;
                        case 'rejected':
                          echo '<span class="badge bg-danger">Đã từ chối</span>';
                          break;
                        case 'rented':
                          echo '<span class="badge bg-primary">Đang cho thuê</span>';
                          break;
                        case 'hidden':
                          echo '<span class="badge bg-secondary">Ẩn</span>';
                          break;
                        default:
                          echo '<span class="badge bg-secondary">Không xác định</span>';
                      }
                      ?>
                    </td>
                    <td><?php echo date('d/m/Y', strtotime($car['created_at'])); ?></td>
                    <td>
                      <div class="btn-group">
                        <a href="<?php echo BASE_URL; ?>/cars/details/<?php echo $car['id']; ?>"
                          class="btn btn-sm btn-info" target="_blank">
                          <i class="fas fa-eye"></i>
                        </a>

                        <?php if ($car['status'] == 'unapproved'): ?>
                          <a href="<?php echo BASE_URL; ?>/cars/approve/<?php echo $car['id']; ?>"
                            class="btn btn-sm btn-success">
                            <i class="fas fa-check"></i>
                          </a>
                          <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                            data-bs-target="#rejectModal<?php echo $car['id']; ?>">
                            <i class="fas fa-times"></i>
                          </button>

                          <!-- Reject Modal -->
                          <div class="modal fade" id="rejectModal<?php echo $car['id']; ?>" tabindex="-1"
                            aria-labelledby="rejectModalLabel<?php echo $car['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="rejectModalLabel<?php echo $car['id']; ?>">Từ chối xe</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                                </div>
                                <form action="<?php echo BASE_URL; ?>/cars/reject/<?php echo $car['id']; ?>" method="post">
                                  <div class="modal-body">
                                    <div class="mb-3">
                                      <label for="rejection_reason" class="form-label">Lý do từ chối:</label>
                                      <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3"
                                        required></textarea>
                                    </div>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                    <button type="submit" class="btn btn-danger">Từ chối</button>
                                  </div>
                                </form>
                              </div>
                            </div>
                          </div>
                        <?php elseif ($car['status'] == 'approved' || $car['status'] == 'rented'): ?>
                          <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal"
                            data-bs-target="#hideModal<?php echo $car['id']; ?>">
                            <i class="fas fa-eye-slash"></i>
                          </button>

                          <!-- Hide Modal -->
                          <div class="modal fade" id="hideModal<?php echo $car['id']; ?>" tabindex="-1"
                            aria-labelledby="hideModalLabel<?php echo $car['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="hideModalLabel<?php echo $car['id']; ?>">Ẩn xe</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                  <p>Bạn có chắc chắn muốn ẩn xe "<?php echo $car['brand'] . ' ' . $car['model']; ?>" không?
                                  </p>
                                  <p class="text-muted">Xe sẽ không hiển thị trong kết quả tìm kiếm nhưng vẫn tồn tại trong
                                    hệ thống.</p>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                  <a href="<?php echo BASE_URL; ?>/cars/hide/<?php echo $car['id']; ?>"
                                    class="btn btn-primary">Ẩn xe</a>
                                </div>
                              </div>
                            </div>
                          </div>
                        <?php elseif ($car['status'] == 'hidden'): ?>
                          <a href="<?php echo BASE_URL; ?>/cars/unhide/<?php echo $car['id']; ?>"
                            class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i> Hiện
                          </a>
                        <?php endif; ?>
                      </div>
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

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Filtering functionality
    const statusFilter = document.getElementById('statusFilter');
    const typeFilter = document.getElementById('typeFilter');
    const searchInput = document.getElementById('searchInput');
    const resetButton = document.getElementById('resetFilters');
    const rows = document.querySelectorAll('#carsTable tbody tr');

    function applyFilters() {
      const statusValue = statusFilter.value;
      const typeValue = typeFilter.value;
      const searchValue = searchInput.value.toLowerCase();

      rows.forEach(row => {
        const status = row.dataset.status;
        const type = row.dataset.type;
        const searchText = row.dataset.search;

        const matchesStatus = statusValue === 'all' || status === statusValue;
        const matchesType = typeValue === 'all' || type === typeValue;
        const matchesSearch = searchValue === '' || searchText.includes(searchValue);

        if (matchesStatus && matchesType && matchesSearch) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }

    statusFilter.addEventListener('change', applyFilters);
    typeFilter.addEventListener('change', applyFilters);
    searchInput.addEventListener('input', applyFilters);

    resetButton.addEventListener('click', function() {
      statusFilter.value = 'all';
      typeFilter.value = 'all';
      searchInput.value = '';
      applyFilters();
    });

    // Export to CSV functionality
    document.getElementById('exportToCSV').addEventListener('click', function() {
      const table = document.getElementById('carsTable');
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
      a.download = 'cars_data.csv';
      a.click();
    });
  });
</script>

<?php include 'views/shared/footer.php'; ?>