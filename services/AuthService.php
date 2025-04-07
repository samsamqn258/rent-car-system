<?php
require_once 'models/User.php';
require_once 'utils/Validator.php';

class AuthService
{
    private $db;
    private $user;
    private $validator;

    public function __construct($db)
    {
        $this->db = $db;
        $this->user = new User($db);
        $this->validator = new Validator();
    }

    /**
     * Register a new regular user
     * 
     * @param array $userData User registration data
     * @return array Result with success status and message
     */
    public function registerUser($userData)
    {
        // Validate required fields
        $required_fields = ['username', 'email', 'password', 'password_confirm', 'fullname', 'phone', 'address'];
        $validation_errors = $this->validator->validateRequired($userData, $required_fields);

        // Validate email format
        if (!$this->validator->validateEmail($userData['email'])) {
            $validation_errors[] = "Email không hợp lệ.";
        }

        // Validate password strength
        if (!$this->validator->validatePassword($userData['password'])) {
            $validation_errors[] = "Mật khẩu phải có ít nhất 6 ký tự.";
        }

        // Validate password confirmation
        if ($userData['password'] !== $userData['password_confirm']) {
            $validation_errors[] = "Mật khẩu xác nhận không khớp.";
        }

        // Validate phone number
        if (!$this->validator->validatePhone($userData['phone'])) {
            $validation_errors[] = "Số điện thoại không hợp lệ.";
        }

        if (!empty($validation_errors)) {
            return [
                'success' => false,
                'errors' => $validation_errors,
                'message' => "Vui lòng sửa các lỗi sau: " . implode(', ', $validation_errors)
            ];
        }

        // Check if username or email already exists
        $this->user->username = $userData['username'];
        $this->user->email = $userData['email'];

        if ($this->user->usernameOrEmailExists()) {
            return [
                'success' => false,
                'message' => "Tên đăng nhập hoặc email đã tồn tại."
            ];
        }

        // Set user properties
        $this->user->password = $userData['password'];
        $this->user->fullname = $userData['fullname'];
        $this->user->phone = $userData['phone'];
        $this->user->address = $userData['address'];
        $this->user->role = 'regular';

        // Create user
        if ($this->user->create()) {
            return [
                'success' => true,
                'message' => "Đăng ký thành công! Vui lòng đăng nhập.",
                'user_id' => $this->user->id
            ];
        } else {
            return [
                'success' => false,
                'message' => "Có lỗi xảy ra khi đăng ký. Vui lòng thử lại."
            ];
        }
    }

    /**
     * Register a new car owner
     * 
     * @param array $userData Owner registration data
     * @return array Result with success status and message
     */
    public function registerOwner($userData)
    {
        // Use the same validation logic as regular user registration
        $result = $this->registerUser($userData);

        // If validation fails, return the error
        if (!$result['success']) {
            return $result;
        }

        // If successful, update the user role to owner
        $this->user->id = $result['user_id'];
        $this->user->role = 'owner';

        // Read the user data
        $user_data = $this->user->read($this->user->id)->fetch(PDO::FETCH_ASSOC);

        if (!$user_data) {
            return [
                'success' => false,
                'message' => "Không tìm thấy thông tin người dùng sau khi đăng ký."
            ];
        }

        // Create owner contract (in a real app, we would add more logic here)
        // For now, we're just updating the user role
        if ($this->updateUserRole($this->user->id, 'owner')) {
            return [
                'success' => true,
                'message' => "Đăng ký chủ xe thành công! Vui lòng đăng nhập.",
                'user_id' => $this->user->id
            ];
        } else {
            return [
                'success' => false,
                'message' => "Đăng ký tài khoản thành công nhưng không thể cập nhật vai trò thành chủ xe."
            ];
        }
    }

    /**
     * Authenticate user login
     * 
     * @param string $username Username or email
     * @param string $password Password
     * @return array Result with success status, user data and message
     */
    public function login($username, $password)
    {
        // Validate required inputs
        if (empty($username) || empty($password)) {
            return [
                'success' => false,
                'message' => "Vui lòng nhập đầy đủ thông tin đăng nhập."
            ];
        }

        // Set user properties
        $this->user->username = $username;
        $this->user->email = $username; // Username can be email too
        $this->user->password = $password;

        // Attempt login
        if ($this->user->login()) {
            // Get user data for session
            $user_data = [
                'user_id' => $this->user->id,
                'username' => $this->user->username,
                'fullname' => $this->user->fullname,
                'email' => $this->user->email,
                'role' => $this->user->role,
                'status' => $this->user->status
            ];

            // Check if account is blocked
            if ($this->user->status === 'blocked') {
                return [
                    'success' => false,
                    'message' => "Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên."
                ];
            }

            return [
                'success' => true,
                'message' => "Đăng nhập thành công!",
                'user_data' => $user_data
            ];
        } else {
            return [
                'success' => false,
                'message' => "Tên đăng nhập hoặc mật khẩu không đúng."
            ];
        }
    }

