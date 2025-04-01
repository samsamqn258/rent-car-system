<?php
require_once 'models/User.php';
require_once 'utils/Validator.php';

class UserController {
    private $db;
    private $user;
    private $validator;

    public function __construct($db) {
        $this->db = $db;
        $this->user = new User($db);
        $this->validator = new Validator();
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Bạn phải đăng nhập để truy cập trang này.";
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    // Show user profile
    public function showProfile() {
        // Get user details
        $this->user->id = $_SESSION['user_id'];
        $user = $this->user->read($_SESSION['user_id'])->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            $_SESSION['error'] = "Không tìm thấy thông tin người dùng.";
            header('Location: ' . BASE_URL);
            exit;
        }
        
        include 'views/user/profile.php';
    }

    // Update user profile
    public function updateProfile() {
        // Validate required fields
        $required_fields = ['fullname', 'phone', 'address', 'license'];
        $validation_errors = $this->validator->validateRequired($_POST, $required_fields);
        
        // Validate phone number
        if (!$this->validator->validatePhone($_POST['phone'])) {
            $validation_errors[] = "Số điện thoại không hợp lệ.";
        }
        
        if (!empty($validation_errors)) {
            $_SESSION['error'] = "Vui lòng sửa các lỗi sau: " . implode(', ', $validation_errors);
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/user/profile');
            exit;
        }
        
        // Update user details
        $this->user->id = $_SESSION['user_id'];
        $this->user->fullname = $_POST['fullname'];
        $this->user->phone = $_POST['phone'];
        $this->user->address = $_POST['address'];
        $this->user->license = $_POST['license'];
        
        if ($this->user->update()) {
            // Update session data
            $_SESSION['fullname'] = $this->user->fullname;
            
            $_SESSION['success'] = "Cập nhật thông tin thành công.";
        } else {
            $_SESSION['error'] = "Không thể cập nhật thông tin.";
        }
        
        header('Location: ' . BASE_URL . '/user/profile');
        exit;
    }

    // Show change password form
    public function showChangePasswordForm() {
        include 'views/user/change_password.php';
    }

    // Change password
    public function changePassword() {
        // Validate required fields
        $required_fields = ['current_password', 'new_password', 'confirm_password'];
        $validation_errors = $this->validator->validateRequired($_POST, $required_fields);
        
        // Validate password strength
        if (!$this->validator->validatePassword($_POST['new_password'])) {
            $validation_errors[] = "Mật khẩu mới phải có ít nhất 6 ký tự.";
        }
        
        // Validate password confirmation
        if ($_POST['new_password'] !== $_POST['confirm_password']) {
            $validation_errors[] = "Mật khẩu xác nhận không khớp.";
        }
        
        if (!empty($validation_errors)) {
            $_SESSION['error'] = "Vui lòng sửa các lỗi sau: " . implode(', ', $validation_errors);
            header('Location: ' . BASE_URL . '/user/change_password');
            exit;
        }
        
        // Verify current password
        $this->user->id = $_SESSION['user_id'];
        $user_data = $this->user->read($_SESSION['user_id'])->fetch(PDO::FETCH_ASSOC);
        
        if (!password_verify($_POST['current_password'], $user_data['password'])) {
            $_SESSION['error'] = "Mật khẩu hiện tại không đúng.";
            header('Location: ' . BASE_URL . '/user/change_password');
            exit;
        }
        
        // Update password
        $this->user->password = $_POST['new_password'];
        
        if ($this->user->updatePassword()) {
            $_SESSION['success'] = "Đổi mật khẩu thành công.";
            header('Location: ' . BASE_URL . '/user/profile');
        } else {
            $_SESSION['error'] = "Không thể đổi mật khẩu.";
            header('Location: ' . BASE_URL . '/user/change_password');
        }
        
        exit;
    }

    // Show user bookings
    public function showBookings() {
        // Get user bookings
        $booking = new Booking($this->db);
        $booking->user_id = $_SESSION['user_id'];
        $bookings_stmt = $booking->readByUser();
        
        $bookings = [];
        while ($row = $bookings_stmt->fetch(PDO::FETCH_ASSOC)) {
            $bookings[] = $row;
        }
        
        include 'views/user/bookings.php';
    }

    // Show user reviews
    public function showReviews() {
        // Get user reviews
        $review = new Review($this->db);
        $review->user_id = $_SESSION['user_id'];
        $reviews_stmt = $review->readByUser();
        
        $reviews = [];
        while ($row = $reviews_stmt->fetch(PDO::FETCH_ASSOC)) {
            $reviews[] = $row;
        }
        
        include 'views/user/reviews.php';
    }
}