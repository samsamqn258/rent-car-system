<?php include 'views/shared/header.php'; ?>

<div class="container mt-4">
  <div class="row">
    <div class="col-md-3">
      <div class="card mb-4">
        <div class="card-header text-white" style="background-color: #5fcf86;">
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
          <a href="<?php echo BASE_URL; ?>/owner/revenue" class="list-group-item list-group-item-action">
            <i class="fas fa-chart-line me-2"></i> Doanh thu
          </a>
          <a href="<?php echo BASE_URL; ?>/owner/contracts" class="list-group-item list-group-item-action active">
            <i class="fas fa-file-contract me-2"></i> Quản lý hợp đồng
          </a>
        </div>
      </div>

    </div>

    <div class="col-md-9">
      <div class="card">
        <div class="card-header text-white" style="background-color: #5fcf86;">
          <h3 class="mb-0">Danh sách hợp đồng của bạn</h3>
        </div>
        <div class="card-body">
          <?php if (isset($_SESSION['success'])): ?>
          <div class="alert alert-success"><?php echo $_SESSION['success'];
                                                            unset($_SESSION['success']); ?></div>
          <?php endif; ?>
          <?php if (isset($_SESSION['error'])): ?>
          <div class="alert alert-danger"><?php echo $_SESSION['error'];
                                                        unset($_SESSION['error']); ?></div>
          <?php endif; ?>

          <?php if ($contracts && $contracts->rowCount() > 0): ?>
          <div class="table-responsive">
            <table class="table table-bordered table-striped">
              <thead class="thead-light">
                <tr>
                  <th>ID</th>
                  <th>Ngày bắt đầu</th>
                  <th>Ngày kết thúc</th>
                  <th>Phí hợp đồng</th>
                  <th>Trạng thái</th>

                </tr>
              </thead>
              <tbody>
                <?php while ($contract = $contracts->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                  <td><?php echo htmlspecialchars($contract['id']); ?></td>
                  <td><?php echo htmlspecialchars($contract['start_date']); ?></td>
                  <td><?php echo htmlspecialchars($contract['end_date']); ?></td>
                  <td><?php echo htmlspecialchars(number_format($contract['contract_fee'])); ?> VND</td>

                  <td>
                    <?php echo $contract['approved'] ? '<span class="badge bg-success">Đã duyệt</span>' : '<span class="badge bg-secondary">Chưa duyệt</span>'; ?>
                  </td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
            <div>
              <form action="<?php echo BASE_URL; ?>/owner/add_contract" method="post">
                <div class="d-grid gap-2">
                  <button type="submit" class="btn text-white btn-lg" style="background-color: #5fcf86;">
                    <i class="fas fa-file-contract me-2"></i> Tạo hợp đồng
                  </button>
                </div>
              </form>
            </div>
          </div>
          <?php else: ?>

          <div class="card-body">
            <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error'];
                                                                unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            <p class="lead">Bạn chưa có hợp đồng nào</p>

            <p>Nhấn nút bên dưới để tạo hợp đồng chủ xe mặc định. Hợp đồng này có thời hạn 1 năm kể từ ngày tạo và có
              phí cố định.</p>

            <form action="<?php echo BASE_URL; ?>/owner/add_contract" method="post">
              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success btn-lg">
                  <i class="fas fa-file-contract me-2"></i> Tạo hợp đồng
                </button>
              </div>
            </form>

            <div class="alert alert-info mt-4">
              <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i> Lưu ý:</h6>
              <p class="mb-0">Bạn chỉ có thể tạo một hợp đồng chủ xe khi chưa có hợp đồng nào còn hiệu lực hoặc sắp hết
                hạn trong vòng 1 tuần.</p>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'views/shared/footer.php'; ?>