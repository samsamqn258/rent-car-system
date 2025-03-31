<?php include 'views/shared/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Đăng ký tài khoản chủ xe</h3>
                </div>
                <div class="card-body">
                    <form action="<?php echo BASE_URL; ?>/auth/register_owner" method="post" id="registerOwnerForm" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3">Thông tin tài khoản</h5>
                                
                                <div class="mb-3">
                                    <label for="username" class="form-label">Tên đăng nhập</label>
                                    <input type="text" class="form-control" id="username" name="username" required
                                        value="<?php echo isset($_SESSION['form_data']['username']) ? $_SESSION['form_data']['username'] : ''; ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required
                                        value="<?php echo isset($_SESSION['form_data']['email']) ? $_SESSION['form_data']['email'] : ''; ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mật khẩu</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password" required minlength="6">
                                        <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">Mật khẩu phải có ít nhất 6 ký tự.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password_confirm" class="form-label">Xác nhận mật khẩu</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" required minlength="6">
                                        <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password_confirm">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h5 class="mb-3">Thông tin cá nhân</h5>
                                
                                <div class="mb-3">
                                    <label for="fullname" class="form-label">Họ và tên</label>
                                    <input type="text" class="form-control" id="fullname" name="fullname" required
                                        value="<?php echo isset($_SESSION['form_data']['fullname']) ? $_SESSION['form_data']['fullname'] : ''; ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Số điện thoại</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" required
                                        value="<?php echo isset($_SESSION['form_data']['phone']) ? $_SESSION['form_data']['phone'] : ''; ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="address" class="form-label">Địa chỉ</label>
                                    <textarea class="form-control" id="address" name="address" rows="3" required><?php echo isset($_SESSION['form_data']['address']) ? $_SESSION['form_data']['address'] : ''; ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h5 class="mb-3">Điều khoản và cam kết</h5>
                        
                        <div class="alert alert-info">
                            <h6 class="alert-heading">Thông tin quan trọng cho chủ xe</h6>
                            <p>Khi đăng ký làm chủ xe, bạn sẽ có thể:</p>
                            <ul>
                                <li>Đăng xe của bạn lên hệ thống cho người khác thuê</li>
                                <li>Quản lý lịch trình cho thuê và xem lịch sử giao dịch</li>
                                <li>Nhận thanh toán trực tiếp vào tài khoản của bạn</li>
                                <li>Được hỗ trợ kỹ thuật và hướng dẫn từ đội ngũ của chúng tôi</li>
                            </ul>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="owner_agreement" name="owner_agreement" required>
                            <label class="form-check-label" for="owner_agreement">
                                Tôi đã đọc và đồng ý với <a href="#" data-bs-toggle="modal" data-bs-target="#ownerTermsModal">Điều khoản dành cho chủ xe</a>
                            </label>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">
                                Tôi đồng ý với <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Điều khoản sử dụng</a> và <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal">Chính sách bảo mật</a>
                            </label>
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">Đăng ký làm chủ xe</button>
                        </div>
                    </form>
                    
                    <div class="mt-3 text-center">
                        <p>Đã có tài khoản? <a href="<?php echo BASE_URL; ?>/auth/login">Đăng nhập ngay</a></p>
                        <p>Chỉ muốn thuê xe? <a href="<?php echo BASE_URL; ?>/auth/register">Đăng ký tài khoản thường</a></p>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Lợi ích khi trở thành chủ xe</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="fas fa-money-bill-wave text-success me-2"></i> Tạo thu nhập thụ động</h5>
                            <p>Cho thuê xe khi bạn không sử dụng và tạo thêm nguồn thu nhập hàng tháng.</p>
                            
                            <h5><i class="fas fa-shield-alt text-primary me-2"></i> Bảo hiểm đầy đủ</h5>
                            <p>Xe của bạn được bảo vệ bởi chính sách bảo hiểm toàn diện trong thời gian cho thuê.</p>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fas fa-chart-line text-info me-2"></i> Quản lý dễ dàng</h5>
                            <p>Công cụ quản lý trực tuyến giúp theo dõi lịch trình, doanh thu và thống kê đơn giản.</p>
                            
                            <h5><i class="fas fa-headset text-warning me-2"></i> Hỗ trợ 24/7</h5>
                            <p>Đội ngũ hỗ trợ của chúng tôi luôn sẵn sàng giúp đỡ bạn khi cần thiết.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Owner Terms Modal -->
