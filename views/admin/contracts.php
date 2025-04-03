<?php include 'views/shared/header.php'; ?>
<div class="container-fluid mt-4">
    <div class="row">
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
                        <a class="nav-link active" href="<?php echo BASE_URL; ?>/admin/statistics">
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

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div
                class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Danh sách hợp đồng và chủ xe</h1>
            </div>

            <div class="card">
                <div class="card-body">
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success"><?php echo $_SESSION['success'];
                                                            unset($_SESSION['success']); ?></div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?php echo $_SESSION['error'];
                                                        unset($_SESSION['error']); ?></div>
                    <?php endif; ?>

                    <?php if (!empty($contracts)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th>ID Hợp đồng</th>
                                        <th>Tên Chủ Xe</th>
                                        <th>Ngày Bắt Đầu</th>
                                        <th>Ngày Kết Thúc</th>
                                        <th>Phí Hợp Đồng</th>
                                        <th>Thanh toán</th>
                                        <!-- <th>Đã Duyệt</th> -->
                                        <th>Ngày Tạo</th>
                                        <!-- <th>Ngày Cập Nhật</th>
                                        <th>Hành động</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($contracts as $contract): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($contract['id']); ?></td>
                                            <td><?php echo htmlspecialchars($contract['owner_name']); ?></td>
                                            <td><?php echo htmlspecialchars($contract['start_date']); ?></td>
                                            <td><?php echo htmlspecialchars($contract['end_date']); ?></td>
                                            <td><?php echo htmlspecialchars(number_format($contract['contract_fee'])); ?> VND</td>
                                            <td>
                                                <?php
                                                $status = htmlspecialchars($contract['status']);
                                                if ($status === 'pending_payment') {
                                                    echo '<span class="badge bg-warning text-dark">Chưa thanh toán</span>';
                                                } elseif ($status === 'paid') {
                                                    echo '<span class="badge bg-success">Đã thanh toán</span>';
                                                } elseif ($status === 'cancelled') {
                                                    echo '<span class="badge bg-danger">Đã hủy</span>';
                                                } elseif ($status === 'expired') {
                                                    echo '<span class="badge bg-secondary">Đã hết hạn</span>';
                                                } else {
                                                    echo htmlspecialchars($status);
                                                }
                                                ?>
                                            </td>
                                            <!-- <td>
                                                <?php echo $contract['approved'] ? '<span class="badge bg-success">Đã duyệt</span>' : '<span class="badge bg-secondary">Chưa duyệt</span>'; ?>
                                            </td> -->
                                            <td><?php echo htmlspecialchars($contract['created_at']); ?></td>
                                            <!-- <td><?php echo htmlspecialchars($contract['updated_at']); ?></td> -->
                                            <!-- <td>
                                                <?php if (!$contract['approved']): ?>
                                                    <form method="post" action="<?php echo BASE_URL; ?>/admin/approveContract">
                                                        <input type="hidden" name="contract_id" value="<?php echo htmlspecialchars($contract['id']); ?>">
                                                        <button type="submit" class="btn btn-success btn-sm">Duyệt</button>
                                                    </form>
                                                    <form method="post" action="<?php echo BASE_URL; ?>/admin/rejectContract">
                                                        <input type="hidden" name="contract_id" value="<?php echo htmlspecialchars($contract['id']); ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm mt-1">Từ chối</button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="text-muted">Đã xử lý</span>
                                                <?php endif; ?>
                                            </td> -->
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="lead">Không có hợp đồng nào được tìm thấy.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'views/shared/footer.php'; ?>