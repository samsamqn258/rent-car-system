<?php
require_once 'models/User.php';
require_once 'models/Booking.php';
require_once 'models/Review.php';
require_once 'utils/Validator.php';

class UserService {
    private $db;
    private $user;
    private $validator;

    public function __construct($db) {
        $this->db = $db;
        $this->user = new User($db);
        $this->validator = new Validator();
    }

    /**
     * Get user details by ID
     * 
     * @param int $user_id User ID
     * @return array|bool User data array or false if not found
     */
    public function getUserById($user_id) {
        $result = $this->user->read($user_id);
        
        if ($result) {
            return $result->fetch(PDO::FETCH_ASSOC);
        }
        
        return false;
    }

    /**
     * Update user profile
     * 
     * @param int $user_id User ID
     * @param array $user_data User data to update
     * @return array Result with success status and message
     */
    public function updateProfile($user_id, $user_data) {
        // Validate required fields
        $required_fields = ['fullname', 'phone', 'address'];
        $validation_errors = $this->validator->validateRequired($user_data, $required_fields);
        
        // Validate phone number
        if (!$this->validator->validatePhone($user_data['phone'])) {
            $validation_errors[] = "Số điện thoại không hợp lệ.";
        }
        
        if (!empty($validation_errors)) {
            return [
                'success' => false,
                'message' => "Vui lòng sửa các lỗi sau: " . implode(', ', $validation_errors)
            ];
        }
        
        // Set user properties
        $this->user->id = $user_id;
        $this->user->fullname = $user_data['fullname'];
        $this->user->phone = $user_data['phone'];
        $this->user->address = $user_data['address'];
        
        // Update user
        if ($this->user->update()) {
            return [
                'success' => true,
                'message' => "Cập nhật thông tin thành công."
            ];
        } else {
            return [
                'success' => false,
                'message' => "Không thể cập nhật thông tin. Vui lòng thử lại."
            ];
        }
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
    public function changePassword($user_id, $current_password, $new_password, $confirm_password) {
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
     * Get user bookings
     * 
     * @param int $user_id User ID
     * @return array Bookings array
     */
    public function getUserBookings($user_id) {
        $booking = new Booking($this->db);
        $booking->user_id = $user_id;
        $bookings_stmt = $booking->readByUser();
        
        $bookings = [];
        while ($row = $bookings_stmt->fetch(PDO::FETCH_ASSOC)) {
            // Check if booking can be reviewed
            $booking->id = $row['id'];
            $row['can_be_reviewed'] = $booking->canBeReviewed();
            $bookings[] = $row;
        }
        
        return $bookings;
    }

    /**
     * Get user reviews
     * 
     * @param int $user_id User ID
     * @return array Reviews array
     */
    public function getUserReviews($user_id) {
        $review = new Review($this->db);
        $review->user_id = $user_id;
        $reviews_stmt = $review->readByUser();
        
        $reviews = [];
        while ($row = $reviews_stmt->fetch(PDO::FETCH_ASSOC)) {
            $reviews[] = $row;
        }
        
        return $reviews;
    }

    /**
     * Upgrade regular user to car owner
     * 
     * @param int $user_id User ID
     * @return array Result with success status and message
     */
    public function upgradeToOwner($user_id) {
        // Get user details
        $user_data = $this->getUserById($user_id);
        
        if (!$user_data) {
            return [
                'success' => false,
                'message' => "Không tìm thấy thông tin người dùng."
            ];
        }
        
        // Check if user is already an owner
        if ($user_data['role'] === 'owner') {
            return [
                'success' => false,
                'message' => "Người dùng đã là chủ xe."
            ];
        }
        
        // Update user role to owner
        $query = "UPDATE users SET role = 'owner' WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $user_id);
        
        if ($stmt->execute()) {
            // Create owner contract (in a real application, we would add more details)
            $contract_query = "INSERT INTO car_owner_contracts (owner_id, start_date, end_date, contract_fee, status) 
                              VALUES (:owner_id, :start_date, :end_date, :contract_fee, 'active')";
            
            $contract_stmt = $this->db->prepare($contract_query);
            
            $start_date = date('Y-m-d');
            $end_date = date('Y-m-d', strtotime('+1 year'));
            $contract_fee = 500000; // Example fee (500,000 VND)
            
            $contract_stmt->bindParam(':owner_id', $user_id);
            $contract_stmt->bindParam(':start_date', $start_date);
            $contract_stmt->bindParam(':end_date', $end_date);
            $contract_stmt->bindParam(':contract_fee', $contract_fee);
            
            $contract_stmt->execute();
            
            return [
                'success' => true,
                'message' => "Nâng cấp lên chủ xe thành công! Bạn có thể bắt đầu đăng xe ngay bây giờ."
            ];
        } else {
            return [
                'success' => false,
                'message' => "Không thể nâng cấp lên chủ xe. Vui lòng thử lại."
            ];
        }
    }

    /**
     * Get all users (for admin)
     * 
     * @param array $filters Optional filters
     * @return array Users array
     */
    public function getAllUsers($filters = []) {
        $query = "SELECT * FROM users";
        
        // Apply filters if provided
        $whereClause = [];
        $params = [];
        
        if (!empty($filters)) {
            if (isset($filters['role']) && $filters['role'] !== 'all') {
                $whereClause[] = "role = :role";
                $params[':role'] = $filters['role'];
            }
            
            if (isset($filters['status']) && $filters['status'] !== 'all') {
                $whereClause[] = "status = :status";
                $params[':status'] = $filters['status'];
            }
            
            if (isset($filters['search']) && !empty($filters['search'])) {
                $whereClause[] = "(username LIKE :search OR email LIKE :search OR fullname LIKE :search)";
                $params[':search'] = '%' . $filters['search'] . '%';
            }
            
            if (!empty($whereClause)) {
                $query .= " WHERE " . implode(" AND ", $whereClause);
            }
        }
        
        $query .= " ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        
        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = $row;
        }
        
        return $users;
    }

    /**
     * Block/unblock user (for admin)
     * 
     * @param int $user_id User ID
     * @param string $status New status ('active' or 'blocked')
     * @return array Result with success status and message
     */
    public function updateUserStatus($user_id, $status) {
        if (!in_array($status, ['active', 'blocked'])) {
            return [
                'success' => false,
                'message' => "Trạng thái không hợp lệ."
            ];
        }
        
        $this->user->id = $user_id;
        $this->user->status = $status;
        
        if ($this->user->updateStatus()) {
            return [
                'success' => true,
                'message' => $status === 'active' ? "Mở khóa người dùng thành công." : "Khóa người dùng thành công."
            ];
        } else {
            return [
                'success' => false,
                'message' => "Không thể cập nhật trạng thái người dùng. Vui lòng thử lại."
            ];
        }
    }

    /**
     * Get user statistics (for admin)
     * 
     * @return array Statistics
     */
    public function getUserStatistics() {
        $statistics = [];
        
        // Total users
        $query = "SELECT COUNT(*) as total FROM users";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $statistics['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Users by role
        $query = "SELECT role, COUNT(*) as count FROM users GROUP BY role";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        $statistics['users_by_role'] = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $statistics['users_by_role'][$row['role']] = $row['count'];
        }
        
        // Active vs blocked users
        $query = "SELECT status, COUNT(*) as count FROM users GROUP BY status";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        $statistics['users_by_status'] = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $statistics['users_by_status'][$row['status']] = $row['count'];
        }
        
        // New users in last 30 days
        $query = "SELECT COUNT(*) as count FROM users WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $statistics['new_users_30_days'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        return $statistics;
    }