<div class="modal fade" id="ownerTermsModal" tabindex="-1" aria-labelledby="ownerTermsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ownerTermsModalLabel">Điều khoản dành cho chủ xe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5>1. Quy định về xe cho thuê</h5>
                <p>Khi đăng ký làm chủ xe trên hệ thống của chúng tôi, bạn đồng ý rằng:</p>
                <ul>
                    <li>Xe của bạn phải đáp ứng các tiêu chuẩn an toàn và được bảo dưỡng thường xuyên.</li>
                    <li>Xe phải có đầy đủ giấy tờ hợp lệ, đăng kiểm còn hạn và bảo hiểm theo quy định pháp luật.</li>
                    <li>Bạn phải cung cấp thông tin chính xác về xe, bao gồm hình ảnh, mô tả và tình trạng hiện tại.</li>
                    <li>Bạn chịu trách nhiệm về tính hợp pháp và quyền sở hữu đối với xe đăng ký cho thuê.</li>
                </ul>
                
                <h5>2. Hợp đồng và phí dịch vụ</h5>
                <p>Khi tham gia làm chủ xe, bạn đồng ý với các điều khoản sau:</p>
                <ul>
                    <li>Ký kết hợp đồng cho thuê xe có thời hạn 1 năm, có thể gia hạn tự động.</li>
                    <li>Thanh toán phí dịch vụ hàng năm theo quy định của chúng tôi.</li>
                    <li>Nền tảng sẽ thu phí hoa hồng 15% trên mỗi giao dịch thuê xe thành công.</li>
                    <li>Các khoản phí có thể thay đổi và sẽ được thông báo trước ít nhất 30 ngày.</li>
                </ul>
                
                <h5>3. Trách nhiệm của chủ xe</h5>
                <p>Khi trở thành chủ xe, bạn cam kết:</p>
                <ul>
                    <li>Đảm bảo xe sạch sẽ, đầy nhiên liệu và sẵn sàng cho khách thuê theo lịch đã đặt.</li>
                    <li>Cung cấp đầy đủ hướng dẫn sử dụng xe cho khách thuê.</li>
                    <li>Giải quyết các vấn đề phát sinh một cách kịp thời và chuyên nghiệp.</li>
                    <li>Không hủy đơn đặt xe đã xác nhận trừ trường hợp bất khả kháng.</li>
                </ul>
                
                <h5>4. Quy trình giải quyết tranh chấp</h5>
                <p>Trong trường hợp có tranh chấp với khách thuê:</p>
                <ul>
                    <li>Chúng tôi sẽ đóng vai trò trung gian để giải quyết các tranh chấp.</li>
                    <li>Bạn đồng ý tuân theo quy trình giải quyết tranh chấp của chúng tôi.</li>
                    <li>Trong trường hợp cần thiết, chúng tôi có quyền đưa ra quyết định cuối cùng.</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Đã hiểu</button>
            </div>
        </div>
    </div>
</div>

<!-- Terms and Privacy Modals - same as in register.php -->
<!-- Terms Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <!-- Same content as in register.php -->
</div>

<!-- Privacy Modal -->
<div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
    <!-- Same content as in register.php -->
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
    
    // Form validation
    const form = document.getElementById('registerOwnerForm');
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirm');
    
    form.addEventListener('submit', function(e) {
        if (password.value !== passwordConfirm.value) {
            e.preventDefault();
            alert('Mật khẩu xác nhận không khớp với mật khẩu!');
        }
    });
});
</script>

<?php
// Clear form data from session
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}
?>

<?php include 'views/shared/footer.php'; ?>