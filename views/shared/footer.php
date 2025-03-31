<!-- Footer -->
<footer class="bg-dark text-white mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="mb-3">Về chúng tôi</h5>
                    <p>Nền tảng thuê xe tự lái kết nối chủ xe và khách hàng, giúp bạn dễ dàng thuê xe ô tô tự lái với quy trình đơn giản, nhanh chóng và an toàn.</p>
                    <div class="social-links mt-3">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-md-2 mb-4 mb-md-0">
                    <h5 class="mb-3">Liên kết</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>" class="text-white">Trang chủ</a></li>
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>/cars/search" class="text-white">Tìm xe</a></li>
                        <li class="mb-2"><a href="#" class="text-white">Hướng dẫn thuê xe</a></li>
                        <li class="mb-2"><a href="#" class="text-white">Điều khoản sử dụng</a></li>
                        <li class="mb-2"><a href="#" class="text-white">Chính sách bảo mật</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4 mb-md-0">
                    <h5 class="mb-3">Đăng ký xe</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>/auth/register_owner" class="text-white">Đăng ký làm chủ xe</a></li>
                        <li class="mb-2"><a href="#" class="text-white">Quy trình đăng ký xe</a></li>
                        <li class="mb-2"><a href="#" class="text-white">Câu hỏi thường gặp</a></li>
                        <li class="mb-2"><a href="#" class="text-white">Chính sách chủ xe</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5 class="mb-3">Liên hệ</h5>
                    <ul class="list-unstyled contact-info">
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> 123 Đường XYZ, Quận 1, TP. HCM</li>
                        <li class="mb-2"><i class="fas fa-phone-alt me-2"></i> 0123 456 789</li>
                        <li class="mb-2"><i class="fas fa-envelope me-2"></i> contact@carservice.com</li>
                        <li class="mb-2"><i class="fas fa-clock me-2"></i> 8:00 - 20:00, Thứ Hai - Chủ Nhật</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 bg-secondary">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Tất cả quyền được bảo lưu.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <img src="<?php echo BASE_URL; ?>/public/images/payment-methods.png" alt="Payment Methods" height="30">
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="<?php echo BASE_URL; ?>/public/js/scripts.js"></script>
</body>
</html>