    /**
     * Get user activity (bookings, reviews, etc.)
     * 
     * @param int $user_id User ID
     * @return array Activity data
     */
    public function getUserActivity($user_id) {
        $activity = [];
        
        // Get user bookings count
        $query = "SELECT COUNT(*) as count FROM bookings WHERE user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $activity['bookings_count'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Get user reviews count
        $query = "SELECT COUNT(*) as count FROM reviews WHERE user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $activity['reviews_count'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Get total spent
        $query = "SELECT SUM(total_price) as total FROM bookings WHERE user_id = :user_id AND booking_status IN ('confirmed', 'completed')";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $activity['total_spent'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?: 0;
        
        // Get cars owned (if user is an owner)
        $query = "SELECT COUNT(*) as count FROM cars WHERE owner_id = :owner_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':owner_id', $user_id);
        $stmt->execute();
        $activity['cars_owned'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Get rental income (if user is an owner)
        $query = "SELECT SUM(b.total_price) as total 
                  FROM bookings b 
                  JOIN cars c ON b.car_id = c.id 
                  WHERE c.owner_id = :owner_id AND b.booking_status IN ('confirmed', 'completed')";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':owner_id', $user_id);
        $stmt->execute();
        $activity['rental_income'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?: 0;
        
        return $activity;
    }
}