    /**
     * Update user role
     * 
     * @param int $user_id User ID
     * @param string $role New role (regular, owner, admin)
     * @return bool Success status
     */
    public function updateUserRole($user_id, $role)
    {
        // This is a simple implementation
        // In a real application, you might want to add more checks
        $query = "UPDATE users SET role = :role WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':id', $user_id);

        return $stmt->execute();
    }

    /**
     * Change user password
     * 
     * @param int $user_id User ID
     * @param string $current_password Current password
     * @param string $new_password New password
     * @param string $confirm_password Confirm new password
     * @return array Result with success status and message
     */
    public function changePassword($user_id, $current_password, $new_password, $confirm_password)
    {
        // Validate inputs
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            return [
                'success' => false,
                'message' => "Vui lòng nhập đầy đủ thông tin."
            ];
        }

        // Validate password strength
        if (!$this->validator->validatePassword($new_password)) {
            return [
                'success' => false,
                'message' => "Mật khẩu mới phải có ít nhất 6 ký tự."
            ];
        }

        // Validate password confirmation
        if ($new_password !== $confirm_password) {
            return [
                'success' => false,
                'message' => "Mật khẩu xác nhận không khớp với mật khẩu mới."
            ];
        }

        // Verify current password
        $this->user->id = $user_id;
        $user_data = $this->user->read($user_id)->fetch(PDO::FETCH_ASSOC);

        if (!$user_data) {
            return [
                'success' => false,
                'message' => "Không tìm thấy thông tin người dùng."
            ];
        }

        if (!password_verify($current_password, $user_data['password'])) {
            return [
                'success' => false,
                'message' => "Mật khẩu hiện tại không đúng."
            ];
        }

        // Update password
        $this->user->password = $new_password;

        if ($this->user->updatePassword()) {
            return [
                'success' => true,
                'message' => "Đổi mật khẩu thành công."
            ];
        } else {
            return [
                'success' => false,
                'message' => "Không thể đổi mật khẩu. Vui lòng thử lại."
            ];
        }
    }

    /**
     * Request password reset
     * 
     * @param string $email User email
     * @return array Result with success status and message
     */
    public function forgotPassword($email)
    {
        // Validate email
        if (!$this->validator->validateEmail($email)) {
            return [
                'success' => false,
                'message' => "Email không hợp lệ."
            ];
        }

        // Check if email exists
        $query = "SELECT id, username FROM users WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            // For security reasons, we don't tell the user that the email doesn't exist
            return [
                'success' => true,
                'message' => "Nếu email tồn tại trong hệ thống, bạn sẽ nhận được hướng dẫn đặt lại mật khẩu."
            ];
        }

        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Store token in database (you would need a password_resets table for this)
        // This is a simplified version
        $query = "INSERT INTO password_resets (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user['id']);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expires_at', $expires);

        if ($stmt->execute()) {
            // Send email with reset link (in a real app)
            // For now, we just return success
            return [
                'success' => true,
                'message' => "Chúng tôi đã gửi hướng dẫn đặt lại mật khẩu vào email của bạn."
            ];
        } else {
            return [
                'success' => false,
                'message' => "Có lỗi xảy ra khi xử lý yêu cầu. Vui lòng thử lại."
            ];
        }
    }

    /**
     * Verify password reset token
     * 
     * @param string $token Reset token
     * @return array Result with success status, user data and message
     */
    public function verifyResetToken($token)
    {
        // Check if token exists and is valid
        $query = "SELECT user_id FROM password_resets WHERE token = :token AND expires_at > NOW()";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        $reset = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reset) {
            return [
                'success' => false,
                'message' => "Token không hợp lệ hoặc đã hết hạn."
            ];
        }

        // Token is valid, return user ID
        return [
            'success' => true,
            'user_id' => $reset['user_id']
        ];
    }

    /**
     * Reset user password
     * 
     * @param string $token Reset token
     * @param string $password New password
     * @param string $confirm_password Confirm new password
     * @return array Result with success status and message
     */
    public function resetPassword($token, $password, $confirm_password)
    {
        // Verify token
        $token_result = $this->verifyResetToken($token);

        if (!$token_result['success']) {
            return $token_result;
        }

        // Validate password
        if (!$this->validator->validatePassword($password)) {
            return [
                'success' => false,
                'message' => "Mật khẩu phải có ít nhất 6 ký tự."
            ];
        }

        // Validate password confirmation
        if ($password !== $confirm_password) {
            return [
                'success' => false,
                'message' => "Mật khẩu xác nhận không khớp."
            ];
        }

        // Update password
        $this->user->id = $token_result['user_id'];
        $this->user->password = $password;

        if ($this->user->updatePassword()) {
            // Delete used tokens
            $query = "DELETE FROM password_resets WHERE user_id = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $token_result['user_id']);
            $stmt->execute();

            return [
                'success' => true,
                'message' => "Mật khẩu đã được đặt lại thành công. Vui lòng đăng nhập."
            ];
        } else {
            return [
                'success' => false,
                'message' => "Không thể đặt lại mật khẩu. Vui lòng thử lại."
            ];
        }
    }
}
