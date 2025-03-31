<?php
require_once 'models/User.php';
require_once 'utils/Validator.php';

class AuthController {
    private $db;
    private $user;
    private $validator;

    public function __construct($db) {
        $this->db = $db;
        $this->user = new User($db);
        $this->validator = new Validator();
    }

    // Display login form
    public function showLoginForm() {
        include 'views/auth/login.php';
    }

    // Process login form
    public function login() {
        // Validate required fields
        $required_fields = ['username', 'password'];
        $validation_errors = $this->validator->validateRequired($_POST, $required_fields);
        
        if (!empty($validation_errors)) {
            $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin đăng nhập.";
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
        
        // Set user properties
        $this->user->username = $_POST['username'];
        $this->user->email = $_POST['username']; // Username can be email too
        $this->user->password = $_POST['password'];
        
        // Attempt login
        if ($this->user->login()) {
            // Set session variables
            $_SESSION['user_id'] = $this->user->id;
            $_SESSION['username'] = $this->user->username;
            $_SESSION['fullname'] = $this->user->fullname;
            $_SESSION['user_role'] = $this->user->role;
            
            // Redirect based on role
            if (isset($_SESSION['redirect_after_login'])) {
                $redirect = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);
                header('Location: ' . $redirect);
            } else {
                if ($this->user->role == 'admin') {
                    header('Location: ' . BASE_URL . '/admin/dashboard');
                } else if ($this->user->role == 'owner') {
                    header('Location: ' . BASE_URL . '/owner/cars');
                } else {
                    header('Location: ' . BASE_URL);
                }
            }
        } else {
            $_SESSION['error'] = "Tên đăng nhập hoặc mật khẩu không đúng.";
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/auth/login');
        }
        
        exit;
    }

    // Display registration form
    public function showRegisterForm() {
        include 'views/auth/register.php';
    }

    // Process registration form
    public function register() {
        // Validate required fields
        $required_fields = ['username', 'email', 'password', 'password_confirm', 'fullname', 'phone', 'address'];
        $validation_errors = $this->validator->validateRequired($_POST, $required_fields);
        
        // Validate email format
        if (!$this->validator->validateEmail($_POST['email'])) {
            $validation_errors[] = "Email không hợp lệ.";
        }
        
        // Validate password strength
        if (!$this->validator->validatePassword($_POST['password'])) {
            $validation_errors[] = "Mật khẩu phải có ít nhất 6 ký tự.";
        }
        
        // Validate password confirmation
        if ($_POST['password'] !== $_POST['password_confirm']) {
            $validation_errors[] = "Mật khẩu xác nhận không khớp.";
        }
        
        // Validate phone number
        if (!$this->validator->validatePhone($_POST['phone'])) {
            $validation_errors[] = "Số điện thoại không hợp lệ.";
        }
        
        if (!empty($validation_errors)) {
            $_SESSION['error'] = "Vui lòng sửa các lỗi sau: " . implode(', ', $validation_errors);
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/auth/register');
            exit;
        }
        
        // Check if username or email already exists
        $this->user->username = $_POST['username'];
        $this->user->email = $_POST['email'];
        
        if ($this->user->usernameOrEmailExists()) {
            $_SESSION['error'] = "Tên đăng nhập hoặc email đã tồn tại.";
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/auth/register');
            exit;
        }
        
        // Set user properties
        $this->user->password = $_POST['password'];
        $this->user->fullname = $_POST['fullname'];
        $this->user->phone = $_POST['phone'];
        $this->user->address = $_POST['address'];
        $this->user->role = 'regular';
        
        // Create user
        if ($this->user->create()) {
            $_SESSION['success'] = "Đăng ký thành công! Vui lòng đăng nhập.";
            header('Location: ' . BASE_URL . '/auth/login');
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra khi đăng ký. Vui lòng thử lại.";
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/auth/register');
        }
        
        exit;
    }

    // Display owner registration form
    public function showOwnerRegisterForm() {
        include 'views/auth/register_owner.php';
    }

    // Process owner registration form
    public function registerOwner() {
        // Validate required fields
        $required_fields = ['username', 'email', 'password', 'password_confirm', 'fullname', 'phone', 'address'];
        $validation_errors = $this->validator->validateRequired($_POST, $required_fields);
        
        // Validate email format
        if (!$this->validator->validateEmail($_POST['email'])) {
            $validation_errors[] = "Email không hợp lệ.";
        }
        
        // Validate password strength
        if (!$this->validator->validatePassword($_POST['password'])) {
            $validation_errors[] = "Mật khẩu phải có ít nhất 6 ký tự.";
        }
        
        // Validate password confirmation
        if ($_POST['password'] !== $_POST['password_confirm']) {
            $validation_errors[] = "Mật khẩu xác nhận không khớp.";
        }
        
        // Validate phone number
        if (!$this->validator->validatePhone($_POST['phone'])) {
            $validation_errors[] = "Số điện thoại không hợp lệ.";
        }
        
        if (!empty($validation_errors)) {
            $_SESSION['error'] = "Vui lòng sửa các lỗi sau: " . implode(', ', $validation_errors);
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/auth/register_owner');
            exit;
        }
        
        // Check if username or email already exists
        $this->user->username = $_POST['username'];
        $this->user->email = $_POST['email'];
        
        if ($this->user->usernameOrEmailExists()) {
            $_SESSION['error'] = "Tên đăng nhập hoặc email đã tồn tại.";
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/auth/register_owner');
            exit;
        }
        
        // Set user properties
        $this->user->password = $_POST['password'];
        $this->user->fullname = $_POST['fullname'];
        $this->user->phone = $_POST['phone'];
        $this->user->address = $_POST['address'];
        $this->user->role = 'owner';
        
        // Create user
        if ($this->user->create()) {
            // Create owner contract (in a real app)
            // $contract = new CarOwnerContract($this->db);
            // $contract->createContract($this->user->id);
            
            $_SESSION['success'] = "Đăng ký chủ xe thành công! Vui lòng đăng nhập.";
            header('Location: ' . BASE_URL . '/auth/login');
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra khi đăng ký. Vui lòng thử lại.";
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/auth/register_owner');
        }
        
        exit;
    }

    // Logout user
    public function logout() {
        // Unset all session variables
        $_SESSION = array();
        
        // Destroy the session
        session_destroy();
        
        // Redirect to home page
        header('Location: ' . BASE_URL);
        exit;
    }

    // Display forgot password form
    public function showForgotPasswordForm() {
        include 'views/auth/forgot_password.php';
    }

    // Process forgot password form
    public function forgotPassword() {
        // In a real app, implement password reset email functionality
        $_SESSION['success'] = "Nếu email tồn tại trong hệ thống, bạn sẽ nhận được hướng dẫn đặt lại mật khẩu.";
        header('Location: ' . BASE_URL . '/auth/login');
        exit;
    }

    // Display reset password form
    public function showResetPasswordForm($token) {
        // In a real app, validate token
        include 'views/auth/reset_password.php';
    }

    // Process reset password form
    public function resetPassword() {
        // In a real app, implement password reset functionality
        $_SESSION['success'] = "Mật khẩu đã được đặt lại thành công. Vui lòng đăng nhập.";
        header('Location: ' . BASE_URL . '/auth/login');
        exit;
    }
}