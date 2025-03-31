<?php include 'views/shared/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Đăng ký tài khoản</h3>
                </div>
                <div class="card-body">
                    <form action="<?php echo BASE_URL; ?>/auth/register" method="post" id="registerForm">
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
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" required>
                            <label class="form-check-label" for="terms">
                                Tôi đồng ý với <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Điều khoản sử dụng</a> và <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal">Chính sách bảo mật</a>
                            </label>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Đăng ký</button>
                        </div>
                    </form>
                    
                    <div class="mt-3 text-center">
                        <p>Đã có tài khoản? <a href="<?php echo BASE_URL; ?>/auth/login">Đăng nhập ngay</a></p>
                        <p>Bạn muốn trở thành chủ xe? <a href="<?php echo BASE_URL; ?>/auth/register_owner">Đăng ký tài khoản chủ xe</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Terms and Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Điều khoản sử dụng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5>1. Điều khoản sử dụng dịch vụ thuê xe</h5>
                <p>Khi sử dụng dịch vụ thuê xe của chúng tôi, bạn đồng ý tuân thủ các điều khoản và điều kiện sau đây:</p>
                <ul>
                    <li>Bạn phải cung cấp thông tin chính xác và đầy đủ khi đăng ký tài khoản.</li>
                    <li>Bạn phải có giấy phép lái xe hợp lệ và đủ điều kiện để lái xe theo quy định của pháp luật.</li>
                    <li>Bạn chịu trách nhiệm về mọi hoạt động diễn ra trong tài khoản của mình.</li>
                    <li>Bạn đồng ý không sử dụng dịch vụ cho mục đích bất hợp pháp hoặc trái với điều khoản này.</li>
                </ul>
                
                <h5>2. Đặt xe và thanh toán</h5>
                <p>Khi đặt xe qua hệ thống của chúng tôi, bạn đồng ý với các điều kiện sau:</p>
                <ul>
                    <li>Thanh toán đầy đủ số tiền thuê xe theo quy định.</li>
                    <li>Tuân thủ lịch trình đã đặt và trả xe đúng hạn.</li>
                    <li>Chịu trách nhiệm về tình trạng xe trong thời gian thuê.</li>
                    <li>Trong trường hợp hủy đặt xe, các chính sách hoàn tiền sẽ được áp dụng theo quy định của chúng tôi.</li>
                </ul>
                
                <h5>3. Trách nhiệm người dùng</h5>
                <p>Khi sử dụng dịch vụ, bạn có trách nhiệm:</p>
                <ul>
                    <li>Tuân thủ luật giao thông và quy định pháp luật hiện hành.</li>
                    <li>Giữ gìn và bảo quản xe trong thời gian thuê.</li>
                    <li>Thông báo ngay cho chúng tôi về bất kỳ sự cố hoặc vấn đề nào liên quan đến xe.</li>
                    <li>Không cho người khác sử dụng xe khi chưa được sự đồng ý của chủ xe.</li>
                </ul>
                
                <h5>4. Thay đổi điều khoản</h5>
                <p>Chúng tôi có quyền thay đổi các điều khoản này vào bất kỳ lúc nào. Bạn có trách nhiệm kiểm tra các điều khoản này định kỳ để nắm được những thay đổi. Việc tiếp tục sử dụng dịch vụ sau khi có sự thay đổi đồng nghĩa với việc bạn chấp nhận các điều khoản mới.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Đã hiểu</button>
            </div>
        </div>
    </div>
</div>

<!-- Privacy Policy Modal -->
<div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="privacyModalLabel">Chính sách bảo mật</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5>1. Thông tin chúng tôi thu thập</h5>
                <p>Chúng tôi thu thập các thông tin sau từ người dùng:</p>
                <ul>
                    <li>Thông tin cá nhân: tên, email, số điện thoại, địa chỉ, thông tin giấy phép lái xe.</li>
                    <li>Thông tin giao dịch: lịch sử đặt xe, thanh toán.</li>
                    <li>Thông tin thiết bị: địa chỉ IP, loại trình duyệt, thông tin thiết bị.</li>
                    <li>Vị trí địa lý (khi được sự cho phép của bạn).</li>
                </ul>
                
                <h5>2. Mục đích sử dụng thông tin</h5>
                <p>Chúng tôi sử dụng thông tin của bạn để:</p>
                <ul>
                    <li>Cung cấp và quản lý dịch vụ thuê xe.</li>
                    <li>Xác minh danh tính và đảm bảo an toàn trong giao dịch.</li>
                    <li>Liên lạc với bạn về đơn đặt xe, cập nhật dịch vụ và hỗ trợ khách hàng.</li>
                    <li>Cải thiện và phát triển dịch vụ của chúng tôi.</li>
                    <li>Gửi thông tin khuyến mãi và tiếp thị (nếu bạn đồng ý).</li>
                </ul>
                
                <h5>3. Bảo mật thông tin</h5>
                <p>Chúng tôi cam kết bảo vệ thông tin cá nhân của bạn bằng cách:</p>
                <ul>
                    <li>Sử dụng các biện pháp bảo mật kỹ thuật và vật lý phù hợp.</li>
                    <li>Giới hạn quyền truy cập vào thông tin cá nhân.</li>
                    <li>Không chia sẻ thông tin với bên thứ ba trừ khi được sự đồng ý của bạn hoặc theo yêu cầu pháp lý.</li>
                </ul>
                
                <h5>4. Quyền của người dùng</h5>
                <p>Bạn có quyền:</p>
                <ul>
                    <li>Truy cập và xem thông tin cá nhân của mình.</li>
                    <li>Yêu cầu cập nhật hoặc sửa đổi thông tin không chính xác.</li>
                    <li>Yêu cầu xóa thông tin cá nhân (trong phạm vi pháp luật cho phép).</li>
                    <li>Từ chối nhận thông tin tiếp thị.</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Đã hiểu</button>
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
    
    // Form validation
    const form = document.getElementById('registerForm');
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